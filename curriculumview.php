<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// This program is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

require_once(__DIR__ . '/../../config.php');

require_login();

$courseid = required_param('courseid', PARAM_INT);
$course = get_course($courseid);

$context = context_course::instance($courseid);
require_capability('block/programcurriculum:viewownprogress', $context);

$mappingrepo = new \block_programcurriculum\mapping_repository();
$coursemappings = $mappingrepo->get_by_moodle_course($courseid);
$firstmapping = !empty($coursemappings) ? reset($coursemappings) : null;
$curriculumid = $firstmapping ? (int)$firstmapping->curriculumid : 0;

if ($curriculumid <= 0) {
    redirect(
        new moodle_url('/course/view.php', ['id' => $courseid]),
        get_string('curriculumview_noprogram', 'block_programcurriculum'),
        null,
        \core\output\notification::NOTIFY_INFO
    );
}

$curriculumrepo = new \block_programcurriculum\curriculum_repository();
$curriculum = $curriculumrepo->get($curriculumid);
if (!$curriculum) {
    redirect(new moodle_url('/course/view.php', ['id' => $courseid]));
}

$coursesrepo = new \block_programcurriculum\course_repository();
$courselist = array_values($coursesrepo->get_by_curriculum($curriculumid));
$numterms = max(1, (int)($curriculum->numterms ?? 1));

$termsmap = [];
foreach ($courselist as $item) {
    $term = (int)($item->term ?? 1);
    if (!isset($termsmap[$term])) {
        $termsmap[$term] = [];
    }
    $termsmap[$term][] = [
        'externalcode' => $item->externalcode,
        'name' => $item->name,
    ];
}

$termsections = [];
for ($t = 1; $t <= $numterms; $t++) {
    $termcourses = $termsmap[$t] ?? [];
    $termsections[] = [
        'term' => $t,
        'courses' => $termcourses,
    ];
}

$PAGE->set_context($context);
$PAGE->set_course($course);
$PAGE->set_pagelayout('incourse');
$PAGE->set_secondary_navigation(false);
$PAGE->set_url('/blocks/programcurriculum/curriculumview.php', ['courseid' => $courseid]);
$PAGE->set_title(get_string('viewcurriculum', 'block_programcurriculum'));
$PAGE->set_heading(get_string('pageheading', 'block_programcurriculum'));
$PAGE->navbar->add($course->fullname, new moodle_url('/course/view.php', ['id' => $courseid]));
$PAGE->navbar->add(get_string('viewcurriculum', 'block_programcurriculum'));
$PAGE->requires->css('/blocks/programcurriculum/styles.css');

echo $OUTPUT->header();
echo $OUTPUT->render_from_template('block_programcurriculum/curriculumview', [
    'courseid' => $courseid,
    'programname' => $curriculum->name,
    'backurl' => (new moodle_url('/course/view.php', ['id' => $courseid]))->out(false),
    'termsections' => $termsections,
]);
echo $OUTPUT->footer();
