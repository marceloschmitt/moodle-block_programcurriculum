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

class course_repository {
    /**
     * Handles get_by_curriculum.
     *
     * @param int $curriculumid Parameter.
     * @return array Return value.
     */
    public function get_by_curriculum(int $curriculumid): array {
        global $DB;

        return $DB->get_records('block_programcurriculum_course', ['curriculumid' => $curriculumid], 'term ASC, sortorder ASC, name ASC');
    }

    /**
     * Handles get.
     *
     * @param int $id Parameter.
     * @return ?\\stdClass Return value.
     */
    public function get(int $id): ?\stdClass {
        global $DB;

        $record = $DB->get_record('block_programcurriculum_course', ['id' => $id]);
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

        $record = $DB->get_record('block_programcurriculum_course', ['externalcode' => $externalcode], 'id', IGNORE_MULTIPLE);
        return $record ?: null;
    }

    /**
     * Get course by external code within a curriculum. Used to detect if discipline is already in this program.
     */
    public function get_by_externalcode_and_curriculum(string $externalcode, int $curriculumid): ?\stdClass {
        global $DB;

        $record = $DB->get_record('block_programcurriculum_course', [
            'externalcode' => $externalcode,
            'curriculumid' => $curriculumid,
        ]);
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

        if (!empty($record->id)) {
            $DB->update_record('block_programcurriculum_course', $record);
            return (int)$record->id;
        }

        if (empty($record->sortorder) || (int)$record->sortorder <= 0) {
            $record->sortorder = $this->get_next_sortorder((int)$record->curriculumid);
        }

        return (int)$DB->insert_record('block_programcurriculum_course', $record);
    }

    /**
     * Handles get_next_sortorder.
     *
     * @param int $curriculumid Parameter.
     * @return int Return value.
     */
    private function get_next_sortorder(int $curriculumid): int {
        global $DB;

        $sql = "SELECT COALESCE(MAX(sortorder), 0) + 1
                  FROM {block_programcurriculum_course}
                 WHERE curriculumid = :curriculumid";

        return (int)$DB->get_field_sql($sql, ['curriculumid' => $curriculumid]);
    }

    /**
     * Handles set_sortorder.
     *
     * @param int $id Parameter.
     * @param int $sortorder Parameter.
     * @return void Return value.
     */
    public function set_sortorder(int $id, int $sortorder): void {
        global $DB;

        $DB->set_field('block_programcurriculum_course', 'sortorder', $sortorder, ['id' => $id]);
    }

    /**
     * Handles delete.
     *
     * @param int $id Parameter.
     * @return void Return value.
     */
    public function delete(int $id): void {
        global $DB;

        $DB->delete_records('block_programcurriculum_course', ['id' => $id]);
    }

    /**
     * Delete all courses of a curriculum. Call mapping_repository::delete_by_curriculum first.
     */
    public function delete_by_curriculum(int $curriculumid): void {
        global $DB;

        $DB->delete_records('block_programcurriculum_course', ['curriculumid' => $curriculumid]);
    }
}
