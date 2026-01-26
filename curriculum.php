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

$id = optional_param('id', 0, PARAM_INT);
$returnurl = new moodle_url('/blocks/programcurriculum/manage.php');

$PAGE->set_context($context);
$PAGE->set_url('/blocks/programcurriculum/curriculum.php', ['id' => $id]);
$PAGE->set_title($id ? get_string('editcurriculum', 'block_programcurriculum') : get_string('addcurriculum', 'block_programcurriculum'));
$PAGE->set_heading(get_string('managecurricula', 'block_programcurriculum'));

$repo = new \block_programcurriculum\curriculum_repository();
$record = $id ? $repo->get($id) : null;

$mform = new \block_programcurriculum\form\curriculum_form(null, []);
if ($record) {
    $mform->set_data($record);
}

if ($mform->is_cancelled()) {
    redirect($returnurl);
}

if ($data = $mform->get_data()) {
    $repo->upsert($data);
    redirect($returnurl);
}

echo $OUTPUT->header();
$mform->display();
echo $OUTPUT->footer();
