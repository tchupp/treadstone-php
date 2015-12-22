<?php

namespace Api\Database;

class UserRepository {

    private $userParams = ['login', 'password', 'firstName', 'lastName', 'email', 'activated', 'activationKey'];

    private $databaseConnection;

    /**
     * UserRepository constructor.
     * @param $databaseConnection DatabaseConnection
     */
    public function __construct($databaseConnection) {
        $this->databaseConnection = $databaseConnection;
    }

    public function save(&$user) {
        if (!$this->verifyUser($user)) {
            return 0;
        }

        $query = "INSERT
                  INTO treadstone_user(login, password_hash, first_name, last_name, email, activated, activation_key)
                  VALUES(:login, :password, :firstName, :lastName, :email, :activated, :activationKey)";

        $user['activated'] = $user['activated'] ? 1 : 0;

        $this->databaseConnection->bindMore($user);
        $rows = $this->databaseConnection->query($query);
        return $rows;
    }

    /**
     * @return array of Users, indexed by id
     */
    public function findAll() {
        $query = "SELECT *
                  FROM treadstone_user";

        return $this->databaseConnection->query($query);
    }

    /**
     * @param $login string login of the user to find
     * @return array user if exists, null if not
     */
    public function findOneByLogin($login) {
        $query = "SELECT *
                  FROM treadstone_user WHERE login = :login LIMIT 1";

        $this->databaseConnection->bind('login', $login);
        return $this->databaseConnection->query($query)[0];
    }

    public function findOneByEmail($email) {
        $query = "SELECT *
                  FROM treadstone_user WHERE email = :email LIMIT 1";

        $this->databaseConnection->bind('email', $email);
        return $this->databaseConnection->query($query);
    }

    private function verifyUser($user) {
        foreach ($this->userParams as $param) {
            if (!array_key_exists($param, $user)) {
                return false;
            }
        }
        return true;
    }
}
