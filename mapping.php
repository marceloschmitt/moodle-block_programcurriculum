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

$courseid = required_param('courseid', PARAM_INT);
$id = optional_param('id', 0, PARAM_INT);

$PAGE->set_context($context);
$PAGE->set_url('/blocks/programcurriculum/mapping.php', ['courseid' => $courseid, 'id' => $id]);
$PAGE->set_title(get_string('mappings', 'block_programcurriculum'));

$coursesrepo = new \block_programcurriculum\course_repository();
$course = $coursesrepo->get($courseid);
if (!$course) {
    throw new moodle_exception('invalidrecord', 'error');
}

$mappingrepo = new \block_programcurriculum\mapping_repository();
$mapping = $id ? $mappingrepo->get($id) : null;

$action = optional_param('action', '', PARAM_ALPHA);
if ($action === 'delete' && $id) {
    require_sesskey();
    if (!$mapping || (int)$mapping->courseid !== (int)$courseid) {
        throw new moodle_exception('invalidrecord', 'error');
    }
    $mappingrepo->delete($id);
    redirect(
        new moodle_url('/blocks/programcurriculum/mapping.php', ['courseid' => $courseid]),
        get_string('mappingdeleted', 'block_programcurriculum'),
        null,
        \core\output\notification::NOTIFY_SUCCESS
    );
}

if ($action === 'automatic') {
    require_sesskey();
    $maincode = trim($course->externalcode ?? '');
    $equivcode = trim($course->equivalencecode ?? '');
    if ($maincode === '' && $equivcode === '') {
        redirect(
            new moodle_url('/blocks/programcurriculum/mapping.php', ['courseid' => $courseid]),
            get_string('automaticmappingnocode', 'block_programcurriculum'),
            null,
            \core\output\notification::NOTIFY_ERROR
        );
    }
    $created = 0;
    if ($maincode !== '') {
        $created += $mappingrepo->run_automatic_mapping($courseid, $maincode);
    }
    if ($equivcode !== '') {
        $created += $mappingrepo->run_automatic_mapping($courseid, $equivcode);
    }
    redirect(
        new moodle_url('/blocks/programcurriculum/mapping.php', ['courseid' => $courseid]),
        get_string('automaticmappingdone', 'block_programcurriculum', $created),
        null,
        \core\output\notification::NOTIFY_SUCCESS
    );
}

$courses = [];
foreach (get_courses() as $moodlecourse) {
    if ((int)$moodlecourse->id === SITEID) {
        continue;
    }
    $courses[$moodlecourse->id] = format_string($moodlecourse->fullname, true, ['context' => context_course::instance($moodlecourse->id)]);
}

$mform = new \block_programcurriculum\form\mapping_form(null, [
    'courses' => $courses,
    'freeze_course' => !empty($mapping),
    'courseid' => $courseid,
]);
if ($mapping) {
    $mform->set_data($mapping);
} else {
    $mform->set_data((object)['courseid' => $courseid]);
}

if ($mform->is_cancelled()) {
    redirect(new moodle_url('/blocks/programcurriculum/course.php', ['curriculumid' => $course->curriculumid]));
}

if ($data = $mform->get_data()) {
    $mappingrepo->upsert($data);
    redirect(new moodle_url('/blocks/programcurriculum/mapping.php', ['courseid' => $courseid]));
}

$validationerror = $mform->is_submitted() && !$mform->is_cancelled() && !$mform->is_validated();
$validationmessage = null;
if ($validationerror) {
    $moodlecourseid = optional_param('moodlecourseid', 0, PARAM_INT);
    if ($moodlecourseid) {
        $existing = $DB->get_record(
            'block_programcurriculum_mapping',
            ['courseid' => $courseid, 'moodlecourseid' => $moodlecourseid],
            'id',
            IGNORE_MULTIPLE
        );
        if ($existing && (int)$existing->id !== (int)$id) {
            $validationmessage = get_string('duplicatemapping', 'block_programcurriculum');
        }
    }
    if ($validationmessage === null) {
        $validationmessage = get_string('mappingformerror', 'block_programcurriculum');
    }
}

$mappings = [];
foreach ($mappingrepo->get_by_course($courseid) as $item) {
    $moodlecourse = $DB->get_record('course', ['id' => $item->moodlecourseid], 'id, fullname');
    $coursename = $moodlecourse
        ? format_string($moodlecourse->fullname, true, ['context' => context_course::instance($moodlecourse->id)])
        : get_string('moodlecourse_deleted', 'block_programcurriculum', $item->moodlecourseid);
    $mappings[] = [
        'id' => $item->id,
        'coursename' => $coursename,
        'moodlecourseid' => $item->moodlecourseid,
        'deleteurl' => (new moodle_url('/blocks/programcurriculum/mapping.php', [
            'courseid' => $courseid,
            'id' => $item->id,
            'action' => 'delete',
            'sesskey' => sesskey(),
        ]))->out(false),
        'deleteconfirm' => get_string('deletemappingconfirm', 'block_programcurriculum'),
    ];
}

$PAGE->set_heading(get_string('pageheading', 'block_programcurriculum'));
$PAGE->requires->js_call_amd('block_programcurriculum/mapping_actions', 'init');

echo $OUTPUT->header();
echo $OUTPUT->render_from_template('block_programcurriculum/mapping', [
    'coursename' => $course->name,
    'coursecode' => $course->externalcode,
    'backurl' => (new moodle_url('/blocks/programcurriculum/course.php', ['curriculumid' => $course->curriculumid]))->out(false),
    'automaticurl' => (new moodle_url('/blocks/programcurriculum/mapping.php', [
        'courseid' => $courseid,
        'action' => 'automatic',
        'sesskey' => sesskey(),
    ]))->out(false),
    'courseid' => $courseid,
    'mappings' => $mappings,
    'hasmappings' => !empty($mappings),
    'validationerror' => $validationerror,
    'validationmessage' => $validationmessage,
    'formhtml' => (function () use ($mform): string {
        ob_start();
        $mform->display();
        return (string)ob_get_clean();
    })(),
    'modaltitle' => $id ? get_string('editmapping', 'block_programcurriculum') : get_string('addmapping', 'block_programcurriculum'),
]);
echo $OUTPUT->footer();
