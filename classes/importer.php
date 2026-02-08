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

    /**
     * Parse text format: program name(s), then for each program alternating semester line + course lines.
     * Semester line: only digits (e.g. "1", "2") or contains "semestre" (e.g. "1º SEMESTRE - ... 330h").
     * Multiple programs: one or more blank lines then next non-empty line = new program name.
     * Does not write to DB; returns structure for preview.
     *
     * @param string $content Raw file/content.
     * Program: name = before " - ", code = after " - ". Course: code = before " - ", name = after " - ".
     * Term number derived from order (1, 2, 3...).
     *
     * @return array { programs: [ { programname, programcode, terms: [ { term, label, courses: [] } ] } ], errors: [] }
     */
    public function parse_text_format(string $content): array {
        $lines = preg_split('/\r\n|\r|\n/', $content);
        $result = [
            'programs' => [],
            'errors' => [],
        ];

        $trimmed = [];
        foreach ($lines as $i => $line) {
            $trimmed[$i] = trim($line);
        }

        $n = count($trimmed);
        if ($n === 0) {
            $result['errors'][] = get_string('importtext_empty', 'block_programcurriculum');
            return $result;
        }

        $i = 0;
        $afterblank = false;
        $currentprogram = null;
        $currentterm = null;
        $currentcourses = [];

        while ($i < $n) {
            $line = $trimmed[$i];
            if ($line === '') {
                if ($currentprogram !== null) {
                    $this->push_current_term($currentterm, $currentcourses, $currentprogram);
                    $result['programs'][] = $currentprogram;
                    $currentprogram = null;
                    $currentterm = null;
                    $currentcourses = [];
                }
                $afterblank = true;
                $i++;
                continue;
            }

            if ($afterblank && $currentprogram !== null) {
                // After blank line(s), this non-empty line = new program name. Finish current program.
                $this->push_current_term($currentterm, $currentcourses, $currentprogram);
                $result['programs'][] = $currentprogram;
                $parsed = $this->parse_program_line($line);
                $currentprogram = [
                    'programname' => $parsed['name'],
                    'programcode' => $parsed['code'],
                    'terms' => [],
                ];
                $currentterm = null;
                $currentcourses = [];
                $afterblank = false;
                $i++;
                continue;
            }

            $afterblank = false;

            if ($this->is_semester_line($line)) {
                if ($currentprogram === null) {
                    $result['errors'][] = get_string('importtext_semester_before_program', 'block_programcurriculum', $i + 1);
                } else {
                    $this->push_current_term($currentterm, $currentcourses, $currentprogram);
                    $currentterm = $line;
                    $currentcourses = [];
                }
            } else {
                if ($currentprogram === null) {
                    $parsed = $this->parse_program_line($line);
                    $currentprogram = [
                        'programname' => $parsed['name'],
                        'programcode' => $parsed['code'],
                        'terms' => [],
                    ];
                } else if ($currentterm === null) {
                    $result['errors'][] = get_string('importtext_semester_first', 'block_programcurriculum', $i + 1);
                } else {
                    $currentcourses[] = $this->parse_course_line($line);
                }
            }
            $i++;
        }

        if ($currentprogram !== null) {
            $this->push_current_term($currentterm, $currentcourses, $currentprogram);
            $result['programs'][] = $currentprogram;
        }

        if (empty($result['programs']) && empty($result['errors'])) {
            $result['errors'][] = get_string('importtext_noprograms', 'block_programcurriculum');
        }

        return $result;
    }

    /**
     * Parses program line: part before " - " = name, part after = code.
     *
     * @param string $line
     * @return array{name: string, code: string}
     */
    private function parse_program_line(string $line): array {
        $line = trim($line);
        $sep = ' - ';
        $pos = strpos($line, $sep);
        if ($pos !== false) {
            return [
                'name' => trim(substr($line, 0, $pos)),
                'code' => trim(substr($line, $pos + strlen($sep))),
            ];
        }
        return ['name' => $line, 'code' => $line];
    }

    /**
     * Parses a course line. If it contains " - ", part before = code, part after = name; else whole line = name.
     *
     * @param string $line
     * @return array{name: string, code: string}
     */
    private function parse_course_line(string $line): array {
        $line = trim($line);
        $sep = ' - ';
        $pos = strpos($line, $sep);
        if ($pos !== false) {
            return [
                'code' => trim(substr($line, 0, $pos)),
                'name' => trim(substr($line, $pos + strlen($sep))),
            ];
        }
        return ['code' => '', 'name' => $line];
    }

    /**
     * Appends current term to program and resets term/courses (by reference).
     *
     * @param string|null $currentterm
     * @param array $currentcourses
     * @param array $currentprogram
     */
    private function push_current_term(?string $currentterm, array $currentcourses, array &$currentprogram): void {
        if ($currentterm !== null) {
            $termnumber = count($currentprogram['terms']) + 1;
            $courseswithterm = array_map(function ($c) use ($termnumber) {
                return $c + ['term' => $termnumber];
            }, $currentcourses);
            $currentprogram['terms'][] = [
                'term' => $termnumber,
                'label' => $currentterm,
                'courses' => $courseswithterm,
            ];
        }
    }

    /**
     * Whether the line is a semester header (e.g. "1", "2", "1º SEMESTRE - ADMINISTRAÇÃO SUB 2020/1	330h").
     */
    private function is_semester_line(string $line): bool {
        $line = trim($line);
        if ($line === '') {
            return false;
        }
        if (preg_match('/^\d+$/', $line)) {
            return true;
        }
        return (bool) preg_match('/semestre/i', $line);
    }

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
            $numterms = max(1, (int)($data['numterms'] ?? ($curriculum->numterms ?? 1)));
            $curriculumrecord = (object)[
                'id' => $curriculum->id ?? 0,
                'name' => $data['curriculum_name'],
                'externalcode' => $data['curriculum_code'],
                'description' => $data['curriculum_description'] ?? '',
                'numterms' => $numterms,
            ];
            $curriculumid = $curriculumrepo->upsert($curriculumrecord);

            if (empty($data['course_code']) || empty($data['course_name'])) {
                $errors[] = "Line {$line}: Missing course fields.";
                continue;
            }

            $course = $courserepo->get_by_externalcode($data['course_code']);
            $term = max(1, min($numterms, (int)($data['term'] ?? ($course->term ?? 1))));
            $courserecord = (object)[
                'id' => $course->id ?? 0,
                'curriculumid' => $curriculumid,
                'name' => $data['course_name'],
                'externalcode' => $data['course_code'],
                'equivalencecode' => $data['equivalence_code'] ?? '',
                'term' => $term,
                'sortorder' => (int)$data['sortorder'],
            ];
            $courseid = $courserepo->upsert($courserecord);

            if (!empty($data['moodle_course_id'])) {
                $mappingrepo->upsert((object)[
                    'courseid' => $courseid,
                    'moodlecourseid' => (int)$data['moodle_course_id'],
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
            'sortorder' => trim((string)($row[5] ?? '0')),
            'curriculum_description' => trim((string)($row[6] ?? '')),
            'numterms' => trim((string)($row[7] ?? '')),
            'term' => trim((string)($row[8] ?? '')),
            'equivalence_code' => trim((string)($row[9] ?? '')),
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
