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

class import_form extends \moodleform {
    protected function definition(): void {
        $mform = $this->_form;

        $mform->addElement('textarea', 'importtext', get_string('importtext_content', 'block_programcurriculum'), [
            'rows' => 20,
            'cols' => 80,
            'class' => 'programcurriculum-import-text',
        ]);
        $mform->setType('importtext', PARAM_RAW);
        $mform->addHelpButton('importtext', 'importtext_format', 'block_programcurriculum');

        $mform->addElement('filepicker', 'importfile', get_string('importtext_file', 'block_programcurriculum'), null, [
            'accepted_types' => ['.txt', '.csv'],
        ]);
        $mform->addHelpButton('importfile', 'importtext_file_help', 'block_programcurriculum');

        $this->add_action_buttons(true, get_string('importtext_preview', 'block_programcurriculum'));
    }
}
