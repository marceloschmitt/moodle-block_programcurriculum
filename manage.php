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

require_login();

$context = context_system::instance();
require_capability('block/programcurriculum:manage', $context);

$curriculumrepo = new \block_programcurriculum\curriculum_repository();
$coursesql = "SELECT curriculumid, COUNT(*) AS total
                    FROM {block_programcurriculum_course}
                GROUP BY curriculumid";
$coursecounts = $DB->get_records_sql_menu($coursesql);

$curricula = [];
foreach ($curriculumrepo->get_all() as $curriculum) {
    $curricula[] = [
        'id' => $curriculum->id,
        'name' => $curriculum->name,
        'externalcode' => $curriculum->externalcode,
        'coursecount' => $coursecounts[$curriculum->id] ?? 0,
        'editurl' => (new moodle_url('/blocks/programcurriculum/curriculum.php', ['id' => $curriculum->id]))->out(false),
        'coursesurl' => (new moodle_url('/blocks/programcurriculum/course.php', ['curriculumid' => $curriculum->id]))->out(false),
    ];
}

$data = [
    'addcurriculumurl' => (new moodle_url('/blocks/programcurriculum/curriculum.php'))->out(false),
    'curricula' => $curricula,
    'hascurricula' => !empty($curricula),
];

$PAGE->set_context($context);
$PAGE->set_url('/blocks/programcurriculum/manage.php');
$PAGE->set_title(get_string('managecurricula', 'block_programcurriculum'));
$PAGE->set_heading(get_string('pluginname', 'block_programcurriculum'));

echo $OUTPUT->header();
echo $OUTPUT->render_from_template('block_programcurriculum/manage', $data);
echo $OUTPUT->footer();
