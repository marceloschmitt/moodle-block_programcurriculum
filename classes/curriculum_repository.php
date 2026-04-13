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

namespace block_programcurriculum;

defined('MOODLE_INTERNAL') || die();

class curriculum_repository {
    /**
     * Handles get_all.
     *
     * @return array Return value.
     */
    public function get_all(): array {
        global $DB;

        return $DB->get_records('block_programcurriculum_curriculum', [], 'name ASC');
    }

    /**
     * Handles get.
     *
     * @param int $id Parameter.
     * @return ?\\stdClass Return value.
     */
    public function get(int $id): ?\stdClass {
        global $DB;

        $record = $DB->get_record('block_programcurriculum_curriculum', ['id' => $id]);
        return $record ?: null;
    }

    /**
     * Handles get_by_externalcode.
     *
     * @param string $externalcode Parameter.
     * @return ?\\stdClass Return value.
     */
    public function get_by_externalcode(string $externalcode): ?\stdClass {
        global $DB;

        $record = $DB->get_record('block_programcurriculum_curriculum', ['externalcode' => $externalcode]);
        return $record ?: null;
    }

    /**
     * Handles upsert.
     *
     * @param \\stdClass $record Parameter.
     * @return int Return value.
     */
    public function upsert(\stdClass $record): int {
        global $DB;

        $now = time();
        if (!empty($record->id)) {
            $record->timemodified = $now;
            $DB->update_record('block_programcurriculum_curriculum', $record);
            return (int)$record->id;
        }

        $record->timecreated = $now;
        $record->timemodified = $now;
        return (int)$DB->insert_record('block_programcurriculum_curriculum', $record);
    }

    /**
     * Handles delete.
     *
     * @param int $id Parameter.
     * @return void Return value.
     */
    public function delete(int $id): void {
        global $DB;

        $DB->delete_records('block_programcurriculum_curriculum', ['id' => $id]);
    }
}
