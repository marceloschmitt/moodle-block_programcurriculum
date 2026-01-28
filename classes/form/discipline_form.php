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

class discipline_form extends \moodleform {
    protected function definition(): void {
        $mform = $this->_form;
        $mform->updateAttributes(['id' => 'programcurriculum-discipline-form']);
        $mform->setRequiredNote('');

        $mform->addElement('text', 'name', get_string('disciplinename', 'block_programcurriculum'), ['size' => 50]);
        $mform->setType('name', PARAM_TEXT);
        $mform->addRule('name', null, 'required', null, 'client');

        $mform->addElement('text', 'externalcode', get_string('disciplinecode', 'block_programcurriculum'));
        $mform->setType('externalcode', PARAM_ALPHANUMEXT);
        $mform->addRule('externalcode', null, 'required', null, 'client');

        $mform->addElement('hidden', 'sortorder');
        $mform->setType('sortorder', PARAM_INT);

        $mform->addElement('hidden', 'curriculumid');
        $mform->setType('curriculumid', PARAM_INT);

        $mform->addElement('hidden', 'id');
        $mform->setType('id', PARAM_INT);
    }

    public function validation($data, $files): array {
        global $DB;

        $errors = parent::validation($data, $files);

        if (!empty($data['externalcode'])) {
            $existing = $DB->get_record(
                'block_programcurriculum_discipline',
                ['externalcode' => $data['externalcode']],
                'id',
                IGNORE_MULTIPLE
            );
            if ($existing && (int)$existing->id !== (int)($data['id'] ?? 0)) {
                $errors['externalcode'] = get_string('duplicatedisciplinecode', 'block_programcurriculum');
            }
        }

        return $errors;
    }
}
