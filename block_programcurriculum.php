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

class block_programcurriculum extends block_base {
    public function init(): void {
        $this->title = get_string('pluginname', 'block_programcurriculum');
    }

    public function applicable_formats(): array {
        return [
            'site-index' => true,
            'course-view' => true,
            'my' => true,
            'mod' => false,
            'admin' => false,
        ];
    }

    public function get_content(): stdClass {
        if ($this->content !== null) {
            return $this->content;
        }

        $this->content = new stdClass();
        $this->content->text = '';
        $this->content->footer = '';

        if (empty($this->instance)) {
            return $this->content;
        }

        $courseid = (int)($this->page->course->id ?? 0);
        $coursecontext = $courseid > 0 ? context_course::instance($courseid) : null;
        $systemcontext = context_system::instance();

        $items = [];
        if ($coursecontext && has_capability('block/programcurriculum:viewownprogress', $coursecontext)) {
            $items[] = [
                'text' => get_string('viewcurriculum', 'block_programcurriculum'),
                'url' => new moodle_url('/blocks/programcurriculum/curriculumview.php', ['courseid' => $courseid]),
            ];
            $items[] = [
                'text' => get_string('viewprogress', 'block_programcurriculum'),
                'url' => new moodle_url('/blocks/programcurriculum/view.php', ['courseid' => $courseid]),
            ];
        }

        if (has_capability('block/programcurriculum:manage', $systemcontext)) {
            $items[] = [
                'text' => get_string('managecurricula', 'block_programcurriculum'),
                'url' => new moodle_url('/blocks/programcurriculum/manage.php'),
            ];
        }

        if (has_capability('block/programcurriculum:import', $systemcontext)) {
            $items[] = [
                'text' => get_string('importcsv', 'block_programcurriculum'),
                'url' => new moodle_url('/blocks/programcurriculum/import.php'),
            ];
        }

        if (!empty($items)) {
            usort($items, function ($a, $b) {
                return strcoll($a['text'], $b['text']);
            });
            $links = array_map(function ($item) {
                return html_writer::link($item['url'], $item['text']);
            }, $items);
            $this->content->text = html_writer::alist($links, ['class' => 'programcurriculum-links']);
        } else {
            $this->content->text = get_string('nocapability', 'block_programcurriculum');
        }

        return $this->content;
    }
}
