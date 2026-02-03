<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

require_once(__DIR__ . '/../../config.php');
require_once($CFG->libdir . '/completionlib.php');

global $DB;
require_login();

$courseid = required_param('courseid', PARAM_INT);
$userid = required_param('userid', PARAM_INT);
$course = get_course($courseid);

$context = context_course::instance($courseid);
require_capability('block/programcurriculum:viewprogress', $context);

$canviewall = has_capability('block/programcurriculum:viewallprogress', $context);
if ($userid != $USER->id && !$canviewall) {
    throw new required_capability_exception($context, 'block/programcurriculum:viewallprogress', 'nopermissions', '');
}

$curriculumid = required_param('curriculumid', PARAM_INT);
$curriculumrepo = new \block_programcurriculum\curriculum_repository();
$curriculum = $curriculumrepo->get($curriculumid);
if (!$curriculum || $curriculumid <= 0) {
    redirect(new moodle_url('/blocks/programcurriculum/view.php', ['courseid' => $courseid]));
}

$mappingrepo = new \block_programcurriculum\mapping_repository();
$coursemappings = $mappingrepo->get_by_moodle_course($courseid);
$firstmapping = !empty($coursemappings) ? reset($coursemappings) : null;

$data = [
    'courseid' => $courseid,
    'userid' => $userid,
    'curriculumid' => $curriculumid,
    'programname' => $firstmapping ? $firstmapping->programname : '',
    'externalcoursename' => $firstmapping ? $firstmapping->externalcoursename : '',
    'hassubtitle' => $firstmapping && !empty($firstmapping->programname) && !empty($firstmapping->externalcoursename),
    'courseviewurl' => (new moodle_url('/blocks/programcurriculum/view.php', [
        'courseid' => $courseid,
    ]))->out(false),
    'progress' => null,
    'sidebarcourses' => [],
];

$calculator = new \block_programcurriculum\progress_calculator();
$progress = $calculator->calculate_for_user($userid, $curriculumid);

$curriculumcourses = $mappingrepo->get_by_curriculum_with_details($curriculumid);
$grouped = [];
foreach ($curriculumcourses as $row) {
    $key = (int)$row->externalcourseid;
    if (!isset($grouped[$key])) {
        $grouped[$key] = [
            'externalcoursename' => $row->externalcoursename,
            'moodlecourses' => [],
        ];
    }
    $ctx = context_course::instance((int)$row->moodlecourseid);
    if (is_enrolled($ctx, $userid)) {
        $completed = $progress['details_by_course'][$row->moodlecourseid] ?? false;
        $grouped[$key]['moodlecourses'][] = [
            'name' => $row->moodlecoursename,
            'completed' => $completed,
        ];
    }
}

$data['courserows'] = array_values($grouped);
$data['progress'] = [
    'percent' => $progress['percent'],
    'completed' => $progress['completed'],
    'total' => $progress['total'],
];

$PAGE->set_context($context);
$PAGE->set_course($course);
$PAGE->set_pagelayout('incourse');
$PAGE->set_secondary_navigation(false);
$PAGE->set_url('/blocks/programcurriculum/progress.php', [
    'courseid' => $courseid,
    'userid' => $userid,
    'curriculumid' => $curriculumid,
]);
$user = $DB->get_record('user', ['id' => $userid], 'id, firstname, lastname, firstnamephonetic, lastnamephonetic, middlename, alternatename');
$studentname = $user ? fullname($user) : get_string('student', 'block_programcurriculum');

$PAGE->set_title(get_string('viewtitle', 'block_programcurriculum'));
$PAGE->set_heading($studentname);
$PAGE->navbar->add($course->fullname, new moodle_url('/course/view.php', ['id' => $courseid]));
$PAGE->navbar->add(get_string('viewtitle', 'block_programcurriculum'));

echo $OUTPUT->header();
echo $OUTPUT->render_from_template('block_programcurriculum/progress', $data);
echo $OUTPUT->footer();
