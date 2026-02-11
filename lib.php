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

    global $USER;

    $courseid = (int) $course->id;
    $canviewall = has_capability('block/programcurriculum:viewallprogress', $context);

    $mappingrepo = new \block_programcurriculum\mapping_repository();
    $coursemappings = $mappingrepo->get_by_moodle_course($courseid);
    $firstmapping = !empty($coursemappings) ? reset($coursemappings) : null;
    $curriculumid = $firstmapping ? (int) $firstmapping->curriculumid : 0;

    // Create parent "Currículo" with children, so it appears as a section in the nav.
    $curriculumnode = $parentnode->add(
        get_string('curriculumnav', 'block_programcurriculum'),
        null,
        navigation_node::TYPE_CONTAINER,
        null,
        'programcurriculum',
        new pix_icon('i/report', '')
    );
    $curriculumnode->showinflatnavigation = true;

    // Progress link: for students view.php redirects; for teachers link to own progress.
    if ($canviewall && $curriculumid > 0) {
        $progressurl = new moodle_url('/blocks/programcurriculum/progress.php', [
            'courseid' => $courseid,
            'userid' => $USER->id,
            'curriculumid' => $curriculumid,
        ]);
    } else {
        $progressurl = new moodle_url('/blocks/programcurriculum/view.php', ['courseid' => $courseid]);
    }
    $curriculumnode->add(
        get_string('viewprogress', 'block_programcurriculum'),
        $progressurl,
        navigation_node::TYPE_SETTING,
        null,
        'programcurriculumprogress',
        new pix_icon('i/report', '')
    );

    if ($canviewall) {
        $curriculumnode->add(
            get_string('listofstudents', 'block_programcurriculum'),
            new moodle_url('/blocks/programcurriculum/view.php', ['courseid' => $courseid]),
            navigation_node::TYPE_SETTING,
            null,
            'programcurriculumstudents',
            new pix_icon('i/group', '')
        );
    }
}
