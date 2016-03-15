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
                  INTO treadstone_user(login, password_hash, first_name, last_name, email, activated, activation_key, reset_key)
                  VALUES(:login, :password, :firstName, :lastName, :email, :activated, :activationKey, :resetKey)";
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
                      first_name = :firstName, last_name = :lastName, email = :email,
                      activated = :activated, activation_key = :activationKey,
                      reset_key = :resetKey
                  WHERE login = :login";

        $this->databaseConnection->bindMore($user->toDatabaseArray());
        $rows = $this->databaseConnection->query($query);

        return $rows;
    }

    public function findAll() {
        $query = "SELECT User.login, User.password_hash AS password,
                    User.first_name, User.last_name, User.email,
                    User.activated, User.activation_key,
                    User.reset_key, Auth.authority_name AS role
                  FROM treadstone_user User, treadstone_user_authority Auth
                  WHERE User.id = Auth.user_id";
        $rows = $this->databaseConnection->query($query);

        $users = $this->convertRowsToUsers($rows);
        return $users;
    }

    public function findOneByLogin($login) {
        $query = "SELECT User.login, User.password_hash AS password,
                    User.first_name, User.last_name, User.email,
                    User.activated, User.activation_key,
                    User.reset_key, Auth.authority_name AS role
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
                    User.activated, User.activation_key,
                    User.reset_key, Auth.authority_name AS role
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
                    User.activated, User.activation_key,
                    User.reset_key, Auth.authority_name AS role
                  FROM treadstone_user User, treadstone_user_authority Auth
                  WHERE activation_key = :activation_key
                  AND User.id = Auth.user_id";
        $this->databaseConnection->bind('activation_key', $key);
        $rows = $this->databaseConnection->query($query);

        $users = $this->convertRowsToUsers($rows);
        return reset($users);
    }

    public function findOneByResetKey($key) {
        $query = "SELECT User.login, User.password_hash AS password,
                    User.first_name, User.last_name, User.email,
                    User.activated, User.activation_key,
                    User.reset_key, Auth.authority_name AS role
                  FROM treadstone_user User, treadstone_user_authority Auth
                  WHERE reset_key = :reset_key
                  AND User.id = Auth.user_id";
        $this->databaseConnection->bind('reset_key', $key);
        $rows = $this->databaseConnection->query($query);

        $users = $this->convertRowsToUsers($rows);
        return reset($users);
    }

    private function convertRowsToUsers($rows) {
        /** @var User[] $usersDTO */
        $usersDTO = array();
        foreach ($rows as $row) {
            if (empty($usersDTO[$row['login']])) {
                $login = $row['login'];
                $password = $row['password'];
                $email = $row['email'];
                $firstName = $row['first_name'];
                $lastName = $row['last_name'];
                $activated = $row['activated'];
                $activationKey = $row['activation_key'];
                $resetKey = $row['reset_key'];
                $roles = array($row['role']);

                $user = new User($login, $password, $email, $firstName, $lastName,
                    $activated, $activationKey, $resetKey, $roles);

                $usersDTO[$row['login']] = $user;
            } else {
                $usersDTO[$row['login']]->addRole($row['role']);
            }
        }
        return $usersDTO;
    }
}
