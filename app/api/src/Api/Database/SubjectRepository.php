<?php

namespace Api\Database;

class SubjectRepository {

    private $subjectParams = ['code', 'name'];

    private $databaseConnection;

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

        $subject = array();
        foreach ($rows as $row) {
            $subject[$row['code']] = $row;
        }
        return $subject;
    }

    public function findOneByCode($code) {
        $query = "SELECT *
                  FROM treadstone_subject
                  WHERE code = :code";
        $this->databaseConnection->bind('code', $code);
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
