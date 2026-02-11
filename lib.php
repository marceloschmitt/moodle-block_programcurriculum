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

/**
 * Extends the course navigation with a link to curriculum progress.
 *
 * Users with viewownprogress see a link to the progress page (view.php redirects
 * to their progress or to the students list if they have viewallprogress).
 *
 * @param navigation_node $parentnode The course node in the navigation tree.
 * @param stdClass $course The course object.
 * @param context_course $context The course context.
 */
function block_programcurriculum_extend_navigation_course(
    navigation_node $parentnode,
    stdClass $course,
    context_course $context
): void {
    if (!has_capability('block/programcurriculum:viewownprogress', $context)) {
        return;
    }

    $url = new moodle_url('/blocks/programcurriculum/view.php', [
        'courseid' => (int) $course->id,
    ]);

    $parentnode->add(
        get_string('viewprogress', 'block_programcurriculum'),
        $url,
        navigation_node::TYPE_SETTING,
        null,
        'programcurriculumprogress',
        new pix_icon('i/report', '')
    );
}
