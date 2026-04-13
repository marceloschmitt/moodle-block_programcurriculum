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
// along with Moodle. If not, see <http://www.gnu.org/licenses/>.

/**
 * Plugin file for block_programcurriculum.
 *
 * @package    block_programcurriculum
 * @copyright  2026 Marcelo Augusto Rauh Schmitt <marcelo.schmitt@poa.ifrs.edu.br>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace block_programcurriculum;

defined('MOODLE_INTERNAL') || die();

/**
 * Repository for student self-declared completion of external courses.
 */
class user_completion_repository {

    /**
     * Get the set of external course IDs that the user has marked as completed (within a curriculum).
     *
     * @param int $userid User ID.
     * @param int $curriculumid Curriculum ID (only courses in this curriculum are considered).
     * @return int[] List of block_programcurriculum_course IDs.
     */
    public function get_completed_course_ids(int $userid, int $curriculumid): array {
        global $DB;

        $sql = "SELECT uc.courseid
                  FROM {block_programcurriculum_user_completion} uc
                  JOIN {block_programcurriculum_course} c ON c.id = uc.courseid
                 WHERE uc.userid = :userid AND c.curriculumid = :curriculumid";
        $records = $DB->get_records_sql($sql, ['userid' => $userid, 'curriculumid' => $curriculumid]);
        return array_map('intval', array_column($records, 'courseid'));
    }

    /**
     * Check whether the user has marked the given external course as completed.
     *
     * @param int $userid User ID.
     * @param int $courseid External course ID (block_programcurriculum_course.id).
     * @return bool
     */
    public function is_completed(int $userid, int $courseid): bool {
        global $DB;
        return $DB->record_exists('block_programcurriculum_user_completion', [
            'userid' => $userid,
            'courseid' => $courseid,
        ]);
    }

    /**
     * Set or unset the user's self-declared completion for an external course.
     *
     * @param int $userid User ID.
     * @param int $courseid External course ID (block_programcurriculum_course.id).
     * @param bool $completed True to mark as completed, false to remove.
     */
    public function set_completed(int $userid, int $courseid, bool $completed): void {
        global $DB;

        $exists = $DB->record_exists('block_programcurriculum_user_completion', [
            'userid' => $userid,
            'courseid' => $courseid,
        ]);

        if ($completed && !$exists) {
            $DB->insert_record('block_programcurriculum_user_completion', (object)[
                'userid' => $userid,
                'courseid' => $courseid,
                'timemodified' => time(),
            ]);
        } elseif (!$completed && $exists) {
            $DB->delete_records('block_programcurriculum_user_completion', [
                'userid' => $userid,
                'courseid' => $courseid,
            ]);
        }
    }
}
