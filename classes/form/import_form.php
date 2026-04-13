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

/**
 * Plugin file for block_programcurriculum.
 *
 * @package    block_programcurriculum
 * @copyright  2026 Marcelo Augusto Rauh Schmitt <marcelo.schmitt@poa.ifrs.edu.br>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace block_programcurriculum\form;

defined('MOODLE_INTERNAL') || die();

require_once($GLOBALS['CFG']->libdir . '/formslib.php');

class import_form extends \moodleform {
    /**
     * Handles definition.
     *
     * @return void Return value.
     */
    protected function definition(): void {
        $mform = $this->_form;

        $mform->addElement('filepicker', 'importfile', get_string('importtext_file', 'block_programcurriculum'), null, [
            'accepted_types' => ['.txt', '.csv'],
        ]);
        $mform->addRule('importfile', null, 'required', null, 'client');
        $mform->addHelpButton('importfile', 'importtext_file', 'block_programcurriculum');

        $mform->addElement('submit', 'import', get_string('import_do', 'block_programcurriculum'));
        $this->add_action_buttons(true, get_string('importtext_preview', 'block_programcurriculum'));
    }
}
