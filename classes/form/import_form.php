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

        $mform->addElement('filepicker', 'csvfile', get_string('csvfile', 'block_programcurriculum'), null, [
            'accepted_types' => ['.csv'],
        ]);
        $mform->addRule('csvfile', null, 'required', null, 'client');

        $this->add_action_buttons(true, get_string('upload', 'block_programcurriculum'));
    }
}
