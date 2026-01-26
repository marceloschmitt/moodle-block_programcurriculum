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

        $context = $this->context;
        $courseid = (int)($this->page->course->id ?? 0);

        $links = [];
        if ($courseid > 0 && has_capability('block/programcurriculum:viewprogress', $context)) {
            $url = new moodle_url('/blocks/programcurriculum/view.php', ['courseid' => $courseid]);
            $links[] = html_writer::link($url, get_string('viewprogress', 'block_programcurriculum'));
        }

        if (has_capability('block/programcurriculum:manage', context_system::instance())) {
            $url = new moodle_url('/blocks/programcurriculum/manage.php');
            $links[] = html_writer::link($url, get_string('managecurricula', 'block_programcurriculum'));
        }

        if (has_capability('block/programcurriculum:import', context_system::instance())) {
            $url = new moodle_url('/blocks/programcurriculum/import.php');
            $links[] = html_writer::link($url, get_string('importcsv', 'block_programcurriculum'));
        }

        if (!empty($links)) {
            $this->content->text = html_writer::alist($links, ['class' => 'programcurriculum-links']);
        } else {
            $this->content->text = get_string('nocapability', 'block_programcurriculum');
        }

        return $this->content;
    }
}
