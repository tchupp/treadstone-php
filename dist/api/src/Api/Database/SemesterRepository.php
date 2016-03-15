<?php

namespace Api\Database;

class SemesterRepository {

    private $semesterParams = array('code', 'name');

    private $databaseConnection;

    public static function autowire() {
        return new SemesterRepository(new DatabaseConnection());
    }

    public function __construct(DatabaseConnection $databaseConnection) {
        $this->databaseConnection = $databaseConnection;
    }

    public function save($semester) {
        if (!$this->verifySemester($semester)) {
            return 0;
        }

        $query = "INSERT
                  INTO treadstone_semester(code, name)
                  VALUES(:code, :name)";

        $this->databaseConnection->bindMore($semester);
        $rows = $this->databaseConnection->query($query);

        return $rows;
    }

    public function findAll() {
        $query = "SELECT code, name
                  FROM treadstone_semester";
        $rows = $this->databaseConnection->query($query);

        $semesters = array();
        foreach($rows as $row) {
            $semesters[$row['code']] = $row;
        }
        return $semesters;
    }

    public function findOneByCode($code) {
        $query = "SELECT code, name
                  FROM treadstone_semester
                  WHERE code = :code";
        $this->databaseConnection->bind('code', $code);
        $semester = $this->databaseConnection->query($query);

        return reset($semester);
    }

    private function verifySemester($semester) {
        foreach ($this->semesterParams as $param) {
            if (!array_key_exists($param, $semester)) {
                return false;
            }
        }
        return true;
    }
}
