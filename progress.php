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
require_capability('block/programcurriculum:viewownprogress', $context);

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

$user = $DB->get_record('user', ['id' => $userid], 'id, firstname, lastname, firstnamephonetic, lastnamephonetic, middlename, alternatename');
$studentname = $user ? fullname($user) : get_string('student', 'block_programcurriculum');

$data = [
    'courseid' => $courseid,
    'userid' => $userid,
    'curriculumid' => $curriculumid,
    'programname' => $firstmapping ? $firstmapping->programname : '',
    'studentname' => $studentname,
    'hasprogramname' => $firstmapping && !empty($firstmapping->programname),
    'courseviewurl' => (new moodle_url('/blocks/programcurriculum/view.php', [
        'courseid' => $courseid,
    ]))->out(false),
    'progress' => null,
    'sidebarcourses' => [],
];

$calculator = new \block_programcurriculum\progress_calculator();
$progress = $calculator->calculate_for_user($userid, $curriculumid);

$curriculumcourses = $mappingrepo->get_by_curriculum_with_details($curriculumid);
$now = time();
$grouped = [];
foreach ($curriculumcourses as $row) {
    $key = (int)$row->externalcourseid;
    if (!isset($grouped[$key])) {
        $grouped[$key] = [
            'term' => (int)($row->term ?? 1),
            'externalcoursename' => $row->externalcoursename,
            'moodlecourses' => [],
            'has_active' => false,
        ];
    }
    $ctx = context_course::instance((int)$row->moodlecourseid);
    if (is_enrolled($ctx, $userid)) {
        $completed = $progress['details_by_course'][$row->moodlecourseid] ?? false;
        $courseurl = (new moodle_url('/course/view.php', ['id' => $row->moodlecourseid]))->out(false);
        $enddate = (int)($row->enddate ?? 0);
        $isactive = ($enddate === 0) || ($enddate > $now);
        if ($isactive) {
            $grouped[$key]['has_active'] = true;
        }
        $grouped[$key]['moodlecourses'][] = [
            'name' => $row->moodlecoursename,
            'completed' => $completed,
            'url' => $courseurl,
        ];
    }
}

$numterms = max(1, (int)($curriculum->numterms ?? 1));
$termrows = [];
foreach (range(1, $numterms) as $t) {
    $termrows[$t] = ['term' => $t, 'courserows' => []];
}
foreach (array_values($grouped) as $item) {
    $moodlecount = count($item['moodlecourses']);
    $completedcount = count(array_filter($item['moodlecourses'], function ($m) {
        return $m['completed'];
    }));
    $hasmoodle = $moodlecount > 0;
    $row = [
        'externalcoursename' => $item['externalcoursename'],
        'moodlecourses' => $item['moodlecourses'],
        'moodlecount' => $moodlecount,
        'completedcount' => $completedcount,
        'moodlecoursesjson' => base64_encode(json_encode($item['moodlecourses'])),
        'hasmoodle' => $hasmoodle,
        'row_active' => $hasmoodle && !empty($item['has_active']),
        'row_ended' => $hasmoodle && empty($item['has_active']),
    ];
    $t = (int)($item['term'] ?? 1);
    if (!isset($termrows[$t])) {
        $termrows[$t] = ['term' => $t, 'courserows' => []];
    }
    $termrows[$t]['courserows'][] = $row;
}
$data['termsections'] = array_values($termrows);
$totaldisciplines = count($grouped);
$enrolleddisciplines = count(array_filter(array_values($grouped), function ($item) {
    return !empty($item['moodlecourses']);
}));
$enrollmentpercent = $totaldisciplines > 0 ? (int)round(($enrolleddisciplines / $totaldisciplines) * 100) : 0;

$data['progress'] = [
    'percent' => $progress['percent'],
    'completed' => $progress['completed'],
    'total' => $progress['total'],
    'enrollmentpercent' => $enrollmentpercent,
    'enrolleddisciplines' => $enrolleddisciplines,
    'totaldisciplines' => $totaldisciplines,
];

$PAGE->set_context($context);
$PAGE->set_course($course);
$PAGE->set_pagelayout('incourse');
$PAGE->set_secondary_navigation(false);
$PAGE->requires->css('/blocks/programcurriculum/styles.css');
$PAGE->requires->js_call_amd('block_programcurriculum/progress_actions', 'init');
$PAGE->set_url('/blocks/programcurriculum/progress.php', [
    'courseid' => $courseid,
    'userid' => $userid,
    'curriculumid' => $curriculumid,
]);
$PAGE->set_title(get_string('viewtitle', 'block_programcurriculum'));
$PAGE->set_heading(get_string('pageheading', 'block_programcurriculum'));
$PAGE->navbar->add($course->fullname, new moodle_url('/course/view.php', ['id' => $courseid]));
$PAGE->navbar->add(get_string('viewtitle', 'block_programcurriculum'), new moodle_url('/blocks/programcurriculum/view.php', ['courseid' => $courseid]));
$PAGE->navbar->add(get_string('studentprogress', 'block_programcurriculum'));

echo $OUTPUT->header();
echo $OUTPUT->render_from_template('block_programcurriculum/progress', $data);
echo $OUTPUT->footer();