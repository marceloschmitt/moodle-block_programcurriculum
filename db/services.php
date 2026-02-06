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

$functions = [
    'block_programcurriculum_reorder_courses' => [
        'classname'   => 'block_programcurriculum\external\reorder_courses',
        'methodname'  => 'execute',
        'description' => 'Reorder a curriculum course to a new position.',
        'type'        => 'write',
        'ajax'        => true,
        'capabilities' => 'block/programcurriculum:manage',
        'services'    => [MOODLE_OFFICIAL_MOBILE_SERVICE],
    ],
];
