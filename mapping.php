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

$disciplineid = required_param('disciplineid', PARAM_INT);
$id = optional_param('id', 0, PARAM_INT);

$PAGE->set_context($context);
$PAGE->set_url('/blocks/programcurriculum/mapping.php', ['disciplineid' => $disciplineid, 'id' => $id]);
$PAGE->set_title(get_string('mappings', 'block_programcurriculum'));

$disciplinesrepo = new \block_programcurriculum\discipline_repository();
$discipline = $disciplinesrepo->get($disciplineid);
if (!$discipline) {
    throw new moodle_exception('invalidrecord', 'error');
}

$mappingrepo = new \block_programcurriculum\mapping_repository();
$mapping = $id ? $mappingrepo->get($id) : null;

$action = optional_param('action', '', PARAM_ALPHA);
if ($action === 'delete' && $id) {
    require_sesskey();
    if (!$mapping || (int)$mapping->disciplineid !== (int)$disciplineid) {
        throw new moodle_exception('invalidrecord', 'error');
    }
    $mappingrepo->delete($id);
    redirect(
        new moodle_url('/blocks/programcurriculum/mapping.php', ['disciplineid' => $disciplineid]),
        get_string('mappingdeleted', 'block_programcurriculum'),
        null,
        \core\output\notification::NOTIFY_SUCCESS
    );
}

$courses = [];
foreach (get_courses() as $course) {
    if ((int)$course->id === SITEID) {
        continue;
    }
    $courses[$course->id] = format_string($course->fullname, true, ['context' => context_course::instance($course->id)]);
}

$mform = new \block_programcurriculum\form\mapping_form(null, ['courses' => $courses]);
if ($mapping) {
    $mform->set_data($mapping);
} else {
    $mform->set_data((object)['disciplineid' => $disciplineid, 'required' => 1]);
}

if ($mform->is_cancelled()) {
    redirect(new moodle_url('/blocks/programcurriculum/discipline.php', ['curriculumid' => $discipline->curriculumid]));
}

if ($data = $mform->get_data()) {
    $mappingrepo->upsert($data);
    redirect(new moodle_url('/blocks/programcurriculum/mapping.php', ['disciplineid' => $disciplineid]));
}

$mappings = [];
foreach ($mappingrepo->get_by_discipline($disciplineid) as $item) {
    $course = get_course($item->courseid);
    $mappings[] = [
        'id' => $item->id,
        'coursename' => format_string($course->fullname, true, ['context' => context_course::instance($course->id)]),
        'required' => (int)$item->required,
        'editurl' => (new moodle_url('/blocks/programcurriculum/mapping.php', [
            'disciplineid' => $disciplineid,
            'id' => $item->id,
        ]))->out(false),
        'deleteurl' => (new moodle_url('/blocks/programcurriculum/mapping.php', [
            'disciplineid' => $disciplineid,
            'id' => $item->id,
            'action' => 'delete',
            'sesskey' => sesskey(),
        ]))->out(false),
        'deleteconfirm' => get_string('deletemappingconfirm', 'block_programcurriculum'),
    ];
}

$PAGE->set_heading($discipline->name);
$PAGE->requires->js_call_amd('block_programcurriculum/confirm_delete', 'init');

echo $OUTPUT->header();
echo $OUTPUT->render_from_template('block_programcurriculum/mapping', [
    'disciplinename' => $discipline->name,
    'mappings' => $mappings,
    'hasmappings' => !empty($mappings),
]);
$mform->display();
echo $OUTPUT->footer();
