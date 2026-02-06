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

$string['pluginname'] = 'Program curriculum';
$string['pageheading'] = 'Program Curriculum';
$string['studentprogress'] = 'Student progress';
$string['blockconfiginfo'] = 'This block provides curriculum mapping and progress views.';
$string['programcurriculum:addinstance'] = 'Add a program curriculum block';
$string['programcurriculum:myaddinstance'] = 'Add a program curriculum block to Dashboard';
$string['programcurriculum:manage'] = 'Manage curricula';
$string['programcurriculum:import'] = 'Import curriculum CSV';
$string['programcurriculum:viewownprogress'] = 'View own curriculum progress';
$string['programcurriculum:viewallprogress'] = 'View all students\' curriculum progress';
$string['managecurricula'] = 'Manage curricula';
$string['importcsv'] = 'Import CSV';
$string['viewprogress'] = 'View progress';
$string['viewcurriculum'] = 'View curriculum';
$string['nocapability'] = 'You do not have permission to view this block.';
$string['curricula'] = 'Curricula';
$string['listofprograms'] = 'List of programs';
$string['addcurriculum'] = 'Add program';
$string['editcurriculum'] = 'Edit curriculum';
$string['curriculumname'] = 'Program name';
$string['curriculumcode'] = 'Program code';
$string['duplicatecurriculumcode'] = 'A program with this code already exists. Please choose another program code.';
$string['duplicatecurriculumname'] = 'A program with this name already exists. Please choose another program name.';
$string['curriculumdescription'] = 'Description';
$string['numterms'] = 'Number of terms';
$string['numterms_help'] = 'How many terms (periods) does this program have? Each discipline will be assigned to one term.';
$string['invalidnumterms'] = 'Number of terms must be at least 1.';
$string['term'] = 'Term';
$string['term_help'] = 'Which term (period) does this discipline belong to?';
$string['courses'] = 'Courses';
$string['nocourses'] = 'No courses found.';
$string['addcourse'] = 'Add course';
$string['editcourse'] = 'Edit course';
$string['coursename'] = 'Course name';
$string['coursecode'] = 'Course code';
$string['equivalencecode'] = 'Equivalence code';
$string['equivalencecode_help'] = 'Alternative code for this course. Automatic mapping can use both the original code and the equivalence code to find Moodle courses. Optional.';
$string['duplicatecoursecode'] = 'This course code is already in use.';
$string['deletecoursebody'] = 'This will delete "{$a}".';
$string['deletecoursetitle'] = 'Delete course?';
$string['deletecourseconfirm'] = 'Delete this course?';
$string['coursedeleted'] = 'Course deleted.';
$string['coursedeletemappings'] = 'You cannot delete a course that has mappings.';
$string['courseformerror'] = 'Please fix the errors in the course form.';
$string['curriculumformerror'] = 'Please fix the errors in the curriculum form.';
$string['duplicatecurriculumnameorcode'] = 'The program name or code already exists. Please choose different values.';
$string['sortorder'] = 'Sort order';
$string['moveto'] = 'Move to position';
$string['movemodaltitle'] = 'Move course';
$string['movemodaltitlewithname'] = 'Move course: {$a}';
$string['movepositionhelp'] = 'Enter a position from 1 to {$a}.';
$string['movepositionclick'] = 'Click on a dashed line to move to that position.';
$string['movepositioninvalid'] = 'Position must be between 1 and the last item.';
$string['listofcourses'] = 'List of courses';
$string['backtoprograms'] = 'Back to programs';
$string['mappings'] = 'Mappings';
$string['addmapping'] = 'Add mapping';
$string['manualmapping'] = 'Manual mapping';
$string['automaticmapping'] = 'Automatic mapping';
$string['automaticmappingdone'] = 'Automatic mapping completed. {$a} mapping(s) created.';
$string['automaticmappingnocode'] = 'External course has no code. Cannot run automatic mapping.';
$string['automaticmappingdonecurriculum'] = 'Automatic mapping completed. {$a} mapping(s) created for the curriculum courses.';
$string['editmapping'] = 'Edit requirement';
$string['move'] = 'Move';
$string['deletemappingconfirm'] = 'Delete this mapping?';
$string['deletemappingbody'] = 'This will delete "{$a}".';
$string['deletemappingtitle'] = 'Delete mapping?';
$string['mappingdeleted'] = 'Mapping deleted.';
$string['mappedmoodlecourse'] = 'Mapped Moodle course';
$string['mappingofcourse'] = 'Mapping of the external course {$a}';
$string['listofmappings'] = 'List of mappings';
$string['listofmappingsforcourse'] = 'List of mappings - {$a}';
$string['backtocourses'] = 'Back to courses';
$string['mappingformerror'] = 'Please fix the errors in the mapping form.';
$string['duplicatemapping'] = 'This Moodle course is already mapped to this external course.';
$string['courseplaceholder'] = 'Type to search for a course';
$string['importtitle'] = 'Import curriculum CSV';
$string['importhelp'] = 'CSV columns: curriculum_code, curriculum_name, course_code, course_name, moodle_course_id, sortorder, curriculum_description. Optional: numterms, term, equivalence_code';
$string['importsuccess'] = 'CSV import completed.';
$string['importerrors'] = 'Import errors';
$string['viewtitle'] = 'Curriculum progress';
$string['progressview'] = 'Progress View';
$string['progresspercent'] = 'Progress';
$string['student'] = 'Student';
$string['completionstatus'] = 'Completion status';
$string['completed'] = 'Completed';
$string['notcompleted'] = 'Not completed';
$string['choosecurriculum'] = 'Choose curriculum';
$string['listofstudents'] = 'List of students';
$string['backtostudents'] = 'Back to student list';
$string['viewstudentprogress'] = 'View progress';
$string['nostudents'] = 'No students found in this course.';
$string['nocurricula'] = 'No curricula found.';
$string['deletecurriculumconfirm'] = 'Delete this program?';
$string['deletecurriculumtitle'] = 'Delete program?';
$string['deletecurriculumbody'] = 'This will delete "{$a}".';
$string['curriculumdeleted'] = 'Program deleted.';
$string['curriculumdeletecourses'] = 'Cannot delete a program that has courses. Remove all courses first.';
$string['nomappings'] = 'No mappings found.';
$string['csvfile'] = 'CSV file';
$string['upload'] = 'Upload';
$string['courseidrequired'] = 'Course ID is required to view progress.';
$string['mappedcourse'] = 'Mapped course';
$string['externalcourse'] = 'External course';
$string['program'] = 'Program';
$string['enrolled'] = 'Enrolled';
$string['progressbyenrollment'] = 'by enrollment';
$string['progressbycompletion'] = 'by completion';
$string['notenrolled'] = 'Not enrolled';
$string['moodlecoursesforexternal'] = 'Moodle courses for {$a}';
$string['moodlecourse_deleted'] = 'Moodle course deleted (ID: {$a})';
$string['close'] = 'Close';
