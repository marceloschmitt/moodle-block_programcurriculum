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

$userid = optional_param('userid', 0, PARAM_INT);
if ($userid && !has_capability('block/programcurriculum:viewallprogress', $context)) {
    throw new required_capability_exception($context, 'block/programcurriculum:viewallprogress', 'nopermissions', '');
}

if (!$userid) {
    $userid = $USER->id;
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
];

foreach ($curricula as $curriculum) {
    $progressdata['curricula'][] = [
        'id' => $curriculum->id,
        'name' => $curriculum->name,
        'selected' => ((int)$curriculumid === (int)$curriculum->id),
    ];
}

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

$PAGE->set_context($context);
$PAGE->set_url('/blocks/programcurriculum/view.php', [
    'courseid' => $courseid,
    'userid' => $userid,
    'curriculumid' => $curriculumid,
]);
$PAGE->set_title(get_string('viewtitle', 'block_programcurriculum'));
$PAGE->set_heading(get_string('pluginname', 'block_programcurriculum'));

echo $OUTPUT->header();
echo $OUTPUT->render_from_template('block_programcurriculum/progress', $progressdata);
echo $OUTPUT->footer();
