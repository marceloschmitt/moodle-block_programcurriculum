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
 * Privacy provider implementation for block_programcurriculum.
 *
 * @package    block_programcurriculum
 * @copyright  2026 Marcelo Augusto Rauh Schmitt <marcelo.schmitt@poa.ifrs.edu.br>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace block_programcurriculum\privacy;

defined('MOODLE_INTERNAL') || die();

use context;
use context_system;
use core_privacy\local\metadata\collection;
use core_privacy\local\request\approved_contextlist;
use core_privacy\local\request\approved_userlist;
use core_privacy\local\request\contextlist;
use core_privacy\local\request\plugin\provider as pluginprovider;
use core_privacy\local\request\plugin\userlist_provider;
use core_privacy\local\request\transform;
use core_privacy\local\request\userlist;
use core_privacy\local\request\writer;

/**
 * Privacy provider for block_programcurriculum.
 */
class provider implements
    \core_privacy\local\metadata\provider,
    pluginprovider,
    userlist_provider {

    /**
     * Returns metadata about this plugin's stored personal data.
     *
     * @param collection $collection The initial collection to add items to.
     * @return collection The updated collection.
     */
    public static function get_metadata(collection $collection): collection {
        $collection->add_database_table('block_programcurriculum_user_completion', [
            'userid' => 'privacy:metadata:block_programcurriculum_user_completion:userid',
            'courseid' => 'privacy:metadata:block_programcurriculum_user_completion:courseid',
            'timemodified' => 'privacy:metadata:block_programcurriculum_user_completion:timemodified',
        ], 'privacy:metadata:block_programcurriculum_user_completion');

        return $collection;
    }

    /**
     * Get contexts that contain user information for the specified user.
     *
     * @param int $userid The user to search.
     * @return contextlist The list of contexts containing user data.
     */
    public static function get_contexts_for_userid(int $userid): contextlist {
        global $DB;

        $contextlist = new contextlist();
        $hasdata = $DB->record_exists('block_programcurriculum_user_completion', ['userid' => $userid]);
        if ($hasdata) {
            $contextlist->add_context(context_system::instance());
        }

        return $contextlist;
    }

    /**
     * Export all user data for the specified contexts.
     *
     * @param approved_contextlist $contextlist The approved contexts.
     * @return void
     */
    public static function export_user_data(approved_contextlist $contextlist): void {
        global $DB;

        $userid = $contextlist->get_user()->id;
        $systemcontext = context_system::instance();
        if (!in_array($systemcontext->id, $contextlist->get_contextids(), true)) {
            return;
        }

        $sql = "SELECT uc.id, uc.userid, uc.courseid, uc.timemodified, c.name AS coursename, c.externalcode AS coursecode
                  FROM {block_programcurriculum_user_completion} uc
                  JOIN {block_programcurriculum_course} c ON c.id = uc.courseid
                 WHERE uc.userid = :userid
              ORDER BY uc.timemodified ASC";
        $records = $DB->get_records_sql($sql, ['userid' => $userid]);
        if (empty($records)) {
            return;
        }

        $data = [];
        foreach ($records as $record) {
            $data[] = (object)[
                'courseid' => (int)$record->courseid,
                'coursename' => $record->coursename,
                'coursecode' => $record->coursecode,
                'timemodified' => transform::datetime($record->timemodified),
            ];
        }

        writer::with_context($systemcontext)->export_data(
            [get_string('privacy:path:usercompletion', 'block_programcurriculum')],
            (object)['records' => $data]
        );
    }

    /**
     * Delete all user data for all users in a context.
     *
     * @param context $context The context to delete data for.
     * @return void
     */
    public static function delete_data_for_all_users_in_context(context $context): void {
        global $DB;

        if ($context->contextlevel !== CONTEXT_SYSTEM) {
            return;
        }

        $DB->delete_records('block_programcurriculum_user_completion', []);
    }

    /**
     * Delete all user data for one user in approved contexts.
     *
     * @param approved_contextlist $contextlist The approved contexts for user data removal.
     * @return void
     */
    public static function delete_data_for_user(approved_contextlist $contextlist): void {
        global $DB;

        $systemcontext = context_system::instance();
        if (!in_array($systemcontext->id, $contextlist->get_contextids(), true)) {
            return;
        }

        $userid = $contextlist->get_user()->id;
        $DB->delete_records('block_programcurriculum_user_completion', ['userid' => $userid]);
    }

    /**
     * Get users with data in the specified context.
     *
     * @param userlist $userlist The userlist object.
     * @return void
     */
    public static function get_users_in_context(userlist $userlist): void {
        if ($userlist->get_context()->contextlevel !== CONTEXT_SYSTEM) {
            return;
        }

        $userlist->add_from_sql('userid', "SELECT userid FROM {block_programcurriculum_user_completion}", []);
    }

    /**
     * Delete data for multiple users in a context.
     *
     * @param approved_userlist $userlist The approved list of users.
     * @return void
     */
    public static function delete_data_for_users(approved_userlist $userlist): void {
        global $DB;

        if ($userlist->get_context()->contextlevel !== CONTEXT_SYSTEM) {
            return;
        }

        $userids = $userlist->get_userids();
        if (empty($userids)) {
            return;
        }

        [$insql, $params] = $DB->get_in_or_equal($userids, SQL_PARAMS_NAMED);
        $DB->delete_records_select('block_programcurriculum_user_completion', "userid {$insql}", $params);
    }
}
