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

require_once(__DIR__ . '/../../config.php');

require_login();

$context = context_system::instance();
require_capability('block/programcurriculum:manage', $context);

$curriculumid = required_param('curriculumid', PARAM_INT);
$id = optional_param('id', 0, PARAM_INT);

$curriculumrepo = new \block_programcurriculum\curriculum_repository();
$curriculum = $curriculumrepo->get($curriculumid);
if (!$curriculum) {
    throw new moodle_exception('invalidrecord', 'error');
}

$PAGE->set_context($context);
$PAGE->set_url('/blocks/programcurriculum/course.php', ['curriculumid' => $curriculumid, 'id' => $id]);
$PAGE->set_title(get_string('courses', 'block_programcurriculum'));
$PAGE->set_heading(get_string('pageheading', 'block_programcurriculum'));
$PAGE->requires->js_call_amd('block_programcurriculum/course_actions', 'init');

$coursesrepo = new \block_programcurriculum\course_repository();
$course = $id ? $coursesrepo->get($id) : null;

$mappingrepo = new \block_programcurriculum\mapping_repository();

$action = optional_param('action', '', PARAM_ALPHA);
if ($action === 'delete' && $id) {
    require_sesskey();
    if (!$course || (int)$course->curriculumid !== (int)$curriculumid) {
        throw new moodle_exception('invalidrecord', 'error');
    }
    if ($mappingrepo->has_for_course($course->id)) {
        redirect(
            new moodle_url('/blocks/programcurriculum/course.php', ['curriculumid' => $curriculumid]),
            get_string('coursedeletemappings', 'block_programcurriculum'),
            null,
            \core\output\notification::NOTIFY_ERROR
        );
    }
    $coursesrepo->delete($course->id);
    redirect(
        new moodle_url('/blocks/programcurriculum/course.php', ['curriculumid' => $curriculumid]),
        get_string('coursedeleted', 'block_programcurriculum'),
        null,
        \core\output\notification::NOTIFY_SUCCESS
    );
}

if ($action === 'automatic') {
    require_sesskey();
    $courselist = array_values($coursesrepo->get_by_curriculum($curriculumid));
    $created = 0;
    foreach ($courselist as $item) {
        $created += $mappingrepo->run_automatic_mapping((int)$item->id, $item->externalcode ?? '');
    }
    redirect(
        new moodle_url('/blocks/programcurriculum/course.php', ['curriculumid' => $curriculumid]),
        get_string('automaticmappingdonecurriculum', 'block_programcurriculum', $created),
        null,
        \core\output\notification::NOTIFY_SUCCESS
    );
}

if ($action === 'move' && $id) {
    require_sesskey();
    $ordered = array_values($coursesrepo->get_by_curriculum($curriculumid));
    $orderedids = array_map(function ($item) {
        return (int)$item->id;
    }, $ordered);
    $position = array_search((int)$id, $orderedids, true);
    $newposition = optional_param('position', 0, PARAM_INT);
    if ($position !== false && $newposition > 0) {
        $target = max(1, min(count($orderedids), $newposition)) - 1;
        if ($target !== $position) {
            $movedid = $orderedids[$position];
            array_splice($orderedids, $position, 1);
            array_splice($orderedids, $target, 0, [$movedid]);
        }
        foreach ($orderedids as $index => $courseid) {
            $coursesrepo->set_sortorder($courseid, $index + 1);
        }
    }
    redirect(new moodle_url('/blocks/programcurriculum/course.php', ['curriculumid' => $curriculumid]));
}

$numterms = (int)($curriculum->numterms ?? 1);
$numterms = max(1, $numterms);
$mform = new \block_programcurriculum\form\course_form(null, ['numterms' => $numterms]);
if ($course) {
    $mform->set_data($course);
} else {
    $mform->set_data((object)['curriculumid' => $curriculumid, 'term' => 1]);
}

if ($mform->is_cancelled()) {
    redirect(new moodle_url('/blocks/programcurriculum/manage.php'));
}

if ($data = $mform->get_data()) {
    $coursesrepo->upsert($data);
    redirect(new moodle_url('/blocks/programcurriculum/course.php', ['curriculumid' => $curriculumid]));
}

$validationerror = $mform->is_submitted() && !$mform->is_cancelled() && !$mform->is_validated();

$validationmessage = optional_param('validationmessage', '', PARAM_TEXT);

if ($validationerror) {
    // Force validation to populate errors.
    $submitteddata = $mform->get_submitted_data();
    $files = [];
    $errors = $mform->validation((array)$submitteddata, $files);
    
    $hasduplicatecode = !empty($errors['externalcode']);
    if ($hasduplicatecode) {
        $validationmessage = get_string('duplicatecoursecode', 'block_programcurriculum');
    } else {
        $validationmessage = get_string('courseformerror', 'block_programcurriculum');
    }
    
    // Redirect to clear the form and show only the error message.
    redirect(new moodle_url('/blocks/programcurriculum/course.php', [
        'curriculumid' => $curriculumid,
        'validationmessage' => $validationmessage
    ]));
}

$courselist = array_values($coursesrepo->get_by_curriculum($curriculumid));
$total = count($courselist);
$courseids = array_map(function ($item) {
    return (int)$item->id;
}, $courselist);
$mappingcounts = $mappingrepo->get_counts_by_course_ids($courseids);

$termsmap = [];
foreach ($courselist as $index => $item) {
    $mappingcount = (int)($mappingcounts[$item->id] ?? 0);
    $hasmappings = $mappingcount > 0;
    $term = (int)($item->term ?? 1);
    if (!isset($termsmap[$term])) {
        $termsmap[$term] = [];
    }
    $termsmap[$term][] = [
        'id' => $item->id,
        'name' => $item->name,
        'externalcode' => $item->externalcode,
        'sortorder' => $item->sortorder,
        'curriculumid' => $curriculumid,
        'editurl' => (new moodle_url('/blocks/programcurriculum/course.php', [
            'curriculumid' => $curriculumid,
            'id' => $item->id,
        ]))->out(false),
        'mappingurl' => (new moodle_url('/blocks/programcurriculum/mapping.php', [
            'courseid' => $item->id,
        ]))->out(false),
        'moveactionurl' => (new moodle_url('/blocks/programcurriculum/course.php', [
            'curriculumid' => $curriculumid,
            'id' => $item->id,
            'action' => 'move',
            'sesskey' => sesskey(),
        ]))->out(false),
        'position' => $index + 1,
        'totalpositions' => $total,
        'mappingcount' => $mappingcount,
        'editname' => $item->name,
        'editcode' => $item->externalcode,
        'editsortorder' => $item->sortorder,
        'editterm' => $term,
        'candelete' => !$hasmappings,
        'deleteurl' => !$hasmappings ? (new moodle_url('/blocks/programcurriculum/course.php', [
            'curriculumid' => $curriculumid,
            'id' => $item->id,
            'action' => 'delete',
            'sesskey' => sesskey(),
        ]))->out(false) : null,
        'deleteconfirm' => get_string('deletecourseconfirm', 'block_programcurriculum'),
    ];
}
ksort($termsmap, SORT_NUMERIC);
$termsections = [];
foreach ($termsmap as $termnum => $courses) {
    $termsections[] = [
        'term' => $termnum,
        'courses' => $courses,
        'hascourses' => !empty($courses),
    ];
}

echo $OUTPUT->header();
echo $OUTPUT->render_from_template('block_programcurriculum/course', [
    'curriculumname' => $curriculum->name,
    'backurl' => (new moodle_url('/blocks/programcurriculum/manage.php'))->out(false),
    'automaticurl' => (new moodle_url('/blocks/programcurriculum/course.php', [
        'curriculumid' => $curriculumid,
        'action' => 'automatic',
        'sesskey' => sesskey(),
    ]))->out(false),
    'termsections' => $termsections,
    'hascourses' => !empty($courselist),
    'validationerror' => !empty($validationmessage),
    'validationmessage' => $validationmessage,
    'formhtml' => (function () use ($mform): string {
        ob_start();
        $mform->display();
        return (string)ob_get_clean();
    })(),
    'modaltitle' => $id ? get_string('editcourse', 'block_programcurriculum') : get_string('addcourse', 'block_programcurriculum'),
]);
echo $OUTPUT->footer();
