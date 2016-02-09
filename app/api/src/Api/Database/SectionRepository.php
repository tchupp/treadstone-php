<?php

namespace Api\Database;

class SectionRepository {

    private $databaseConnection;

    public static function autowire() {
        return new SectionRepository(new DatabaseConnection());
    }

    public function __construct(DatabaseConnection $databaseConnection) {
        $this->databaseConnection = $databaseConnection;
    }

    public function findAll() {
        $query = "SELECT Subject.code AS subject_code, Subject.name AS subject_name,
	                Semester.code AS semester_code, Semester.name AS semester_name,
	                Section.section_number,
	                SectionTime.day_name, SectionTime.start_time, SectionTime.end_time
                  FROM treadstone_section Section, treadstone_subject Subject,
	                treadstone_semester Semester, treadstone_section_time SectionTime,
	                treadstone_day Day
                  WHERE Section.subject_id = Subject.id
                  AND Section.semester_id = Semester.id
                  AND SectionTime.section_id = Section.id
                  AND Day.name = SectionTime.day_name
                  ORDER BY Subject.code, Day.number";
        $rows = $this->databaseConnection->query($query);

        $sections = $this->convertRowsToSections($rows);
        return $sections;
    }

    public function findAllBySemester($semester) {
        $query = "SELECT Subject.code AS subject_code, Subject.name AS subject_name,
	                Semester.code AS semester_code, Semester.name AS semester_name,
	                Section.section_number,
	                SectionTime.day_name, SectionTime.start_time, SectionTime.end_time
                  FROM treadstone_section Section, treadstone_subject Subject,
	                treadstone_semester Semester, treadstone_section_time SectionTime,
	                treadstone_day Day
                  WHERE Section.subject_id = Subject.id
                  AND Section.semester_id = Semester.id
                  AND SectionTime.section_id = Section.id
                  AND Semester.code LIKE :semester
                  AND Day.name = SectionTime.day_name
                  ORDER BY Subject.code, Day.number";
        $this->databaseConnection->bind('semester', "%$semester%");
        $rows = $this->databaseConnection->query($query);

        $sections = $this->convertRowsToSections($rows);
        return $sections;
    }

    public function findAllBySubject($subject) {
        $query = "SELECT Subject.code AS subject_code, Subject.name AS subject_name,
	                Semester.code AS semester_code, Semester.name AS semester_name,
	                Section.section_number,
	                SectionTime.day_name, SectionTime.start_time, SectionTime.end_time
                  FROM treadstone_section Section, treadstone_subject Subject,
	                treadstone_semester Semester, treadstone_section_time SectionTime,
	                treadstone_day Day
                  WHERE Section.subject_id = Subject.id
                  AND Section.semester_id = Semester.id
                  AND SectionTime.section_id = Section.id
                  AND Subject.code LIKE :subject
                  AND Day.name = SectionTime.day_name
                  ORDER BY Subject.code, Day.number";
        $this->databaseConnection->bind('subject', "%$subject%");
        $rows = $this->databaseConnection->query($query);

        $sections = $this->convertRowsToSections($rows);
        return $sections;
    }

    public function findAllBySemesterAndSubject($semester, $subject) {
        $query = "SELECT Subject.code AS subject_code, Subject.name AS subject_name,
	                Semester.code AS semester_code, Semester.name AS semester_name,
	                Section.section_number,
	                SectionTime.day_name, SectionTime.start_time, SectionTime.end_time
                  FROM treadstone_section Section, treadstone_subject Subject,
	                treadstone_semester Semester, treadstone_section_time SectionTime,
	                treadstone_day Day
                  WHERE Section.subject_id = Subject.id
                  AND Section.semester_id = Semester.id
                  AND SectionTime.section_id = Section.id
                  AND Semester.code LIKE :semester
                  AND Subject.code LIKE :subject
                  AND Day.name = SectionTime.day_name
                  ORDER BY Subject.code, Day.number";
        $this->databaseConnection->bind('semester', "%$semester%");
        $this->databaseConnection->bind('subject', "%$subject%");
        $rows = $this->databaseConnection->query($query);

        $sections = $this->convertRowsToSections($rows);
        return $sections;
    }

    private function convertRowsToSections($rows) {
        $sections = [];
        foreach ($rows as $row) {
            $sectionTime = [
                'day' => $row['day_name'], 'startTime' => $row['start_time'], 'endTime' => $row['end_time']];

            if (count($sections) > 0
                && $sections[count($sections) - 1]['semester']['code'] == $row['semester_code']
                && $sections[count($sections) - 1]['subject']['code'] == $row['subject_code']) {
                $sections[count($sections) - 1]['times'][] = $sectionTime;
            } else {
                $sections[] = [
                    'semester'      => ['code' => $row['semester_code'], 'name' => $row['semester_name']],
                    'subject'       => ['code' => $row['subject_code'], 'name' => $row['subject_name']],
                    'sectionNumber' => $row['section_number'],
                    'times'         => [$sectionTime]
                ];
            }
        }
        return $sections;
    }
}
