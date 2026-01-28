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

namespace block_programcurriculum;

defined('MOODLE_INTERNAL') || die();

class course_repository {
    public function get_by_curriculum(int $curriculumid): array {
        global $DB;

        return $DB->get_records('block_programcurriculum_course', ['curriculumid' => $curriculumid], 'sortorder ASC, name ASC');
    }

    public function get(int $id): ?\stdClass {
        global $DB;

        return $DB->get_record('block_programcurriculum_course', ['id' => $id]);
    }

    public function get_by_externalcode(string $externalcode): ?\stdClass {
        global $DB;

        return $DB->get_record('block_programcurriculum_course', ['externalcode' => $externalcode]);
    }

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

    private function get_next_sortorder(int $curriculumid): int {
        global $DB;

        $sql = "SELECT COALESCE(MAX(sortorder), 0) + 1
                  FROM {block_programcurriculum_course}
                 WHERE curriculumid = :curriculumid";

        return (int)$DB->get_field_sql($sql, ['curriculumid' => $curriculumid]);
    }

    public function set_sortorder(int $id, int $sortorder): void {
        global $DB;

        $DB->set_field('block_programcurriculum_course', 'sortorder', $sortorder, ['id' => $id]);
    }

    public function delete(int $id): void {
        global $DB;

        $DB->delete_records('block_programcurriculum_course', ['id' => $id]);
    }
}
