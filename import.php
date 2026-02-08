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
require_capability('block/programcurriculum:import', $context);

$PAGE->set_context($context);
$PAGE->set_url('/blocks/programcurriculum/import.php');
$PAGE->set_title(get_string('importtitle', 'block_programcurriculum'));
$PAGE->set_heading(get_string('pageheading', 'block_programcurriculum'));

$mform = new \block_programcurriculum\form\import_form();
$errors = [];
$preview = null;

if ($mform->is_cancelled()) {
    redirect(new moodle_url('/blocks/programcurriculum/manage.php'));
}

if ($data = $mform->get_data()) {
    $filepath = $mform->save_temp_file('importfile');
    if (!$filepath || !is_readable($filepath)) {
        $errors[] = get_string('importtext_nofile', 'block_programcurriculum');
    } else {
        $content = file_get_contents($filepath);
        $content = $content !== false ? trim($content) : '';
        if ($content === '') {
            $errors[] = get_string('importtext_empty', 'block_programcurriculum');
        } else {
            $importer = new \block_programcurriculum\importer();
            $preview = $importer->parse_text_format($content);
            $errors = $preview['errors'] ?? [];
        }
    }
}

if ($preview !== null && empty($errors) && !empty($preview['programs'])) {
    $programs = $preview['programs'];
    $lastindex = count($programs) - 1;
    foreach ($programs as $idx => &$prog) {
        $prog['termcount'] = count($prog['terms']);
        $prog['last'] = ($idx === $lastindex);
    }
    unset($prog);
    $preview['programs'] = $programs;
}

$templatecontext = [
    'helptext' => get_string('importtext_format_short', 'block_programcurriculum'),
    'errors' => $errors,
    'haserrors' => !empty($errors),
    'preview' => $preview,
    'showpreview' => $preview !== null && empty($errors),
];

echo $OUTPUT->header();
echo $OUTPUT->render_from_template('block_programcurriculum/import', $templatecontext);
$mform->display();
echo $OUTPUT->footer();
