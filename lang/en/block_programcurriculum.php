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

defined('MOODLE_INTERNAL') || die();

$string['addcourse'] = 'Add course';
$string['addcurriculum'] = 'Add program';
$string['addmapping'] = 'Add mapping';
$string['assistedmapping'] = 'Assisted mapping';
$string['assistedmapping_confirm'] = 'Confirm selected mappings';
$string['assistedmapping_help'] = 'For each external course, Moodle courses whose name contains the external course name or code are listed. Select the mappings you want to create and click Confirm. Already mapped courses are not shown.';
$string['assistedmapping_nosuggestions'] = 'No Moodle courses found containing this name or code, or all matches are already mapped.';
$string['assistedmapping_select'] = 'Select the Moodle courses to map:';
$string['assistedmappingdone'] = '{$a} mapping(s) created.';
$string['automaticmapping'] = 'Automatic mapping';
$string['automaticmappingdone'] = 'Automatic mapping completed. {$a} mapping(s) created.';
$string['automaticmappingdonecurriculum'] = 'Automatic mapping completed. {$a} mapping(s) created for the curriculum courses.';
$string['automaticmappingnocode'] = 'External course has no code. Cannot run automatic mapping.';
$string['backtocourses'] = 'Back to courses';
$string['backtoprograms'] = 'Back to programs';
$string['backtostudents'] = 'Back to student list';
$string['blockconfiginfo'] = 'This block provides curriculum mapping and progress views.';
$string['choosecurriculum'] = 'Choose curriculum';
$string['clickstudentprogress'] = 'Click on any student to see the progress.';
$string['close'] = 'Close';
$string['completed'] = 'Completed';
$string['completionstatus'] = 'Completion status';
$string['coursecode'] = 'Course code';
$string['coursedeleted'] = 'Course deleted.';
$string['coursedeletemappings'] = 'You cannot delete a course that has mappings.';
$string['courseformerror'] = 'Please fix the errors in the course form.';
$string['courseidrequired'] = 'Course ID is required to view progress.';
$string['coursename'] = 'Course name';
$string['courseplaceholder'] = 'Type to search for a course';
$string['courses'] = 'Courses';
$string['csvfile'] = 'CSV file';
$string['currentdisciplinescount'] = 'Number of current courses:';
$string['currentenrollment'] = 'Current enrollment';
$string['currentsubscriptions'] = 'Current subscriptions';
$string['curricula'] = 'Curricula';
$string['curriculumcode'] = 'Program code';
$string['curriculumdeletecourses'] = 'Cannot delete a program that has courses. Remove all courses first.';
$string['curriculumdeleted'] = 'Program deleted.';
$string['curriculumdescription'] = 'Description';
$string['curriculumformerror'] = 'Please fix the errors in the curriculum form.';
$string['curriculumname'] = 'Program name';
$string['curriculumview_noprogram'] = 'This course is not linked to any program.';
$string['deleteallcourses'] = 'Delete all courses';
$string['deleteallcoursesconfirm'] = 'This will delete all courses of "{$a}". Mappings will also be removed. This action cannot be undone.';
$string['deleteallcoursesdone'] = 'All courses of the program have been deleted.';
$string['deleteallcoursestitle'] = 'Delete all courses of this program?';
$string['deletecoursebody'] = 'This will delete "{$a}".';
$string['deletecourseconfirm'] = 'Delete this course?';
$string['deletecoursetitle'] = 'Delete course?';
$string['deletecurriculumbody'] = 'This will delete "{$a}".';
$string['deletecurriculumconfirm'] = 'Delete this program?';
$string['deletecurriculumtitle'] = 'Delete program?';
$string['deletemappingbody'] = 'This will delete "{$a}".';
$string['deletemappingconfirm'] = 'Delete this mapping?';
$string['deletemappingtitle'] = 'Delete mapping?';
$string['duplicatecoursecode'] = 'This course code is already in use.';
$string['duplicatecurriculumcode'] = 'A program with this code already exists. Please choose another program code.';
$string['duplicatecurriculumname'] = 'A program with this name already exists. Please choose another program name.';
$string['duplicatecurriculumnameorcode'] = 'The program name or code already exists. Please choose different values.';
$string['duplicatemapping'] = 'This Moodle course is already mapped to this external course.';
$string['editcourse'] = 'Edit course';
$string['editcurriculum'] = 'Edit curriculum';
$string['editmapping'] = 'Edit requirement';
$string['enrolled'] = 'Enrolled';
$string['enrolleddisciplinescount'] = '{$a} course(s)';
$string['equivalencecode'] = 'Equivalence code';
$string['equivalencecode_help'] = 'Alternative code for this course. Automatic mapping can use both the original code and the equivalence code to find Moodle courses. Optional.';
$string['externalcourse'] = 'External course';
$string['import_course_exists'] = 'Course already exists (code: {$a}). Import of this program skipped.';
$string['import_curriculum_exists'] = 'Program already exists (code: {$a}). Import of this program skipped.';
$string['import_do'] = 'Import to database';
$string['import_success'] = 'Import completed. {$a} program(s) imported.';
$string['importcsv'] = 'Import CSV';
$string['importcsv_invalidheader'] = 'Invalid CSV header.';
$string['importcsv_missingcoursefields'] = 'Line {$a}: Missing course fields.';
$string['importcsv_missingcurriculumfields'] = 'Line {$a}: Missing curriculum fields.';
$string['importcsv_readerror'] = 'Unable to read CSV file.';
$string['importerrors'] = 'Import errors';
$string['importhelp'] = 'CSV columns: curriculum_code, curriculum_name, course_code, course_name, moodle_course_id, sortorder, curriculum_description. Optional: numterms, term, equivalence_code';
$string['importsuccess'] = 'CSV import completed.';
$string['importtext_content'] = 'File content';
$string['importtext_empty'] = 'The uploaded file is empty.';
$string['importtext_file'] = 'Or upload a file';
$string['importtext_file_help'] = 'Upload a .txt (or .csv) file in the same format: program name, then semester and courses per term.';
$string['importtext_format'] = 'Import file format';
$string['importtext_format_help'] = 'One line with the program name; then for each term: one line with the semester (e.g. 1 or 1st Semester) and the following lines with each course name. Repeat semester and courses for the next terms.';
$string['importtext_format_short'] = 'Format: first line = program name; then for each term: one line with the semester (e.g. 1 or 1st Semester) and the next lines with the courses for that term.';
$string['importtext_nofile'] = 'Please upload a .txt or .csv file.';
$string['importtext_noprogramname'] = 'The first line (program name) is empty.';
$string['importtext_noprograms'] = 'No programs found. The first line should be the program name.';
$string['importtext_nosemesters'] = 'No terms found. Use lines with a number (e.g. 1, 2) or the word "semester".';
$string['importtext_preview'] = 'Preview';
$string['importtext_preview_nosave'] = 'No data was saved. This is preview only.';
$string['importtext_preview_title'] = 'Import preview';
$string['importtext_semester_before_program'] = 'Line {$a}: expected program name before a semester (or separate programs with blank lines).';
$string['importtext_semester_first'] = 'Line {$a}: expected a semester line before courses.';
$string['importtext_terms_count'] = 'Terms';
$string['importtitle'] = 'Import curriculum CSV';
$string['invalidnumterms'] = 'Number of terms must be at least 1.';
$string['listofcourses'] = 'List of courses';
$string['listofmappings'] = 'List of mappings';
$string['listofmappingsforcourse'] = 'List of mappings - {$a}';
$string['listofprograms'] = 'List of programs';
$string['listofstudents'] = 'List of students';
$string['managecurricula'] = 'Manage curricula';
$string['manualmapping'] = 'Manual mapping';
$string['mappedcourse'] = 'Mapped course';
$string['mappedmoodlecourse'] = 'Mapped Moodle course';
$string['mappingdeleted'] = 'Mapping deleted.';
$string['mappingformerror'] = 'Please fix the errors in the mapping form.';
$string['mappingofcourse'] = 'Mapping of the external course {$a}';
$string['mappings'] = 'Mappings';
$string['markascompleted'] = 'Mark as completed';
$string['markdisciplinescompleted'] = 'Mark the courses you have completed';
$string['markedcompleted'] = 'Completed';
$string['moodlecourse_deleted'] = 'Moodle course deleted (ID: {$a})';
$string['moodlecoursesforexternal'] = 'Moodle courses for {$a}';
$string['move'] = 'Move';
$string['movemodaltitle'] = 'Move course';
$string['movemodaltitlewithname'] = 'Move course: {$a}';
$string['movepositionclick'] = 'Click on a dashed line to move to that position.';
$string['movepositionhelp'] = 'Enter a position from 1 to {$a}.';
$string['movepositioninvalid'] = 'Position must be between 1 and the last item.';
$string['moveto'] = 'Move to position';
$string['nocapability'] = 'You do not have permission to view this block.';
$string['nocourses'] = 'No courses found.';
$string['nocurricula'] = 'No curricula found.';
$string['nomappings'] = 'No mappings found.';
$string['nostudents'] = 'No students found in this course.';
$string['notcompleted'] = 'Not completed';
$string['notenrolled'] = 'Not enrolled';
$string['numterms'] = 'Number of terms';
$string['numterms_help'] = 'How many terms (periods) does this program have? Each course will be assigned to one term.';
$string['pageheading'] = 'Program Curriculum';
$string['pluginname'] = 'Program curriculum';
$string['privacy:metadata:block_programcurriculum_user_completion'] = 'Stores user self-declared completion of external curriculum courses.';
$string['privacy:metadata:block_programcurriculum_user_completion:courseid'] = 'The external course ID marked as completed.';
$string['privacy:metadata:block_programcurriculum_user_completion:timemodified'] = 'The time when the completion was last modified.';
$string['privacy:metadata:block_programcurriculum_user_completion:userid'] = 'The user ID.';
$string['privacy:path:usercompletion'] = 'User completed external courses';
$string['program'] = 'Program';
$string['programcurriculum:addinstance'] = 'Add a program curriculum block';
$string['programcurriculum:import'] = 'Import curriculum CSV';
$string['programcurriculum:manage'] = 'Manage curricula';
$string['programcurriculum:myaddinstance'] = 'Add a program curriculum block to Dashboard';
$string['programcurriculum:viewallprogress'] = 'View all students\' curriculum progress';
$string['programcurriculum:viewownprogress'] = 'View own curriculum progress';
$string['progress_intro'] = 'This page shows your progress in {$a}. The first indicator depends on the courses you mark as completed; the second is automatic (courses you have taken). The colors indicate: enrolled in active course, took in a previous term, or completed. Remember to mark the courses you have completed to track your progress.';
$string['progress_optional_notincluded'] = 'Optional courses and complementary activities are not included in the progress calculation.';
$string['progressbycompletion'] = 'by completion';
$string['progressbycompletion_header'] = 'Progress by completion';
$string['progressbyenrollment'] = 'by enrollment';
$string['progressbyenrollment_header'] = 'Progress by enrollment';
$string['progresslegend'] = 'Legend:';
$string['progresslegend_active'] = 'Enrolled in active course';
$string['progresslegend_completed'] = 'Completed';
$string['progresslegend_ended'] = 'Enrolled in ended course';
$string['progresspercent'] = 'Progress';
$string['progressview'] = 'Progress View';
$string['sortorder'] = 'Sort order';
$string['student'] = 'Student';
$string['studentprogress'] = 'Student progress';
$string['term'] = 'Term';
$string['term_help'] = 'Which term (period) does this course belong to?';
$string['thisprogram'] = 'this program';
$string['upload'] = 'Upload';
$string['viewcurriculum'] = 'View curriculum';
$string['viewprogress'] = 'View progress';
$string['viewstudentprogress'] = 'View progress';
$string['viewtitle'] = 'Curriculum progress';
