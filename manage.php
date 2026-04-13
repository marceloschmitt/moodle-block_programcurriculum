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

/**
 * Plugin file for block_programcurriculum.
 *
 * @package    block_programcurriculum
 * @copyright  2026 Marcelo Augusto Rauh Schmitt <marcelo.schmitt@poa.ifrs.edu.br>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(__DIR__ . '/../../config.php');

require_login();

$context = context_system::instance();
require_capability('block/programcurriculum:manage', $context);

$id = optional_param('id', 0, PARAM_INT);

$PAGE->set_context($context);
$PAGE->set_url('/blocks/programcurriculum/manage.php', $id ? ['id' => $id] : []);
$PAGE->set_title(get_string('managecurricula', 'block_programcurriculum'));
$PAGE->set_heading(get_string('pageheading', 'block_programcurriculum'));
$PAGE->requires->js_call_amd('block_programcurriculum/manage_actions', 'init');

$curriculumrepo = new \block_programcurriculum\curriculum_repository();
$coursesrepo = new \block_programcurriculum\course_repository();
$mappingrepo = new \block_programcurriculum\mapping_repository();
$record = $id ? $curriculumrepo->get($id) : null;

$action = optional_param('action', '', PARAM_ALPHA);
$deleteid = optional_param('deleteid', 0, PARAM_INT);
$deleteallcoursesid = optional_param('deleteallcoursesid', 0, PARAM_INT);

if ($action === 'automatic' && confirm_sesskey()) {
    $all = array_values($curriculumrepo->get_all());
    $created = 0;
    foreach ($all as $curriculum) {
        $courselist = array_values($coursesrepo->get_by_curriculum($curriculum->id));
        foreach ($courselist as $item) {
            $created += $mappingrepo->run_automatic_mapping((int)$item->id, $item->externalcode ?? '');
            $equiv = trim($item->equivalencecode ?? '');
            if ($equiv !== '') {
                $created += $mappingrepo->run_automatic_mapping((int)$item->id, $equiv);
            }
        }
    }
    redirect(
        new moodle_url('/blocks/programcurriculum/manage.php'),
        get_string('automaticmappingdonecurriculum', 'block_programcurriculum', $created),
        null,
        \core\output\notification::NOTIFY_SUCCESS
    );
}

if ($action === 'deleteallcourses' && $deleteallcoursesid && confirm_sesskey()) {
    $cur = $curriculumrepo->get($deleteallcoursesid);
    if ($cur) {
        $mappingrepo->delete_by_curriculum($deleteallcoursesid);
        $coursesrepo->delete_by_curriculum($deleteallcoursesid);
        redirect(
            new moodle_url('/blocks/programcurriculum/manage.php'),
            get_string('deleteallcoursesdone', 'block_programcurriculum'),
            null,
            \core\output\notification::NOTIFY_SUCCESS
        );
    }
}

if ($action === 'delete' && $deleteid && confirm_sesskey()) {
    $todelete = $curriculumrepo->get($deleteid);
    if ($todelete) {
        $coursesql = "SELECT COUNT(*) FROM {block_programcurriculum_course} WHERE curriculumid = :cid";
        $coursecount = (int)$DB->get_field_sql($coursesql, ['cid' => $deleteid]);
        if ($coursecount === 0) {
            $curriculumrepo->delete($deleteid);
            redirect(
                new moodle_url('/blocks/programcurriculum/manage.php'),
                get_string('curriculumdeleted', 'block_programcurriculum'),
                null,
                \core\output\notification::NOTIFY_SUCCESS
            );
        }
        redirect(
            new moodle_url('/blocks/programcurriculum/manage.php'),
            get_string('curriculumdeletecourses', 'block_programcurriculum'),
            null,
            \core\output\notification::NOTIFY_ERROR
        );
    }
}

$mform = new \block_programcurriculum\form\curriculum_form(null, []);
if ($record) {
    $mform->set_data($record);
} else {
    $mform->set_data((object)['id' => 0]);
}

if ($mform->is_cancelled()) {
    redirect(new moodle_url('/blocks/programcurriculum/manage.php'));
}

if ($data = $mform->get_data()) {
    $curriculumrepo->upsert($data);
    redirect(new moodle_url('/blocks/programcurriculum/manage.php'));
}

$validationerror = $mform->is_submitted() && !$mform->is_cancelled() && !$mform->is_validated();

$validationmessage = optional_param('validationmessage', '', PARAM_TEXT);

if ($validationerror) {
    // Force validation to populate errors.
    $submitteddata = $mform->get_submitted_data();
    $files = [];
    $errors = $mform->validation((array)$submitteddata, $files);

    $hasduplicatename = !empty($errors['name']);
    $hasduplicatecode = !empty($errors['externalcode']);
    if ($hasduplicatename || $hasduplicatecode) {
        $validationmessage = get_string('duplicatecurriculumnameorcode', 'block_programcurriculum');
    } else {
        $validationmessage = get_string('curriculumformerror', 'block_programcurriculum');
    }

    // Redirect to clear the form and show only the error message.
    redirect(new moodle_url('/blocks/programcurriculum/manage.php', ['validationmessage' => $validationmessage]));
}

$coursesql = "SELECT curriculumid, COUNT(*) AS total
              FROM {block_programcurriculum_course}
              GROUP BY curriculumid";
$coursecounts = $DB->get_records_sql_menu($coursesql);

$curricula = [];
$all = array_values($curriculumrepo->get_all());
foreach ($all as $index => $curriculum) {
    $coursecount = (int)($coursecounts[$curriculum->id] ?? 0);
    $candelete = $coursecount === 0;
    $curricula[] = [
        'id' => $curriculum->id,
        'name' => $curriculum->name,
        'externalcode' => $curriculum->externalcode,
        'coursecount' => $coursecount,
        'position' => $index + 1,
        'editurl' => (new moodle_url('/blocks/programcurriculum/manage.php', ['id' => $curriculum->id]))->out(false),
        'coursesurl' => (new moodle_url('/blocks/programcurriculum/course.php', ['curriculumid' => $curriculum->id]))->out(false),
        'editname' => $curriculum->name,
        'editcode' => $curriculum->externalcode,
        'editdescription' => $curriculum->description ?? '',
        'candelete' => $candelete,
        'deleteurl' => $candelete ? (new moodle_url('/blocks/programcurriculum/manage.php', [
            'action' => 'delete',
            'deleteid' => $curriculum->id,
            'sesskey' => sesskey(),
        ]))->out(false) : null,
        'deleteconfirm' => get_string('deletecurriculumconfirm', 'block_programcurriculum'),
        'hascourses' => $coursecount > 0,
        'deleteallcoursesurl' => (new moodle_url('/blocks/programcurriculum/manage.php', [
            'action' => 'deleteallcourses',
            'deleteallcoursesid' => $curriculum->id,
            'sesskey' => sesskey(),
        ]))->out(false),
        'deleteallcoursestitle' => get_string('deleteallcoursestitle', 'block_programcurriculum'),
        'deleteallcoursesconfirm' => get_string('deleteallcoursesconfirm', 'block_programcurriculum', $curriculum->name),
        'deleteallcoursestitle_attr' => htmlspecialchars(get_string('deleteallcoursestitle', 'block_programcurriculum'), ENT_QUOTES, 'UTF-8'),
        'deleteallcoursesconfirm_attr' => htmlspecialchars(get_string('deleteallcoursesconfirm', 'block_programcurriculum', $curriculum->name), ENT_QUOTES, 'UTF-8'),
    ];
}

echo $OUTPUT->header();
echo $OUTPUT->render_from_template('block_programcurriculum/manage', [
    'curricula' => $curricula,
    'hascurricula' => !empty($curricula),
    'automaticmappingurl' => (new moodle_url('/blocks/programcurriculum/manage.php', [
        'action' => 'automatic',
        'sesskey' => sesskey(),
    ]))->out(false),
    'validationerror' => !empty($validationmessage),
    'validationmessage' => $validationmessage,
    'formhtml' => (function () use ($mform): string {
        ob_start();
        $mform->display();
        return (string) ob_get_clean();
    })(),
    'modaltitle' => $id ? get_string('editcurriculum', 'block_programcurriculum') : get_string('addcurriculum', 'block_programcurriculum'),
]);
echo $OUTPUT->footer();
