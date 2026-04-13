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

/**
 * Calculates curriculum progress metrics.
 */
class progress_calculator {
    /**
     * Handles calculate_for_user.
     *
     * @param int $userid Parameter.
     * @param int $curriculumid Parameter.
     * @return array Return value.
     */
    public function calculate_for_user(int $userid, int $curriculumid): array {
        global $DB;

        $courserepo = new course_repository();
        $mappingrepository = new mapping_repository();
        $usercompletionrepo = new user_completion_repository();

        $allexternal = $courserepo->get_by_curriculum($curriculumid);
        $allexternalids = array_map(function ($c) {
            return (int)$c->id;
        }, $allexternal);
        $total = count($allexternalids);

        $mappings = $mappingrepository->get_by_curriculum($curriculumid);
        $usercompletedids = $usercompletionrepo->get_completed_course_ids($userid, $curriculumid);
        $usercompletedset = array_flip($usercompletedids);

        // Progress by completion: only disciplines marked as completed by the user.
        // Enrolled: those where user is enrolled in at least one mapped Moodle course.
        // Enrolled active: same but only Moodle courses with enddate = 0 or enddate > now (as on progress screen).
        $completedcount = count(array_intersect($usercompletedids, $allexternalids));
        $enrolledexternalids = [];
        $enrolledactiveexternalids = [];
        $now = time();
        foreach ($mappings as $mapping) {
            $moodlecourseid = (int)$mapping->moodlecourseid;
            $externalcourseid = (int)$mapping->courseid;
            $course = $DB->get_record('course', ['id' => $moodlecourseid], 'id, enddate');
            if (!$course) {
                continue;
            }
            $ctx = \context_course::instance($moodlecourseid);
            if (!is_enrolled($ctx, $userid)) {
                continue;
            }
            $enrolledexternalids[] = $externalcourseid;
            $enddate = (int)($course->enddate ?? 0);
            if ($enddate === 0 || $enddate > $now) {
                $enrolledactiveexternalids[] = $externalcourseid;
            }
        }
        // Progress by enrolment: enrolled in Moodle course OR marked as completed.
        $enrolledexternalids = array_unique(array_merge(
            $enrolledexternalids,
            $usercompletedids
        ));
        $enrolledcount = count(array_unique(array_intersect($enrolledexternalids, $allexternalids)));
        $enrolledactivecount = count(array_unique(array_intersect($enrolledactiveexternalids, $allexternalids)));

        $percent = $total > 0 ? (int)round(($completedcount / $total) * 100) : 0;

        // Details and details_by_course: per Moodle course (for UI list/modal).
        $details = [];
        foreach ($mappings as $mapping) {
            $moodlecourseid = (int)$mapping->moodlecourseid;
            $externalcourseid = (int)$mapping->courseid;
            $course = $DB->get_record('course', ['id' => $moodlecourseid], 'id, fullname');
            if (!$course) {
                continue;
            }
            $ctx = \context_course::instance($moodlecourseid);
            if (!is_enrolled($ctx, $userid)) {
                continue;
            }
            $moodlecompleted = $this->get_course_completion_state($userid, $moodlecourseid);
            $usermarked = isset($usercompletedset[$externalcourseid]);
            $iscompleted = $moodlecompleted || $usermarked;

            $details[] = [
                'courseid' => $course->id,
                'coursename' => $course->fullname,
                'completed' => $iscompleted,
            ];
        }
        $detailsbycourse = [];
        foreach ($details as $d) {
            $detailsbycourse[$d['courseid']] = $d['completed'];
        }

        return [
            'total' => $total,
            'completed' => $completedcount,
            'percent' => $percent,
            'enrolled' => $enrolledcount,
            'enrolled_active' => $enrolledactivecount,
            'details' => $details,
            'details_by_course' => $detailsbycourse,
        ];
    }

    /**
     * Handles get_course_completion_state.
     *
     * @param int $userid Parameter.
     * @param int $courseid Parameter.
     * @return bool Return value.
     */
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
