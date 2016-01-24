<?php

namespace Test\Unit;

use Api\Database\UserRepository;
use Phake;
use PHPUnit_Framework_TestCase;

class UserRepositoryTest extends PHPUnit_Framework_TestCase {

    public function testSaveCallsQueryOnDatabaseConnectionWithCorrectQueryCorrectInput() {
        $query = "INSERT
                  INTO treadstone_user(login, password_hash, first_name, last_name, email, activated, activation_key)
                  VALUES(:login, :password, :firstName, :lastName, :email, :activated, :activationKey)";

        $data = $user = array('login' => 'chuppthe', 'password' => 'super',
            'firstName' => 'theo', 'lastName' => 'chupp',
            'email' => 'theo@thisiscool.com', 'activated' => false,
            'activationKey' => 'blahHA473810ji903h1');
        $user['role'] = array('ROLE_USER');

        $rowsModified = 1;

        $databaseConnection = Phake::mock('Api\Database\DatabaseConnection');
        Phake::when($databaseConnection)
            ->query($query)
            ->thenReturn($rowsModified);

        $userRepository = new UserRepository($databaseConnection);

        $this->assertEquals($rowsModified, $userRepository->save($user));

        Phake::inOrder(
            Phake::verify($databaseConnection)->bindMore($data),
            Phake::verify($databaseConnection)->query($query)
        );
    }

    public function testSaveInsertsIntoAuthorityTableOncePerRole() {
        $userQuery = "INSERT
                  INTO treadstone_user(login, password_hash, first_name, last_name, email, activated, activation_key)
                  VALUES(:login, :password, :firstName, :lastName, :email, :activated, :activationKey)";
        $roleQuery = "INSERT
                  INTO treadstone_user_authority(user_id, authority_name)
                  VALUES(:id, :role)";

        $roleUser = 'ROLE_USER';
        $roleAdmin = 'ROLE_ADMIN';
        $user = array('login' => 'chuppthe', 'password' => 'super',
            'firstName' => 'theo', 'lastName' => 'chupp',
            'email' => 'theo@thisiscool.com', 'activated' => false,
            'activationKey' => 'blahHA473810ji903h1',
            'role' => array($roleUser, $roleAdmin));

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

    public function testSaveDoesNotCallQueryIfArrayIsMissingParams() {
        $user = array('login' => 'chuppthe', 'firstName' => 'theo',
            'lastName' => 'chupp', 'email' => 'theo@thisiscool.com');

        $databaseConnection = Phake::mock('Api\Database\DatabaseConnection');

        $userRepository = new UserRepository($databaseConnection);

        $this->assertEquals(0, $userRepository->save($user));

        Phake::verifyNoInteraction($databaseConnection);
    }

    public function testFindAllCallsQueryOnDatabaseConnectionWithCorrectQuery() {
        $query = "SELECT User.*, Auth.authority_name AS role
                  FROM treadstone_user User, treadstone_user_authority Auth
                  WHERE User.id = Auth.user_id";

        $databaseConnection = Phake::mock('Api\Database\DatabaseConnection');
        Phake::when($databaseConnection)
            ->query($query)
            ->thenReturn($this->buildFindAllData());

        $userRepository = new UserRepository($databaseConnection);

        $this->assertSame($this->buildFindAllUsers(), $userRepository->findAll());
    }

    public function testFindOneByLoginCallsQueryOnDatabaseConnectionWithCorrectQuery() {
        $query = "SELECT User.*, Auth.authority_name AS role
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
        $this->assertSame($this->buildFindOneUser(),
            $userRepository->findOneByLogin($loginValue));

        Phake::inOrder(
            Phake::verify($databaseConnection)->bind($loginKey, $loginValue),
            Phake::verify($databaseConnection)->query($query)
        );
    }

    public function testFindOneByEmailCallsQueryOnDatabaseConnectionWithCorrectQuery() {
        $query = "SELECT User.*, Auth.authority_name AS role
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
        $this->assertSame($this->buildFindOneUser(),
            $userRepository->findOneByEmail($emailValue));

        Phake::inOrder(
            Phake::verify($databaseConnection)->bind($emailKey, $emailValue),
            Phake::verify($databaseConnection)->query($query)
        );
    }

    private function buildFindOneData() {
        $data[] = array(
            'id' => 4, 'login' => 'administrator',
            'password_hash' => '$2a$10$mE.qfsV0mji5NcKhb:0w.z4ueI/.bDWbj0T1BYyqP481kGGarKLG',
            'first_name' => 'Admin', 'last_name' => 'Admin', 'email' => 'admin@localhost',
            'activated' => 0, 'activation_key' => NULL, 'reset_key' => NULL,
            'created_date' => '2015-04-39 21:48:37', 'reset_date' => NULL,
            'last_modified_by' => NULL, 'last_modified_date' => NULL,
            'role' => 'ROLE_ADMIN');
        $data[] = array(
            'id' => 4, 'login' => 'administrator',
            'password_hash' => '$2a$10$mE.qfsV0mji5NcKhb:0w.z4ueI/.bDWbj0T1BYyqP481kGGarKLG',
            'first_name' => 'Admin', 'last_name' => 'Admin', 'email' => 'admin@localhost',
            'activated' => 0, 'activation_key' => NULL, 'reset_key' => NULL,
            'created_date' => '2015-04-39 21:48:37', 'reset_date' => NULL,
            'last_modified_by' => NULL, 'last_modified_date' => NULL,
            'role' => 'ROLE_USER');
        return $data;
    }

    private function buildFindAllData() {
        $data = $this->buildFindOneData();
        $data[] = array(
            'id' => 17, 'login' => 'chullupabatman',
            'password_hash' => '$2a$10$fd27ad9546b355d167jb1*BNR7qTDYyVTj/7BqFtdNjAIQMwKBbKe',
            'first_name' => 'batman', 'last_name' => 'chullupa', 'email' => 'chullupabatman@msu.edu',
            'activated' => 1, 'activation_key' => NULL, 'reset_key' => NULL,
            'created_date' => '2015-12-22 14:31:54', 'reset_date' => NULL,
            'last_modified_by' => NULL, 'last_modified_date' => NULL,
            'role' => 'ROLE_USER');
        return $data;
    }

    private function buildFindOneUser() {
        $user = array(
            'id' => 4, 'login' => 'administrator',
            'password_hash' => '$2a$10$mE.qfsV0mji5NcKhb:0w.z4ueI/.bDWbj0T1BYyqP481kGGarKLG',
            'first_name' => 'Admin', 'last_name' => 'Admin', 'email' => 'admin@localhost',
            'activated' => 0, 'activation_key' => null,
            'reset_key' => null,
            'created_date' => '2015-04-39 21:48:37',
            'reset_date' => null,
            'last_modified_by' => null,
            'last_modified_date' => null,
            'role' => array('ROLE_ADMIN', 'ROLE_USER'));
        return $user;
    }

    private function buildFindAllUsers() {
        $users['administrator'] = $this->buildFindOneUser();
        $users['chullupabatman'] = array(
            'id' => 17,
            'login' => 'chullupabatman',
            'password_hash' => '$2a$10$fd27ad9546b355d167jb1*BNR7qTDYyVTj/7BqFtdNjAIQMwKBbKe',
            'first_name' => 'batman',
            'last_name' => 'chullupa',
            'email' => 'chullupabatman@msu.edu',
            'activated' => 1,
            'activation_key' => null,
            'reset_key' => null,
            'created_date' => '2015-12-22 14:31:54',
            'reset_date' => null,
            'last_modified_by' => null,
            'last_modified_date' => null,
            'role' => array('ROLE_USER'));
        return $users;
    }
}
