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

class progress_calculator {
    public function calculate_for_user(int $userid, int $curriculumid): array {
        global $DB;

        $mappingrepository = new mapping_repository();
        $mappings = $mappingrepository->get_by_curriculum($curriculumid);

        if (empty($mappings)) {
            return [
                'total' => 0,
                'completed' => 0,
                'percent' => 0,
                'details' => [],
            ];
        }

        $details = [];
        $completedcount = 0;
        foreach ($mappings as $mapping) {
            $moodlecourseid = (int)$mapping->moodlecourseid;
            $course = $DB->get_record('course', ['id' => $moodlecourseid], 'id, fullname');
            if (!$course) {
                continue;
            }
            $ctx = \context_course::instance($moodlecourseid);
            if (!is_enrolled($ctx, $userid)) {
                continue;
            }

            $iscompleted = $this->get_course_completion_state($userid, $moodlecourseid);
            if ($iscompleted) {
                $completedcount++;
            }

            $details[] = [
                'courseid' => $course->id,
                'coursename' => $course->fullname,
                'completed' => $iscompleted,
            ];
        }

        $total = count($details);
        $percent = $total > 0 ? (int)round(($completedcount / $total) * 100) : 0;

        $detailsbycourse = [];
        foreach ($details as $d) {
            $detailsbycourse[$d['courseid']] = $d['completed'];
        }

        return [
            'total' => $total,
            'completed' => $completedcount,
            'percent' => $percent,
            'details' => $details,
            'details_by_course' => $detailsbycourse,
        ];
    }

    private function get_course_completion_state(int $userid, int $courseid): bool {
        try {
            $course = get_course($courseid);
        } catch (\Exception $e) {
            return false;
        }
        $completion = new \completion_info($course);
        if (!$completion->is_enabled()) {
            return false;
        }

        return $completion->is_course_complete($userid);
    }
}
