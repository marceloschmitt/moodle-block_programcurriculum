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

class curriculum_repository {
    public function get_all(): array {
        global $DB;

        return $DB->get_records('block_programcurriculum_curriculum', [], 'name ASC');
    }

    public function get(int $id): ?\stdClass {
        global $DB;

        return $DB->get_record('block_programcurriculum_curriculum', ['id' => $id]);
    }

    public function get_by_externalcode(string $externalcode): ?\stdClass {
        global $DB;

        return $DB->get_record('block_programcurriculum_curriculum', ['externalcode' => $externalcode]);
    }

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
}
