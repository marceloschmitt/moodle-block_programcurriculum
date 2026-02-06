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

namespace block_programcurriculum\external;

defined('MOODLE_INTERNAL') || die();

require_once($GLOBALS['CFG']->libdir . '/externallib.php');

/**
 * External API for reordering curriculum courses.
 *
 * @package    block_programcurriculum
 * @copyright  2025
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class reorder_courses extends \external_api {

    /**
     * Returns description of method parameters.
     *
     * @return \external_function_parameters
     */
    public static function execute_parameters(): \external_function_parameters {
        return new \external_function_parameters([
            'curriculumid' => new \external_value(PARAM_INT, 'Curriculum ID'),
            'courseid' => new \external_value(PARAM_INT, 'Course ID to move'),
            'newposition' => new \external_value(PARAM_INT, 'New position (1-based)'),
        ]);
    }

    /**
     * Reorder a course within the curriculum.
     *
     * @param int $curriculumid Curriculum ID
     * @param int $courseid Course ID to move
     * @param int $newposition New position (1-based)
     * @return array Success status
     */
    public static function execute(int $curriculumid, int $courseid, int $newposition): array {
        global $DB;

        $params = self::validate_parameters(self::execute_parameters(), [
            'curriculumid' => $curriculumid,
            'courseid' => $courseid,
            'newposition' => $newposition,
        ]);
        $curriculumid = $params['curriculumid'];
        $courseid = $params['courseid'];
        $newposition = $params['newposition'];

        $context = \context_system::instance();
        self::validate_context($context);
        require_capability('block/programcurriculum:manage', $context);

        $coursesrepo = new \block_programcurriculum\course_repository();
        $ordered = array_values($coursesrepo->get_by_curriculum($curriculumid));
        $orderedids = array_map(function ($item) {
            return (int)$item->id;
        }, $ordered);

        $position = array_search($courseid, $orderedids, true);
        if ($position === false || $newposition < 1) {
            return ['success' => false];
        }

        $target = max(0, min(count($orderedids) - 1, $newposition - 1));
        if ($target !== $position) {
            $movedid = $orderedids[$position];
            array_splice($orderedids, $position, 1);
            array_splice($orderedids, $target, 0, [$movedid]);

            $orderedrecords = [];
            foreach ($ordered as $r) {
                $orderedrecords[(int)$r->id] = $r;
            }
            $neworder = [];
            foreach ($orderedids as $id) {
                $neworder[] = $orderedrecords[$id];
            }

            foreach ($neworder as $index => $rec) {
                $coursesrepo->set_sortorder((int)$rec->id, $index + 1);
                $newterm = $index > 0
                    ? (int)($neworder[$index - 1]->term ?? 1)
                    : (count($neworder) > 1 ? (int)($neworder[1]->term ?? 1) : (int)($rec->term ?? 1));
                if ((int)$rec->term !== $newterm) {
                    $DB->set_field('block_programcurriculum_course', 'term', $newterm, ['id' => $rec->id]);
                }
            }
        }

        return ['success' => true];
    }

    /**
     * Returns description of method result value.
     *
     * @return \external_single_structure
     */
    public static function execute_returns(): \external_single_structure {
        return new \external_single_structure([
            'success' => new \external_value(PARAM_BOOL, 'Whether the reorder succeeded'),
        ]);
    }
}
