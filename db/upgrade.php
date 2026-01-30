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

defined('MOODLE_INTERNAL') || die();

function xmldb_block_programcurriculum_upgrade(int $oldversion): bool {
    global $DB;

    $dbman = $DB->get_manager();

    if ($oldversion < 2026012834) {
        $table = new xmldb_table('block_programcurriculum_mapping');
        $oldkey = new xmldb_key('disciplineid', XMLDB_KEY_FOREIGN, ['disciplineid'], 'block_programcurriculum_discipline', ['id']);
        if ($dbman->table_exists($table)) {
            try {
                $dbman->drop_key($table, $oldkey);
            } catch (\ddl_exception $e) {
                // Key already removed or never existed.
            }
        }

        $oldindex = new xmldb_index('disciplineid', XMLDB_INDEX_NOTUNIQUE, ['disciplineid']);
        if ($dbman->table_exists($table) && $dbman->index_exists($table, $oldindex)) {
            $dbman->drop_index($table, $oldindex);
        }

        $oldindex = new xmldb_index('courseid', XMLDB_INDEX_NOTUNIQUE, ['courseid']);
        if ($dbman->table_exists($table) && $dbman->index_exists($table, $oldindex)) {
            $dbman->drop_index($table, $oldindex);
        }

        $field = new xmldb_field('courseid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0');
        if ($dbman->table_exists($table) && $dbman->field_exists($table, $field)) {
            $dbman->rename_field($table, $field, 'moodlecourseid');
        }

        $field = new xmldb_field('disciplineid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0');
        if ($dbman->table_exists($table) && $dbman->field_exists($table, $field)) {
            $dbman->rename_field($table, $field, 'courseid');
        }

        $table = new xmldb_table('block_programcurriculum_discipline');
        if ($dbman->table_exists($table)) {
            $dbman->rename_table($table, 'block_programcurriculum_course');
        }

        $table = new xmldb_table('block_programcurriculum_mapping');
        $key = new xmldb_key('courseid', XMLDB_KEY_FOREIGN, ['courseid'], 'block_programcurriculum_course', ['id']);
        if ($dbman->table_exists($table)) {
            try {
                $dbman->add_key($table, $key);
            } catch (\ddl_exception $e) {
                // Key already exists or cannot be added.
            }
        }

        $index = new xmldb_index('courseid', XMLDB_INDEX_NOTUNIQUE, ['courseid']);
        if ($dbman->table_exists($table) && !$dbman->index_exists($table, $index)) {
            $dbman->add_index($table, $index);
        }

        $index = new xmldb_index('moodlecourseid', XMLDB_INDEX_NOTUNIQUE, ['moodlecourseid']);
        if ($dbman->table_exists($table) && !$dbman->index_exists($table, $index)) {
            $dbman->add_index($table, $index);
        }

        upgrade_block_savepoint(true, 2026012834, 'programcurriculum');
    }

    if ($oldversion < 2026012845) {
        $table = new xmldb_table('block_programcurriculum_mapping');
        $field = new xmldb_field('required');
        if ($dbman->table_exists($table) && $dbman->field_exists($table, $field)) {
            $dbman->drop_field($table, $field);
        }

        upgrade_block_savepoint(true, 2026012845, 'programcurriculum');
    }

    if ($oldversion < 2026012846) {
        $table = new xmldb_table('block_programcurriculum_mapping');
        $index = new xmldb_index('course_moodlecourse', XMLDB_INDEX_UNIQUE, ['courseid', 'moodlecourseid']);
        if ($dbman->table_exists($table) && !$dbman->index_exists($table, $index)) {
            $dbman->add_index($table, $index);
        }

        upgrade_block_savepoint(true, 2026012846, 'programcurriculum');
    }

    if ($oldversion < 2026013002) {
        $table = new xmldb_table('block_programcurriculum_curriculum');
        $index = new xmldb_index('name', XMLDB_INDEX_UNIQUE, ['name']);
        if ($dbman->table_exists($table) && !$dbman->index_exists($table, $index)) {
            try {
                $dbman->add_index($table, $index);
            } catch (\ddl_exception $e) {
                // Index may already exist or duplicate names prevent it.
            }
        }

        upgrade_block_savepoint(true, 2026013002, 'programcurriculum');
    }

    return true;
}
