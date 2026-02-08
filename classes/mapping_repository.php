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

    public function get_by_course(int $courseid): array {
        global $DB;

        return $DB->get_records('block_programcurriculum_mapping', ['courseid' => $courseid], 'id ASC');
    }

    public function has_for_course(int $courseid): bool {
        global $DB;

        return $DB->record_exists('block_programcurriculum_mapping', ['courseid' => $courseid]);
    }

    /**
     * Delete all mappings for courses belonging to a curriculum.
     */
    public function delete_by_curriculum(int $curriculumid): void {
        global $DB;

        $courseids = $DB->get_fieldset_sql(
            "SELECT id FROM {block_programcurriculum_course} WHERE curriculumid = :cid",
            ['cid' => $curriculumid]
        );
        if (!empty($courseids)) {
            $DB->delete_records_list('block_programcurriculum_mapping', 'courseid', $courseids);
        }
    }

    public function get_by_curriculum(int $curriculumid): array {
        global $DB;

        $sql = "SELECT m.*
                  FROM {block_programcurriculum_mapping} m
                  JOIN {block_programcurriculum_course} c
                    ON c.id = m.courseid
                 WHERE c.curriculumid = :curriculumid";

        return $DB->get_records_sql($sql, ['curriculumid' => $curriculumid]);
    }

    /**
     * Get curriculum mappings with external course name and sortorder.
     *
     * @param int $curriculumid Curriculum id.
     * @return array List of objects with externalcoursename, moodlecourseid, sortorder.
     */
    public function get_by_curriculum_with_details(int $curriculumid): array {
        global $DB;

        $sql = "SELECT m.id AS mappingid, c.id AS externalcourseid, c.name AS externalcoursename, c.term, c.sortorder, m.moodlecourseid, co.fullname AS moodlecoursename, co.enddate
                  FROM {block_programcurriculum_mapping} m
                  JOIN {block_programcurriculum_course} c ON c.id = m.courseid
                  JOIN {course} co ON co.id = m.moodlecourseid
                 WHERE c.curriculumid = :curriculumid
              ORDER BY c.term ASC, c.sortorder ASC, c.name ASC";
        return array_values($DB->get_records_sql($sql, ['curriculumid' => $curriculumid]));
    }

    /**
     * Get mappings for a Moodle course, with external course, program and Moodle course names.
     *
     * @param int $moodlecourseid Moodle course id.
     * @return array List of objects with externalcoursename, programname, moodlecoursename.
     */
    public function get_by_moodle_course(int $moodlecourseid): array {
        global $DB;

        $sql = "SELECT c.name AS externalcoursename, cu.name AS programname, cu.id AS curriculumid, co.fullname AS moodlecoursename
                  FROM {block_programcurriculum_mapping} m
                  JOIN {block_programcurriculum_course} c ON c.id = m.courseid
                  JOIN {block_programcurriculum_curriculum} cu ON cu.id = c.curriculumid
                  JOIN {course} co ON co.id = m.moodlecourseid
                 WHERE m.moodlecourseid = :moodlecourseid";
        return array_values($DB->get_records_sql($sql, ['moodlecourseid' => $moodlecourseid]));
    }

    /**
     * Get Moodle courses whose fullname or shortname contains the external course name (or code),
     * excluding courses already mapped to this external course.
     *
     * @param int $externalcourseid Block course id.
     * @param string $name External course name (searched in Moodle fullname/shortname).
     * @param string $code External course code (optional; also searched if non-empty).
     * @return array List of stdClass with id, fullname, shortname.
     */
    public function get_suggested_moodle_courses(int $externalcourseid, string $name, string $code = ''): array {
        global $DB;

        $name = trim($name);
        $code = trim($code);
        if ($name === '' && $code === '') {
            return [];
        }

        $conditions = [];
        $params = ['siteid' => SITEID, 'courseid' => $externalcourseid];
        $i = 0;
        foreach (['name' => $name, 'code' => $code] as $key => $term) {
            if ($term === '') {
                continue;
            }
            $like1 = 'like' . $i . 'a';
            $like2 = 'like' . $i . 'b';
            $conditions[] = '(' . $DB->sql_like('c.fullname', ':' . $like1, false, false) . ' OR ' .
                $DB->sql_like('c.shortname', ':' . $like2, false, false) . ')';
            $params[$like1] = '%' . $DB->sql_like_escape($term) . '%';
            $params[$like2] = '%' . $DB->sql_like_escape($term) . '%';
            $i++;
        }
        if (empty($conditions)) {
            return [];
        }

        $sql = "SELECT c.id, c.fullname, c.shortname
                  FROM {course} c
                 WHERE c.id <> :siteid
                   AND (" . implode(' OR ', $conditions) . ")
                   AND c.id NOT IN (
                       SELECT m.moodlecourseid
                         FROM {block_programcurriculum_mapping} m
                        WHERE m.courseid = :courseid
                   )
              ORDER BY c.fullname ASC";
        return array_values($DB->get_records_sql($sql, $params));
    }

    public function get_counts_by_course_ids(array $courseids): array {
        global $DB;

        if (empty($courseids)) {
            return [];
        }

        list($insql, $params) = $DB->get_in_or_equal($courseids, SQL_PARAMS_NAMED);
        $sql = "SELECT courseid, COUNT(1) AS total
                  FROM {block_programcurriculum_mapping}
                 WHERE courseid {$insql}
              GROUP BY courseid";

        return $DB->get_records_sql_menu($sql, $params);
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

    /**
     * Create mappings for all Moodle courses whose fullname or shortname contains the given code.
     *
     * @param int $courseid External course (block_programcurriculum_course) id.
     * @param string $externalcode External course code to match in Moodle course names.
     * @return int Number of new mappings created.
     */
    public function run_automatic_mapping(int $courseid, string $externalcode): int {
        global $DB;

        $code = trim($externalcode);
        if ($code === '') {
            return 0;
        }

        $like = $DB->sql_like('c.fullname', ':like1', false, false) . ' OR ' .
                $DB->sql_like('c.shortname', ':like2', false, false);
        $sql = "SELECT c.id
                  FROM {course} c
                 WHERE c.id <> :siteid
                   AND ({$like})";
        $params = [
            'siteid' => SITEID,
            'like1' => '%' . $DB->sql_like_escape($code) . '%',
            'like2' => '%' . $DB->sql_like_escape($code) . '%',
        ];
        $matches = $DB->get_records_sql($sql, $params);
        $created = 0;
        foreach ($matches as $c) {
            $exists = $DB->record_exists('block_programcurriculum_mapping', [
                'courseid' => $courseid,
                'moodlecourseid' => $c->id,
            ]);
            if (!$exists) {
                $this->upsert((object)[
                    'courseid' => $courseid,
                    'moodlecourseid' => $c->id,
                ]);
                $created++;
            }
        }
        return $created;
    }
}
