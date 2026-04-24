# Program Curriculum Block (`block_programcurriculum`)

This Moodle block helps institutions manage program curricula, map external courses to Moodle courses, and let students track curriculum progress directly inside Moodle.

## Requirements

- Moodle **5.0+**
- PHP version supported by your Moodle 5.0 installation

## Installation

You can install the plugin in two common ways:

### Option 1: Clone from GitHub

1. Go to your Moodle root directory.
2. Clone the repository into `blocks/programcurriculum`.
git clone https://github.com/marceloschmitt/moodle-block_programcurriculum

3. Open Moodle as admin and complete the upgrade process.

### Option 2: Download and copy

1. Download the plugin ZIP from GitHub.
2. Extract it.
3. Copy the extracted folder to: <moodle_root>/blocks/programcurriculum
4. Open Moodle as admin and complete the upgrade process.

## What this plugin does

- Create and manage curricula (programs).
- Add external courses (name/code/term/sort order) to each curriculum.
- Map each external course to one or more Moodle courses.
- Provide automatic mapping suggestions using course name/shortname matching.
- Show student progress by:
  - completion percentage
  - enrollment percentage
- Allow students to mark external courses as completed (when permitted).
- Import curricula/courses from file.

## Import format

The import page uses the structured text format.

## Structured text import

Simple rules:

- Programs are separated by one or more blank lines.
- Each program starts with a program line: `Program Name - PROGRAM_CODE`.
- Terms are defined by term lines (for example `1`, `2`, `3`).
- Course lines use `COURSE_CODE - Course Name`.

Example:

```text
Computer Science - CS-BSC
1
CS101 - Programming Fundamentals
CS102 - Discrete Mathematics
2
CS201 - Data Structures

Information Systems - IS-BSC
1
IS101 - Intro to Information Systems
```

## Notes

- If a curriculum with the same external code already exists, it is skipped during import.
- If a course code already exists inside the same curriculum, duplicate entries are ignored.
- `moodle_course_id` can be empty if you want to import curricula/courses first and map later.

## License

GNU GPL v3 or later.
