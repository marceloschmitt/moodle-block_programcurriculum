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

namespace block_programcurriculum\external;

defined('MOODLE_INTERNAL') || die();

require_once($GLOBALS['CFG']->libdir . '/externallib.php');

/**
 * External API for toggling student self-declared completion of an external course.
 *
 * @package    block_programcurriculum
 * @copyright  2025
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class toggle_user_completion extends \external_api {

    /**
     * Returns description of method parameters.
     *
     * @return \external_function_parameters
     */
    public static function execute_parameters(): \external_function_parameters {
        return new \external_function_parameters([
            'courseid' => new \external_value(PARAM_INT, 'Moodle course ID (for context)'),
            'externalcourseid' => new \external_value(PARAM_INT, 'External course ID (block_programcurriculum_course)'),
            'completed' => new \external_value(PARAM_BOOL, 'True to mark as completed, false to unmark'),
        ]);
    }

    /**
     * Toggle the current user's self-declared completion for an external course.
     *
     * @param int $courseid Moodle course ID
     * @param int $externalcourseid External course ID
     * @param bool $completed New completed state
     * @return array New state and success
     */
    public static function execute(int $courseid, int $externalcourseid, bool $completed): array {
        global $USER;

        $params = self::validate_parameters(self::execute_parameters(), [
            'courseid' => $courseid,
            'externalcourseid' => $externalcourseid,
            'completed' => $completed,
        ]);
        $courseid = $params['courseid'];
        $externalcourseid = $params['externalcourseid'];
        $completed = $params['completed'];

        $context = \context_course::instance($courseid);
        self::validate_context($context);
        require_capability('block/programcurriculum:viewownprogress', $context);

        global $DB;
        $course = $DB->get_record('block_programcurriculum_course', ['id' => $externalcourseid], 'id, curriculumid');
        if (!$course) {
            return ['success' => false, 'completed' => false];
        }

        $repo = new \block_programcurriculum\user_completion_repository();
        $repo->set_completed((int)$USER->id, $externalcourseid, $completed);

        return [
            'success' => true,
            'completed' => $completed,
        ];
    }

    /**
     * Returns description of method result value.
     *
     * @return \external_single_structure
     */
    public static function execute_returns(): \external_single_structure {
        return new \external_single_structure([
            'success' => new \external_value(PARAM_BOOL, 'Whether the operation succeeded'),
            'completed' => new \external_value(PARAM_BOOL, 'Current completed state'),
        ]);
    }
}
