<?php

namespace Api\Database;

class UserRepository {

    private $userParams = array('login', 'password',
        'firstName', 'lastName', 'email',
        'activated', 'activationKey', 'role');

    private $databaseConnection;

    public function __construct(DatabaseConnection $databaseConnection) {
        $this->databaseConnection = $databaseConnection;
    }

    public function save(&$user) {
        if (!$this->verifyUser($user)) {
            return 0;
        }

        $userQuery = "INSERT
                  INTO treadstone_user(login, password_hash, first_name, last_name, email, activated, activation_key)
                  VALUES(:login, :password, :firstName, :lastName, :email, :activated, :activationKey)";
        $roleQuery = "INSERT
                  INTO treadstone_user_authority(user_id, authority_name)
                  VALUES(:id, :role)";

        $user['activated'] = $user['activated'] ? 1 : 0;

        $roles = $user['role'];
        unset($user['role']);

        $this->databaseConnection->bindMore($user);
        $rows = $this->databaseConnection->query($userQuery);
        $userId = $this->databaseConnection->lastInsertId();

        foreach($roles as $role) {
            $data = array('id' => $userId, 'role' => $role);
            $this->databaseConnection->bindMore($data);
            $rows += $this->databaseConnection->query($roleQuery);
        }
        return $rows;
    }

    public function findAll() {
        $query = "SELECT User.*, Auth.authority_name AS role
                  FROM treadstone_user User, treadstone_user_authority Auth
                  WHERE User.id = Auth.user_id";
        $rows = $this->databaseConnection->query($query);

        $users = $this->convertRowsToUsers($rows);
        return $users;
    }

    public function findOneByLogin($login) {
        $query = "SELECT User.*, Auth.authority_name AS role
                  FROM treadstone_user User, treadstone_user_authority Auth
                  WHERE login = :login
                  AND User.id = Auth.user_id";
        $this->databaseConnection->bind('login', $login);
        $rows = $this->databaseConnection->query($query);

        $users = $this->convertRowsToUsers($rows);
        return reset($users);
    }

    public function findOneByEmail($email) {
        $query = "SELECT User.*, Auth.authority_name AS role
                  FROM treadstone_user User, treadstone_user_authority Auth
                  WHERE email = :email
                  AND User.id = Auth.user_id";
        $this->databaseConnection->bind('email', $email);
        $rows = $this->databaseConnection->query($query);

        $users = $this->convertRowsToUsers($rows);
        return reset($users);
    }

    private function verifyUser($user) {
        foreach ($this->userParams as $param) {
            if (!array_key_exists($param, $user)) {
                return false;
            }
        }
        return true;
    }

    private function convertRowsToUsers($rows) {
        $users = array();
        foreach ($rows as $row) {
            if (empty($users[$row['login']])) {
                $users[$row['login']] = $row;
                $users[$row['login']]['role'] = array($row['role']);
            } else {
                $users[$row['login']]['role'][] = $row['role'];
            }
        }
        return $users;
    }
}
