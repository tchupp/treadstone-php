<?php

namespace Api\Database;

class UserRepository {
    private $databaseConnection;

    /**
     * UserRepository constructor.
     * @param $databaseConnection DatabaseConnection
     */
    public function __construct($databaseConnection) {
        $this->databaseConnection = $databaseConnection;
    }

    /**
     *
     * @return array of Users, indexed by login
     */
    public function findAll() {
        return $this->databaseConnection->query("SELECT * FROM TREADSTONE_USER");
    }

    /**
     * @param $login string login of the user to find
     * @return array user if exists, null if not
     */
    public function findOneByLogin($login) {
        $this->databaseConnection->bind('login', $login);
        return $this->databaseConnection->query("SELECT * FROM TREADSTONE_USER WHERE login = :login LIMIT 1");
    }
}
