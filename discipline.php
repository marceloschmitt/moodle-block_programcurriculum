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
$id = optional_param('id', 0, PARAM_INT);

$curriculumrepo = new \block_programcurriculum\curriculum_repository();
$curriculum = $curriculumrepo->get($curriculumid);
if (!$curriculum) {
    throw new moodle_exception('invalidrecord', 'error');
}

$PAGE->set_context($context);
$PAGE->set_url('/blocks/programcurriculum/discipline.php', ['curriculumid' => $curriculumid, 'id' => $id]);
$PAGE->set_title(get_string('disciplines', 'block_programcurriculum'));
$PAGE->set_heading($curriculum->name);
$PAGE->requires->js_call_amd('block_programcurriculum/confirm_delete', 'init');

$disciplinesrepo = new \block_programcurriculum\discipline_repository();
$discipline = $id ? $disciplinesrepo->get($id) : null;

$mappingrepo = new \block_programcurriculum\mapping_repository();

$action = optional_param('action', '', PARAM_ALPHA);
if ($action === 'delete' && $id) {
    require_sesskey();
    if (!$discipline || (int)$discipline->curriculumid !== (int)$curriculumid) {
        throw new moodle_exception('invalidrecord', 'error');
    }
    if ($mappingrepo->has_for_discipline($discipline->id)) {
        redirect(
            new moodle_url('/blocks/programcurriculum/discipline.php', ['curriculumid' => $curriculumid]),
            get_string('disciplinedeletemappings', 'block_programcurriculum'),
            null,
            \core\output\notification::NOTIFY_ERROR
        );
    }
    $disciplinesrepo->delete($discipline->id);
    redirect(
        new moodle_url('/blocks/programcurriculum/discipline.php', ['curriculumid' => $curriculumid]),
        get_string('disciplinedeleted', 'block_programcurriculum'),
        null,
        \core\output\notification::NOTIFY_SUCCESS
    );
}

$action = optional_param('action', '', PARAM_ALPHA);
if ($action === 'move' && $id) {
    require_sesskey();
    $ordered = array_values($disciplinesrepo->get_by_curriculum($curriculumid));
    $orderedids = array_map(function ($item) {
        return (int)$item->id;
    }, $ordered);
    $position = array_search((int)$id, $orderedids, true);
    $newposition = optional_param('position', 0, PARAM_INT);
    if ($position !== false && $newposition > 0) {
        $target = max(1, min(count($orderedids), $newposition)) - 1;
        if ($target !== $position) {
            $movedid = $orderedids[$position];
            array_splice($orderedids, $position, 1);
            array_splice($orderedids, $target, 0, [$movedid]);
        }
        foreach ($orderedids as $index => $disciplineid) {
            $disciplinesrepo->set_sortorder($disciplineid, $index + 1);
        }
    }
    redirect(new moodle_url('/blocks/programcurriculum/discipline.php', ['curriculumid' => $curriculumid]));
}

$mform = new \block_programcurriculum\form\discipline_form(null, []);
if ($discipline) {
    $mform->set_data($discipline);
} else {
    $mform->set_data((object)['curriculumid' => $curriculumid]);
}

if ($mform->is_cancelled()) {
    redirect(new moodle_url('/blocks/programcurriculum/manage.php'));
}

if ($data = $mform->get_data()) {
    $disciplinesrepo->upsert($data);
    redirect(new moodle_url('/blocks/programcurriculum/discipline.php', ['curriculumid' => $curriculumid]));
}

$disciplines = [];
$disciplinelist = array_values($disciplinesrepo->get_by_curriculum($curriculumid));
$total = count($disciplinelist);
$disciplineids = array_map(function ($item) {
    return (int)$item->id;
}, $disciplinelist);
$mappingcounts = $mappingrepo->get_counts_by_discipline_ids($disciplineids);
foreach ($disciplinelist as $index => $item) {
    $mappingcount = (int)($mappingcounts[$item->id] ?? 0);
    $hasmappings = $mappingcount > 0;
    $disciplines[] = [
        'id' => $item->id,
        'name' => $item->name,
        'externalcode' => $item->externalcode,
        'sortorder' => $item->sortorder,
        'editurl' => (new moodle_url('/blocks/programcurriculum/discipline.php', [
            'curriculumid' => $curriculumid,
            'id' => $item->id,
        ]))->out(false),
        'mappingurl' => (new moodle_url('/blocks/programcurriculum/mapping.php', [
            'disciplineid' => $item->id,
        ]))->out(false),
        'moveactionurl' => (new moodle_url('/blocks/programcurriculum/discipline.php', [
            'curriculumid' => $curriculumid,
            'id' => $item->id,
            'action' => 'move',
            'sesskey' => sesskey(),
        ]))->out(false),
        'position' => $index + 1,
        'totalpositions' => $total,
        'mappingcount' => $mappingcount,
        'candelete' => !$hasmappings,
        'deleteurl' => !$hasmappings ? (new moodle_url('/blocks/programcurriculum/discipline.php', [
            'curriculumid' => $curriculumid,
            'id' => $item->id,
            'action' => 'delete',
            'sesskey' => sesskey(),
        ]))->out(false) : null,
        'deleteconfirm' => get_string('deletedisciplineconfirm', 'block_programcurriculum'),
    ];
}


echo $OUTPUT->header();
echo $OUTPUT->render_from_template('block_programcurriculum/discipline', [
    'curriculumname' => $curriculum->name,
    'disciplines' => $disciplines,
    'hasdisciplines' => !empty($disciplines),
    'formhtml' => (function () use ($mform): string {
        ob_start();
        $mform->display();
        return (string)ob_get_clean();
    })(),
    'modaltitle' => $id ? get_string('editdiscipline', 'block_programcurriculum') : get_string('adddiscipline', 'block_programcurriculum'),
    'openmodal' => !empty($id),
]);
echo $OUTPUT->footer();
