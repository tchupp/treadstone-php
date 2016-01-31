<?php

namespace Api\Database;

use Api\Model\User;

class UserRepository {

    private $databaseConnection;

    public static function autowire() {
        return new UserRepository(new DatabaseConnection());
    }

    public function __construct(DatabaseConnection $databaseConnection) {
        $this->databaseConnection = $databaseConnection;
    }

    public function save(User $user) {
        $userQuery = "INSERT
                  INTO treadstone_user(login, password_hash, first_name, last_name, email, activated, activation_key)
                  VALUES(:login, :password, :first_name, :last_name, :email, :activated, :activation_key)";
        $roleQuery = "INSERT
                  INTO treadstone_user_authority(user_id, authority_name)
                  VALUES(:id, :role)";

        $this->databaseConnection->bindMore($user->toDatabaseArray());
        $rows = $this->databaseConnection->query($userQuery);
        $userId = $this->databaseConnection->lastInsertId();

        $roles = $user->getRoles();
        foreach ($roles as $role) {
            $data = array('id' => $userId, 'role' => $role);
            $this->databaseConnection->bindMore($data);
            $rows += $this->databaseConnection->query($roleQuery);
        }
        return $rows;
    }

    public function update(User $user) {
        $query = "UPDATE treadstone_user
                  SET password_hash = :password,
                      first_name = :first_name, last_name = :last_name, email = :email,
                      activated = :activated, activation_key = :activation_key,
                      reset_key = :reset_key, reset_date = :reset_date
                  WHERE login = :login";

        $this->databaseConnection->bindMore($user->toDatabaseArray());
        $rows = $this->databaseConnection->query($query);

        return $rows;
    }

    public function findAll() {
        $query = "SELECT User.login, User.password_hash AS password,
                    User.first_name, User.last_name, User.email,
                    User.activated, User.activation_key, Auth.authority_name AS role
                  FROM treadstone_user User, treadstone_user_authority Auth
                  WHERE User.id = Auth.user_id";
        $rows = $this->databaseConnection->query($query);

        $users = $this->convertRowsToUsers($rows);
        return $users;
    }

    public function findOneByLogin($login) {
        $query = "SELECT User.login, User.password_hash AS password,
                    User.first_name, User.last_name, User.email,
                    User.activated, User.activation_key, Auth.authority_name AS role
                  FROM treadstone_user User, treadstone_user_authority Auth
                  WHERE login = :login
                  AND User.id = Auth.user_id";
        $this->databaseConnection->bind('login', $login);
        $rows = $this->databaseConnection->query($query);

        $users = $this->convertRowsToUsers($rows);
        return reset($users);
    }

    public function findOneByEmail($email) {
        $query = "SELECT User.login, User.password_hash AS password,
                    User.first_name, User.last_name, User.email,
                    User.activated, User.activation_key, Auth.authority_name AS role
                  FROM treadstone_user User, treadstone_user_authority Auth
                  WHERE email = :email
                  AND User.id = Auth.user_id";
        $this->databaseConnection->bind('email', $email);
        $rows = $this->databaseConnection->query($query);

        $users = $this->convertRowsToUsers($rows);
        return reset($users);
    }

    public function findOneByActivationKey($key) {
        $query = "SELECT User.login, User.password_hash AS password,
                    User.first_name, User.last_name, User.email,
                    User.activated, User.activation_key, Auth.authority_name AS role
                  FROM treadstone_user User, treadstone_user_authority Auth
                  WHERE activation_key = :activation_key
                  AND User.id = Auth.user_id";
        $this->databaseConnection->bind('activation_key', $key);
        $rows = $this->databaseConnection->query($query);

        $users = $this->convertRowsToUsers($rows);
        return reset($users);
    }

    private function convertRowsToUsers($rows) {
        $usersDTO = array();
        foreach ($rows as $row) {
            if (empty($usersDTO[$row['login']])) {
                $usersDTO[$row['login']] = $row;
                $usersDTO[$row['login']]['role'] = array($row['role']);
            } else {
                $usersDTO[$row['login']]['role'][] = $row['role'];
            }
        }
        $users = array();
        foreach ($usersDTO as $user) {
            $users[] = new User($user['login'], $user['password'], $user['email'],
                $user['first_name'], $user['last_name'], $user['activated'], $user['activation_key'], $user['role']);
        }
        return $users;
    }
}
