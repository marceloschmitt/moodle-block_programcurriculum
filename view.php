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
 * Plugin file for block_programcurriculum.
 *
 * @package    block_programcurriculum
 * @copyright  2026 Marcelo Augusto Rauh Schmitt <marcelo.schmitt@poa.ifrs.edu.br>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(__DIR__ . '/../../config.php');
require_once($CFG->libdir . '/completionlib.php');

require_login();

$courseid = required_param('courseid', PARAM_INT);
$course = get_course($courseid);

$context = context_course::instance($courseid);
require_capability('block/programcurriculum:viewownprogress', $context);

$canviewall = has_capability('block/programcurriculum:viewallprogress', $context);

$mappingrepo = new \block_programcurriculum\mapping_repository();
$coursemappings = $mappingrepo->get_by_moodle_course($courseid);
$firstmapping = !empty($coursemappings) ? reset($coursemappings) : null;
$curriculumid = $firstmapping ? (int)$firstmapping->curriculumid : 0;

if (!$canviewall) {
    if ($curriculumid <= 0) {
        redirect(new moodle_url('/course/view.php', ['id' => $courseid]));
    }
    redirect(new moodle_url('/blocks/programcurriculum/progress.php', [
        'courseid' => $courseid,
        'userid' => $USER->id,
        'curriculumid' => $curriculumid,
    ]));
}

$data = [
    'hascurricula' => $curriculumid > 0,
    'courseid' => $courseid,
    'curriculumid' => $curriculumid,
    'programname' => $firstmapping ? $firstmapping->programname : '',
    'externalcoursename' => $firstmapping ? $firstmapping->externalcoursename : '',
    'hassubtitle' => $firstmapping && !empty($firstmapping->programname) && !empty($firstmapping->externalcoursename),
    'students' => [],
    'hasstudents' => false,
];

$calculator = $curriculumid > 0 ? new \block_programcurriculum\progress_calculator() : null;
$namefields = 'u.id, u.firstname, u.lastname, u.firstnamephonetic, u.lastnamephonetic, u.middlename, u.alternatename';
$users = get_enrolled_users($context, 'moodle/course:isincompletionreports', 0, $namefields, 'u.lastname ASC, u.firstname ASC');
foreach ($users as $u) {
    $url = new moodle_url('/blocks/programcurriculum/progress.php', [
        'courseid' => $courseid,
        'userid' => $u->id,
        'curriculumid' => $curriculumid,
    ]);
    $student = [
        'id' => $u->id,
        'fullname' => fullname($u),
        'viewurl' => $url->out(false),
    ];
    if ($calculator && $curriculumid > 0) {
        $progress = $calculator->calculate_for_user((int)$u->id, $curriculumid);
        $student['progresspercent'] = $progress['percent'];
        $student['progresscompleted'] = $progress['completed'];
        $student['progresstotal'] = $progress['total'];
        $student['enrolledcount'] = $progress['enrolled_active'];
        $student['enrollmentpercent'] = $progress['total'] > 0
            ? (int)round(($progress['enrolled'] / $progress['total']) * 100) : 0;
    } else {
        $student['progresspercent'] = 0;
        $student['progresscompleted'] = 0;
        $student['progresstotal'] = 0;
        $student['enrolledcount'] = 0;
        $student['enrollmentpercent'] = 0;
    }
    $data['students'][] = $student;
}
usort($data['students'], function ($a, $b) {
    return strcoll($a['fullname'], $b['fullname']);
});
$data['hasstudents'] = !empty($data['students']);

$PAGE->set_context($context);
$PAGE->set_course($course);
$PAGE->set_pagelayout('incourse');
$PAGE->requires->css('/blocks/programcurriculum/styles.css');
$PAGE->set_secondary_navigation(false);
$PAGE->set_url('/blocks/programcurriculum/view.php', ['courseid' => $courseid]);
$PAGE->set_title(get_string('viewtitle', 'block_programcurriculum'));
$PAGE->set_heading(get_string('pageheading', 'block_programcurriculum'));
$PAGE->navbar->add($course->fullname, new moodle_url('/course/view.php', ['id' => $courseid]));
$PAGE->navbar->add(get_string('viewtitle', 'block_programcurriculum'));

echo $OUTPUT->header();
echo $OUTPUT->render_from_template('block_programcurriculum/students', $data);
echo $OUTPUT->footer();
