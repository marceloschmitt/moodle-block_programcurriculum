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

class mapping_form extends \moodleform {
    protected function definition(): void {
        $mform = $this->_form;
        $customdata = $this->_customdata ?? [];
        $courses = $customdata['courses'] ?? [];
        $freezeCourse = !empty($customdata['freeze_course']);
        $courseid = (int)($customdata['courseid'] ?? 0);
        $mform->updateAttributes(['id' => 'programcurriculum-mapping-form']);
        $mform->setRequiredNote('');

        $mform->addElement('autocomplete', 'moodlecourseid', get_string('mappedmoodlecourse', 'block_programcurriculum'), $courses, [
            'multiple' => false,
            'placeholder' => get_string('courseplaceholder', 'block_programcurriculum'),
        ]);
        $mform->setType('moodlecourseid', PARAM_INT);
        $mform->addRule('moodlecourseid', null, 'required', null, 'client');
        if ($freezeCourse) {
            $mform->freeze('moodlecourseid');
        }

        $mform->addElement('hidden', 'courseid');
        $mform->setType('courseid', PARAM_INT);
        $mform->setDefault('courseid', $courseid);

        $mform->addElement('hidden', 'id');
        $mform->setType('id', PARAM_INT);

    }

    public function validation($data, $files): array {
        global $DB;

        $errors = parent::validation($data, $files);

        $courseid = (int)($data['courseid'] ?? 0);
        $moodlecourseid = (int)($data['moodlecourseid'] ?? 0);
        if ($courseid && $moodlecourseid) {
            $params = [
                'courseid' => $courseid,
                'moodlecourseid' => $moodlecourseid,
            ];
            $existing = $DB->get_record('block_programcurriculum_mapping', $params, 'id', IGNORE_MULTIPLE);
            if ($existing && (int)$existing->id !== (int)($data['id'] ?? 0)) {
                $errors['moodlecourseid'] = get_string('duplicatemapping', 'block_programcurriculum');
            }
        }

        return $errors;
    }
}
