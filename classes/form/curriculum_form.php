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

namespace block_programcurriculum\form;

defined('MOODLE_INTERNAL') || die();

require_once($GLOBALS['CFG']->libdir . '/formslib.php');

class curriculum_form extends \moodleform {
    protected function definition(): void {
        $mform = $this->_form;

        $mform->updateAttributes(['id' => 'programcurriculum-curriculum-form']);
        $mform->setRequiredNote('');

        $mform->addElement('text', 'name', get_string('curriculumname', 'block_programcurriculum'), ['size' => 50]);
        $mform->setType('name', PARAM_TEXT);
        $mform->addRule('name', null, 'required', null, 'client');

        $mform->addElement('text', 'externalcode', get_string('curriculumcode', 'block_programcurriculum'), ['size' => 7]);
        $mform->setType('externalcode', PARAM_ALPHANUMEXT);
        $mform->addRule('externalcode', null, 'required', null, 'client');

        $mform->addElement('textarea', 'description', get_string('curriculumdescription', 'block_programcurriculum'));
        $mform->setType('description', PARAM_TEXT);

        $mform->addElement('text', 'numterms', get_string('numterms', 'block_programcurriculum'), ['size' => 5]);
        $mform->setType('numterms', PARAM_INT);
        $mform->addRule('numterms', null, 'required', null, 'client');
        $mform->addRule('numterms', null, 'numeric', null, 'client');
        $mform->addRule('numterms', null, 'minlength', 1, 'client');
        $mform->setDefault('numterms', 1);
        $mform->addHelpButton('numterms', 'numterms', 'block_programcurriculum');

        $mform->addElement('hidden', 'id');
        $mform->setType('id', PARAM_INT);
    }

    public function validation($data, $files): array {
        global $DB;

        $errors = parent::validation($data, $files);

        if (!empty(trim($data['name'] ?? ''))) {
            $existing = $DB->get_record(
                'block_programcurriculum_curriculum',
                ['name' => trim($data['name'])],
                'id',
                IGNORE_MULTIPLE
            );
            if ($existing && (int)$existing->id !== (int)($data['id'] ?? 0)) {
                $errors['name'] = get_string('duplicatecurriculumname', 'block_programcurriculum');
            }
        }

        if (isset($data['numterms']) && ((int)$data['numterms']) < 1) {
            $errors['numterms'] = get_string('invalidnumterms', 'block_programcurriculum');
        }

        if (!empty($data['externalcode'])) {
            $existing = $DB->get_record(
                'block_programcurriculum_curriculum',
                ['externalcode' => $data['externalcode']],
                'id',
                IGNORE_MULTIPLE
            );
            if ($existing && (int)$existing->id !== (int)($data['id'] ?? 0)) {
                $errors['externalcode'] = get_string('duplicatecurriculumcode', 'block_programcurriculum');
            }
        }

        return $errors;
    }
}
