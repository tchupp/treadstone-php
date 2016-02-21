<?php

namespace Api\Database;

class SubjectRepository {

    private $subjectParams = ['code', 'name'];

    private $databaseConnection;

    public static function autowire() {
        return new SubjectRepository(new DatabaseConnection());
    }

    public function __construct(DatabaseConnection $databaseConnection) {
        $this->databaseConnection = $databaseConnection;
    }

    public function save($subject) {
        if (!$this->verifySubject($subject)) {
            return 0;
        }

        $query = "INSERT
                  INTO treadstone_subject(code, name)
                  VALUES(:code, :name)";

        $this->databaseConnection->bindMore($subject);
        $rows = $this->databaseConnection->query($query);

        return $rows;
    }

    public function findAll() {
        $query = "SELECT *
                  FROM treadstone_subject";
        $rows = $this->databaseConnection->query($query);

        $subject = [];
        foreach ($rows as $row) {
            $subject[$row['code']] = $row;
        }
        return $subject;
    }

    public function findAllBySemester($semester) {
        $query = "SELECT Subject.code AS subjectCode, Subject.name AS subjectName,
                    Semester.code AS semesterCode, Semester.name AS semesterName
                  FROM treadstone_subject Subject, treadstone_semester Semester, treadstone_section Section
                  WHERE Section.subject_id = Subject.id
                  AND Section.semester_id = Semester.id
                  AND Semester.code = :semester
                  ORDER BY Subject.code";

        $this->databaseConnection->bind('semester', $semester);
        $subjects = $this->databaseConnection->query($query);

        return $subjects;
    }

    public function findOneBySemesterAndSubject($semester, $subject) {
        $query = "SELECT Subject.code AS subjectCode, Subject.name AS subjectName,
                    Semester.code AS semesterCode, Semester.name AS semesterName
                  FROM treadstone_subject Subject, treadstone_semester Semester, treadstone_section Section
                  WHERE Section.subject_id = Subject.id
                  AND Section.semester_id = Semester.id
                  AND Semester.code = :semester
                  AND Subject.code = :subject
                  ORDER BY Subject.code";
        $this->databaseConnection->bind('semester', $semester);
        $this->databaseConnection->bind('subject', $subject);
        $subject = $this->databaseConnection->query($query);

        return reset($subject);
    }

    private function verifySubject($subject) {
        foreach ($this->subjectParams as $param) {
            if (!array_key_exists($param, $subject)) {
                return false;
            }
        }
        return true;
    }
}
