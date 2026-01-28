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

class mapping_repository {
    public function get(int $id): ?\stdClass {
        global $DB;

        return $DB->get_record('block_programcurriculum_mapping', ['id' => $id]);
    }

    public function get_by_discipline(int $disciplineid): array {
        global $DB;

        return $DB->get_records('block_programcurriculum_mapping', ['disciplineid' => $disciplineid], 'id ASC');
    }

    public function has_for_discipline(int $disciplineid): bool {
        global $DB;

        return $DB->record_exists('block_programcurriculum_mapping', ['disciplineid' => $disciplineid]);
    }

    public function get_by_curriculum(int $curriculumid): array {
        global $DB;

        $sql = "SELECT m.*
                  FROM {block_programcurriculum_mapping} m
                  JOIN {block_programcurriculum_discipline} d
                    ON d.id = m.disciplineid
                 WHERE d.curriculumid = :curriculumid";

        return $DB->get_records_sql($sql, ['curriculumid' => $curriculumid]);
    }

    public function upsert(\stdClass $record): int {
        global $DB;

        if (!empty($record->id)) {
            $DB->update_record('block_programcurriculum_mapping', $record);
            return (int)$record->id;
        }

        return (int)$DB->insert_record('block_programcurriculum_mapping', $record);
    }

    public function delete(int $id): void {
        global $DB;

        $DB->delete_records('block_programcurriculum_mapping', ['id' => $id]);
    }
}
