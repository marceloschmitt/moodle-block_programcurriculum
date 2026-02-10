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
        // Allow the block to be added and shown on all pages (configurable per instance).
        return ['all' => true];
    }

    public function get_content(): ?stdClass {
        if ($this->content !== null) {
            return $this->content;
        }

        if (empty($this->instance)) {
            return null;
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

        if (empty($items)) {
            return null;
        }

        $this->content = new stdClass();
        $this->content->text = '';
        $this->content->footer = '';

        $wheelshtml = '';
        if ($courseid > 0 && has_capability('block/programcurriculum:viewownprogress', $coursecontext)) {
            $mappingrepo = new \block_programcurriculum\mapping_repository();
            $coursemappings = $mappingrepo->get_by_moodle_course($courseid);
            $firstmapping = !empty($coursemappings) ? reset($coursemappings) : null;
            $curriculumid = $firstmapping ? (int)$firstmapping->curriculumid : 0;
            if ($curriculumid > 0) {
                global $USER;
                $calculator = new \block_programcurriculum\progress_calculator();
                $progress = $calculator->calculate_for_user((int)$USER->id, $curriculumid);
                $enrollmentpercent = $progress['total'] > 0
                    ? (int)round(($progress['enrolled'] / $progress['total']) * 100) : 0;
                $percent = $progress['percent'];
                $total = $progress['total'];
                $completed = $progress['completed'];
                $enrolleddisciplines = $progress['enrolled'];
                $this->page->requires->css('/blocks/programcurriculum/styles.css');
                $wheelshtml = '<div class="programcurriculum-block-wheels programcurriculum-progress-wheels d-flex flex-wrap gap-2 mb-2">';
                $wheelshtml .= '<div class="programcurriculum-progress-wheel" role="progressbar" aria-valuenow="' . (int)$percent . '" aria-valuemin="0" aria-valuemax="100" title="' . s(get_string('progressbycompletion', 'block_programcurriculum') . ': ' . $percent . '% (' . $completed . '/' . $total . ')') . '">';
                $wheelshtml .= '<div class="programcurriculum-progress-wheel-circle" style="--p: ' . (int)$percent . ';"><span class="programcurriculum-progress-wheel-value">' . (int)$percent . '%</span></div>';
                $wheelshtml .= '<div class="programcurriculum-progress-wheel-label small fw-bold mt-1">' . s(get_string('progressbycompletion_header', 'block_programcurriculum')) . '</div>';
                $wheelshtml .= '<div class="programcurriculum-progress-wheel-detail small text-muted">' . (int)$completed . '/' . (int)$total . '</div></div>';
                $wheelshtml .= '<div class="programcurriculum-progress-wheel programcurriculum-progress-wheel--enrollment" role="progressbar" aria-valuenow="' . (int)$enrollmentpercent . '" aria-valuemin="0" aria-valuemax="100" title="' . s(get_string('progressbyenrollment', 'block_programcurriculum') . ': ' . $enrollmentpercent . '% (' . $enrolleddisciplines . '/' . $total . ')') . '">';
                $wheelshtml .= '<div class="programcurriculum-progress-wheel-circle" style="--p: ' . (int)$enrollmentpercent . ';"><span class="programcurriculum-progress-wheel-value">' . (int)$enrollmentpercent . '%</span></div>';
                $wheelshtml .= '<div class="programcurriculum-progress-wheel-label small fw-bold mt-1">' . s(get_string('progressbyenrollment_header', 'block_programcurriculum')) . '</div>';
                $wheelshtml .= '<div class="programcurriculum-progress-wheel-detail small text-muted">' . (int)$enrolleddisciplines . '/' . (int)$total . '</div></div>';
                $wheelshtml .= '</div>';
            }
        }

        usort($items, function ($a, $b) {
            return strcoll($a['text'], $b['text']);
        });
        $links = array_map(function ($item) {
            return html_writer::link($item['url'], $item['text']);
        }, $items);
        $this->content->text = $wheelshtml . html_writer::alist($links, ['class' => 'programcurriculum-links']);
        return $this->content;
    }
}
