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

$disciplinesrepo = new \block_programcurriculum\discipline_repository();
$discipline = $id ? $disciplinesrepo->get($id) : null;

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
foreach ($disciplinesrepo->get_by_curriculum($curriculumid) as $item) {
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
    ];
}


echo $OUTPUT->header();
echo $OUTPUT->render_from_template('block_programcurriculum/discipline', [
    'curriculumname' => $curriculum->name,
    'disciplines' => $disciplines,
    'hasdisciplines' => !empty($disciplines),
]);
$mform->display();
echo $OUTPUT->footer();
