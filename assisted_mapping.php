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

$curriculumid = required_param('curriculumid', PARAM_INT);

$curriculumrepo = new \block_programcurriculum\curriculum_repository();
$curriculum = $curriculumrepo->get($curriculumid);
if (!$curriculum) {
    throw new moodle_exception('invalidrecord', 'error');
}

$PAGE->set_context($context);
$PAGE->set_url('/blocks/programcurriculum/assisted_mapping.php', ['curriculumid' => $curriculumid]);
$PAGE->set_title(get_string('assistedmapping', 'block_programcurriculum'));
$PAGE->set_heading(get_string('pageheading', 'block_programcurriculum'));
$PAGE->requires->css('/blocks/programcurriculum/styles.css');

$coursesrepo = new \block_programcurriculum\course_repository();
$mappingrepo = new \block_programcurriculum\mapping_repository();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && confirm_sesskey()) {
    global $DB;
    $selected = optional_param_array('mapping', [], PARAM_ALPHANUMEXT);
    $created = 0;
    foreach ($selected as $pair) {
        if (preg_match('/^(\d+)-(\d+)$/', $pair, $m)) {
            $externalid = (int) $m[1];
            $moodleid = (int) $m[2];
            $course = $coursesrepo->get($externalid);
            if ($course && (int)$course->curriculumid === (int)$curriculumid) {
                $exists = $DB->record_exists('block_programcurriculum_mapping', [
                    'courseid' => $externalid,
                    'moodlecourseid' => $moodleid,
                ]);
                if (!$exists) {
                    $mappingrepo->upsert((object)[
                        'id' => 0,
                        'courseid' => $externalid,
                        'moodlecourseid' => $moodleid,
                    ]);
                    $created++;
                }
            }
        }
    }
    redirect(
        new moodle_url('/blocks/programcurriculum/assisted_mapping.php', ['curriculumid' => $curriculumid]),
        get_string('assistedmappingdone', 'block_programcurriculum', $created),
        null,
        \core\output\notification::NOTIFY_SUCCESS
    );
}

$courselist = array_values($coursesrepo->get_by_curriculum($curriculumid));
$rows = [];
foreach ($courselist as $ext) {
    $suggestions = $mappingrepo->get_suggested_moodle_courses(
        (int) $ext->id,
        $ext->name ?? '',
        $ext->externalcode ?? ''
    );
    $suggestionlist = [];
    foreach ($suggestions as $mc) {
        $suggestionlist[] = [
            'moodleid' => $mc->id,
            'fullname' => $mc->fullname,
            'shortname' => $mc->shortname,
            'value' => $ext->id . '-' . $mc->id,
        ];
    }
    $rows[] = [
        'externalid' => $ext->id,
        'externalname' => $ext->name,
        'externalcode' => $ext->externalcode,
        'suggestions' => $suggestionlist,
        'hassuggestions' => !empty($suggestionlist),
    ];
}

echo $OUTPUT->header();
echo $OUTPUT->render_from_template('block_programcurriculum/assisted_mapping', [
    'curriculumid' => $curriculumid,
    'curriculumname' => $curriculum->name,
    'backurl' => (new moodle_url('/blocks/programcurriculum/course.php', ['curriculumid' => $curriculumid]))->out(false),
    'formurl' => (new moodle_url('/blocks/programcurriculum/assisted_mapping.php', ['curriculumid' => $curriculumid]))->out(false),
    'sesskey' => sesskey(),
    'rows' => $rows,
    'hasrows' => !empty($rows),
]);
echo $OUTPUT->footer();
