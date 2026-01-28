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

namespace block_programcurriculum;

defined('MOODLE_INTERNAL') || die();

class importer {
    public function import_csv(string $filepath): array {
        $errors = [];
        $handle = fopen($filepath, 'r');
        if ($handle === false) {
            return ['errors' => ['Unable to read CSV file.']];
        }

        $header = fgetcsv($handle);
        if (!$this->is_valid_header($header)) {
            fclose($handle);
            return ['errors' => ['Invalid CSV header.']];
        }

        $curriculumrepo = new curriculum_repository();
        $courserepo = new course_repository();
        $mappingrepo = new mapping_repository();

        $line = 1;
        while (($row = fgetcsv($handle)) !== false) {
            $line++;
            $data = $this->map_row($row);
            if (empty($data['curriculum_code']) || empty($data['curriculum_name'])) {
                $errors[] = "Line {$line}: Missing curriculum fields.";
                continue;
            }

            $curriculum = $curriculumrepo->get_by_externalcode($data['curriculum_code']);
            $curriculumrecord = (object)[
                'id' => $curriculum->id ?? 0,
                'name' => $data['curriculum_name'],
                'externalcode' => $data['curriculum_code'],
                'description' => $data['curriculum_description'] ?? '',
            ];
            $curriculumid = $curriculumrepo->upsert($curriculumrecord);

            if (empty($data['course_code']) || empty($data['course_name'])) {
                $errors[] = "Line {$line}: Missing course fields.";
                continue;
            }

            $course = $courserepo->get_by_externalcode($data['course_code']);
            $courserecord = (object)[
                'id' => $course->id ?? 0,
                'curriculumid' => $curriculumid,
                'name' => $data['course_name'],
                'externalcode' => $data['course_code'],
                'sortorder' => (int)$data['sortorder'],
            ];
            $courseid = $courserepo->upsert($courserecord);

            if (!empty($data['moodle_course_id'])) {
                $mappingrepo->upsert((object)[
                    'courseid' => $courseid,
                    'moodlecourseid' => (int)$data['moodle_course_id'],
                    'required' => (int)$data['required'],
                ]);
            }
        }

        fclose($handle);
        return ['errors' => $errors];
    }

    private function is_valid_header(?array $header): bool {
        if (empty($header)) {
            return false;
        }

        $required = [
            'curriculum_code',
            'curriculum_name',
            'course_code',
            'course_name',
            'moodle_course_id',
            'required',
            'sortorder',
        ];

        $header = array_map('trim', $header);
        foreach ($required as $column) {
            if (!in_array($column, $header, true)) {
                return false;
            }
        }

        return true;
    }

    private function map_row(array $row): array {
        return [
            'curriculum_code' => trim((string)($row[0] ?? '')),
            'curriculum_name' => trim((string)($row[1] ?? '')),
            'course_code' => trim((string)($row[2] ?? '')),
            'course_name' => trim((string)($row[3] ?? '')),
            'moodle_course_id' => trim((string)($row[4] ?? '')),
            'required' => $this->normalize_bool($row[5] ?? '1'),
            'sortorder' => trim((string)($row[6] ?? '0')),
            'curriculum_description' => trim((string)($row[7] ?? '')),
        ];
    }

    private function normalize_bool($value): int {
        if (is_numeric($value)) {
            return (int)$value ? 1 : 0;
        }

        $value = strtolower(trim((string)$value));
        return in_array($value, ['yes', 'true', '1'], true) ? 1 : 0;
    }
}
