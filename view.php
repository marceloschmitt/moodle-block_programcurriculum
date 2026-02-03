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

require_login();

$courseid = required_param('courseid', PARAM_INT);
$course = get_course($courseid);

$context = context_course::instance($courseid);
require_capability('block/programcurriculum:viewprogress', $context);

$canviewall = has_capability('block/programcurriculum:viewallprogress', $context);
$userid = optional_param('userid', 0, PARAM_INT);

if ($userid && !$canviewall) {
    throw new required_capability_exception($context, 'block/programcurriculum:viewallprogress', 'nopermissions', '');
}

$curriculumrepo = new \block_programcurriculum\curriculum_repository();
$curricula = $curriculumrepo->get_all();

$curriculumid = optional_param('curriculumid', 0, PARAM_INT);
if (!$curriculumid && !empty($curricula)) {
    $curriculumid = (int)reset($curricula)->id;
}

$progressdata = [
    'curricula' => [],
    'hascurricula' => !empty($curricula),
    'progress' => null,
    'courseid' => $courseid,
    'userid' => $userid,
    'showstudentlist' => $canviewall && !$userid,
    'students' => [],
    'hasstudents' => false,
];

foreach ($curricula as $curriculum) {
    $progressdata['curricula'][] = [
        'id' => $curriculum->id,
        'name' => $curriculum->name,
        'selected' => ((int)$curriculumid === (int)$curriculum->id),
    ];
}

$mappingrepo = new \block_programcurriculum\mapping_repository();
$coursemappings = $mappingrepo->get_by_moodle_course($courseid);
$firstmapping = !empty($coursemappings) ? reset($coursemappings) : null;
$progressdata['programname'] = $firstmapping ? $firstmapping->programname : '';
$progressdata['externalcoursename'] = $firstmapping ? $firstmapping->externalcoursename : '';
$progressdata['hassubtitle'] = !empty($progressdata['programname']) && !empty($progressdata['externalcoursename']);

if ($canviewall && !$userid) {
    // Show list of students in the course.

    $namefields = 'u.id, u.firstname, u.lastname, u.firstnamephonetic, u.lastnamephonetic, u.middlename, u.alternatename';
    $users = get_enrolled_users($context, 'moodle/course:isincompletionreports', 0, $namefields, 'u.lastname ASC, u.firstname ASC');
    foreach ($users as $u) {
        $url = new moodle_url('/blocks/programcurriculum/view.php', [
            'courseid' => $courseid,
            'userid' => $u->id,
            'curriculumid' => $curriculumid,
        ]);
        $progressdata['students'][] = [
            'id' => $u->id,
            'fullname' => fullname($u),
            'viewurl' => $url->out(false),
        ];
    }
    usort($progressdata['students'], function ($a, $b) {
        return strcoll($a['fullname'], $b['fullname']);
    });
    $progressdata['hasstudents'] = !empty($progressdata['students']);
} else {
    if (!$userid) {
        $userid = $USER->id;
    }
    $progressdata['userid'] = $userid;
    $courseviewurl = new moodle_url('/blocks/programcurriculum/view.php', ['courseid' => $courseid, 'curriculumid' => $curriculumid]);
    $progressdata['courseviewurl'] = $courseviewurl->out(false);

    if ($curriculumid) {
        $calculator = new \block_programcurriculum\progress_calculator();
        $progress = $calculator->calculate_for_user($userid, $curriculumid);
        $progressdata['progress'] = [
            'percent' => $progress['percent'],
            'completed' => $progress['completed'],
            'total' => $progress['total'],
            'details' => array_map(function (array $detail): array {
                return [
                    'coursename' => $detail['coursename'],
                    'completed' => $detail['completed'],
                ];
            }, $progress['details']),
        ];
    }
}

$PAGE->set_context($context);
$PAGE->set_course($course);
$PAGE->set_pagelayout('incourse');
$PAGE->set_secondary_navigation(false);
$PAGE->set_url('/blocks/programcurriculum/view.php', [
    'courseid' => $courseid,
    'userid' => $userid,
    'curriculumid' => $curriculumid,
]);
$PAGE->set_title(get_string('viewtitle', 'block_programcurriculum'));
$PAGE->set_heading(get_string('progressview', 'block_programcurriculum'));
$PAGE->navbar->add($course->fullname, new moodle_url('/course/view.php', ['id' => $courseid]));
$PAGE->navbar->add(get_string('viewtitle', 'block_programcurriculum'));

echo $OUTPUT->header();
echo $OUTPUT->render_from_template('block_programcurriculum/progress', $progressdata);
echo $OUTPUT->footer();
