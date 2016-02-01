<?php

namespace Test\Unit;

use Api\Database\DatabaseConnection;
use Api\Database\UserRepository;
use Api\Model\User;
use Phake;
use PHPUnit_Framework_TestCase;

class UserRepositoryTest extends PHPUnit_Framework_TestCase {

    public function testAutowire() {
        $userRepository = UserRepository::autowire();

        $this->assertAttributeInstanceOf(DatabaseConnection::class, 'databaseConnection', $userRepository);
    }

    public function testSaveCallsQueryOnDatabaseConnectionWithCorrectQueryCorrectInput() {
        $query = "INSERT
                  INTO treadstone_user(login, password_hash, first_name, last_name, email, activated, activation_key, reset_key)
                  VALUES(:login, :password, :firstName, :lastName, :email, :activated, :activationKey, :resetKey)";

        $user = $this->buildNewUser();

        $rowsModified = 1;

        $databaseConnection = Phake::mock('Api\Database\DatabaseConnection');
        Phake::when($databaseConnection)
            ->query($query)
            ->thenReturn($rowsModified);

        $userRepository = new UserRepository($databaseConnection);

        $this->assertEquals($rowsModified, $userRepository->save($user));

        Phake::inOrder(
            Phake::verify($databaseConnection)->bindMore($user->toDatabaseArray()),
            Phake::verify($databaseConnection)->query($query)
        );
    }

    public function testSaveInsertsIntoAuthorityTableOncePerRole() {
        $userQuery = "INSERT
                  INTO treadstone_user(login, password_hash, first_name, last_name, email, activated, activation_key, reset_key)
                  VALUES(:login, :password, :firstName, :lastName, :email, :activated, :activationKey, :resetKey)";
        $roleQuery = "INSERT
                  INTO treadstone_user_authority(user_id, authority_name)
                  VALUES(:id, :role)";

        $roleUser = 'ROLE_USER';
        $roleAdmin = 'ROLE_ADMIN';
        $user = $this->buildNewUser();
        $user->addRole($roleAdmin);

        $rowsModified = 3;
        $userId = 13;

        $databaseConnection = Phake::mock('Api\Database\DatabaseConnection');
        Phake::when($databaseConnection)
            ->query($userQuery)
            ->thenReturn(1);
        Phake::when($databaseConnection)
            ->query($roleQuery)
            ->thenReturn(1);
        Phake::when($databaseConnection)
            ->lastInsertId()
            ->thenReturn($userId);

        $userRepository = new UserRepository($databaseConnection);

        $this->assertEquals($rowsModified, $userRepository->save($user));

        $userParams = array('id' => $userId, 'role' => $roleUser);
        $adminParams = array('id' => $userId, 'role' => $roleAdmin);
        Phake::inOrder(
            Phake::verify($databaseConnection)->query($userQuery),
            Phake::verify($databaseConnection)->bindMore($userParams),
            Phake::verify($databaseConnection, Phake::times(2))->query($roleQuery),
            Phake::verify($databaseConnection)->bindMore($adminParams)
        );
    }

    public function testUpdateCallsQueryOnDatabaseWithCorrectQueryCorrectInput() {
        $query = "UPDATE treadstone_user
                  SET password_hash = :password,
                      first_name = :firstName, last_name = :lastName, email = :email,
                      activated = :activated, activation_key = :activationKey,
                      reset_key = :resetKey
                  WHERE login = :login";

        $user = $this->buildNewUser();

        $rowsModified = 1;

        $databaseConnection = Phake::mock('Api\Database\DatabaseConnection');
        Phake::when($databaseConnection)
            ->query($query)
            ->thenReturn($rowsModified);

        $userRepository = new UserRepository($databaseConnection);

        $this->assertEquals($rowsModified, $userRepository->update($user));

        Phake::inOrder(
            Phake::verify($databaseConnection)->bindMore($user->toDatabaseArray()),
            Phake::verify($databaseConnection)->query($query)
        );
    }

    public function testFindAllCallsQueryOnDatabaseConnectionWithCorrectQuery() {
        $query = "SELECT User.login, User.password_hash AS password,
                    User.first_name, User.last_name, User.email,
                    User.activated, User.activation_key,
                    User.reset_key, Auth.authority_name AS role
                  FROM treadstone_user User, treadstone_user_authority Auth
                  WHERE User.id = Auth.user_id";

        $databaseConnection = Phake::mock('Api\Database\DatabaseConnection');
        Phake::when($databaseConnection)
            ->query($query)
            ->thenReturn($this->buildFindAllData());

        $userRepository = new UserRepository($databaseConnection);

        $this->assertEquals($this->buildFindAllUsers(), $userRepository->findAll());
    }

    public function testFindOneByLoginCallsQueryOnDatabaseConnectionWithCorrectQuery() {
        $query = "SELECT User.login, User.password_hash AS password,
                    User.first_name, User.last_name, User.email,
                    User.activated, User.activation_key,
                    User.reset_key, Auth.authority_name AS role
                  FROM treadstone_user User, treadstone_user_authority Auth
                  WHERE login = :login
                  AND User.id = Auth.user_id";

        $databaseConnection = Phake::mock('Api\Database\DatabaseConnection');
        Phake::when($databaseConnection)
            ->query($query)
            ->thenReturn($this->buildFindOneData());

        $userRepository = new UserRepository($databaseConnection);

        $loginKey = 'login';
        $loginValue = 'administrator';
        $this->assertEquals($this->buildFindOneUser(),
            $userRepository->findOneByLogin($loginValue));

        Phake::inOrder(
            Phake::verify($databaseConnection)->bind($loginKey, $loginValue),
            Phake::verify($databaseConnection)->query($query)
        );
    }

    public function testFindOneByEmailCallsQueryOnDatabaseConnectionWithCorrectQuery() {
        $query = "SELECT User.login, User.password_hash AS password,
                    User.first_name, User.last_name, User.email,
                    User.activated, User.activation_key,
                    User.reset_key, Auth.authority_name AS role
                  FROM treadstone_user User, treadstone_user_authority Auth
                  WHERE email = :email
                  AND User.id = Auth.user_id";

        $databaseConnection = Phake::mock('Api\Database\DatabaseConnection');
        Phake::when($databaseConnection)
            ->query($query)
            ->thenReturn($this->buildFindOneData());

        $userRepository = new UserRepository($databaseConnection);

        $emailKey = 'email';
        $emailValue = 'theo@theoiscool.com';
        $this->assertEquals($this->buildFindOneUser(),
            $userRepository->findOneByEmail($emailValue));

        Phake::inOrder(
            Phake::verify($databaseConnection)->bind($emailKey, $emailValue),
            Phake::verify($databaseConnection)->query($query)
        );
    }

    public function testFindOneByActivationKeyCallsQueryOnDatabaseConnectionWithCorrectQuery() {
        $query = "SELECT User.login, User.password_hash AS password,
                    User.first_name, User.last_name, User.email,
                    User.activated, User.activation_key,
                    User.reset_key, Auth.authority_name AS role
                  FROM treadstone_user User, treadstone_user_authority Auth
                  WHERE activation_key = :activation_key
                  AND User.id = Auth.user_id";

        $databaseConnection = Phake::mock('Api\Database\DatabaseConnection');
        Phake::when($databaseConnection)
            ->query($query)
            ->thenReturn($this->buildFindOneData());

        $userRepository = new UserRepository($databaseConnection);

        $activationKey = 'activation_key';
        $activationKeyValue = '576nb3ubm2ui942b19';
        $this->assertEquals($this->buildFindOneUser(),
            $userRepository->findOneByActivationKey($activationKeyValue));

        Phake::inOrder(
            Phake::verify($databaseConnection)->bind($activationKey, $activationKeyValue),
            Phake::verify($databaseConnection)->query($query)
        );
    }

    public function testFindOneByResetKey() {
        $query = "SELECT User.login, User.password_hash AS password,
                    User.first_name, User.last_name, User.email,
                    User.activated, User.activation_key,
                    User.reset_key, Auth.authority_name AS role
                  FROM treadstone_user User, treadstone_user_authority Auth
                  WHERE reset_key = :reset_key
                  AND User.id = Auth.user_id";

        $databaseConnection = Phake::mock('Api\Database\DatabaseConnection');
        Phake::when($databaseConnection)
            ->query($query)
            ->thenReturn($this->buildFindOneData());

        $userRepository = new UserRepository($databaseConnection);

        $resetKey = 'reset_key';
        $resetKeyValue = 'uuoikbnm321yionm342198';
        $this->assertEquals($this->buildFindOneUser(),
            $userRepository->findOneByResetKey($resetKeyValue));

        Phake::inOrder(
            Phake::verify($databaseConnection)->bind($resetKey, $resetKeyValue),
            Phake::verify($databaseConnection)->query($query)
        );
    }

    private function buildFindOneData() {
        $data[] = array(
            'login' => 'administrator',
            'password' => '$2a$10$mE.qfsV0mji5NcKhb',
            'first_name' => 'Admin', 'last_name' => 'Admin', 'email' => 'admin@localhost',
            'activated' => 0, 'activation_key' => null, 'reset_key' => null, 'role' => 'ROLE_ADMIN');
        $data[] = array(
            'login' => 'administrator',
            'password' => '$2a$10$mE.qfsV0mji5NcKhb',
            'first_name' => 'Admin', 'last_name' => 'Admin', 'email' => 'admin@localhost',
            'activated' => 0, 'activation_key' => null, 'reset_key' => null, 'role' => 'ROLE_USER');
        return $data;
    }

    private function buildFindAllData() {
        $data = $this->buildFindOneData();
        $data[] = array(
            'login' => 'chullupabatman',
            'password' => '$2a$10$fd27ad9546b355d167jb1',
            'first_name' => 'batman', 'last_name' => 'chullupa', 'email' => 'chullupabatman@msu.edu',
            'activated' => 1, 'activation_key' => null, 'reset_key' => null, 'role' => 'ROLE_USER');
        return $data;
    }

    public static function buildFindOneUser() {
        return new User('administrator', '$2a$10$mE.qfsV0mji5NcKhb', 'admin@localhost',
            'Admin', 'Admin', 0, null, null, array('ROLE_ADMIN', 'ROLE_USER'));
    }

    private function buildFindAllUsers() {
        $userOne = $this->buildFindOneUser();
        $users[$userOne->getLogin()] = $userOne;
        $userTwo = new User('chullupabatman', '$2a$10$fd27ad9546b355d167jb1', 'chullupabatman@msu.edu',
            'batman', 'chullupa', 1, null, null, array('ROLE_USER'));
        $users[$userTwo->getLogin()] = $userTwo;
        return $users;
    }

    private function buildNewUser() {
        $user = new User('chuppthe', 'super', 'theo@thisiscool.com',
            'theo', 'chupp', false, 'blahHA473810ji903h1', null, array('ROLE_USER'));
        return $user;
    }
}
