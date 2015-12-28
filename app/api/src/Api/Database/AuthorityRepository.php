<?php

namespace Api\Database;

class AuthorityRepository {

    private $databaseConnection;

    public function __construct(DatabaseConnection $databaseConnection) {
        $this->databaseConnection = $databaseConnection;
    }

    public function findOne($name) {
        $query = "SELECT name
                  FROM treadstone_authority
                  WHERE name = :name";

        $this->databaseConnection->bind("name", $name);
        $auth = $this->databaseConnection->query($query);

        return reset($auth);
    }
}
