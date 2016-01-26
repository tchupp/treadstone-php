<?php

namespace Test\Unit;

use Api\Database\DatabaseConnection;
use Api\Database\UserRepository;
use Phake;
use Test\TreadstoneTestCase;

class UserRepositoryTest extends TreadstoneTestCase {

    public function testAutowire() {
        $userRepository = UserRepository::autowire();

        $databaseConnection = $this->getPrivateProperty($userRepository, 'databaseConnection');

        $this->assertEquals(DatabaseConnection::class, get_class($databaseConnection));
    }

    public function testSaveCallsQueryOnDatabaseConnectionWithCorrectQueryCorrectInput() {
        $query = "INSERT
                  INTO treadstone_user(login, password_hash, first_name, last_name, email, activated, activation_key)
                  VALUES(:login, :password, :first_name, :last_name, :email, :activated, :activation_key)";

        $data = $user = array('login' => 'chuppthe', 'password' => 'super',
            'first_name' => 'theo', 'last_name' => 'chupp',
            'email' => 'theo@thisiscool.com', 'activated' => false,
            'activation_key' => 'blahHA473810ji903h1');
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
                  VALUES(:login, :password, :first_name, :last_name, :email, :activated, :activation_key)";
        $roleQuery = "INSERT
                  INTO treadstone_user_authority(user_id, authority_name)
                  VALUES(:id, :role)";

        $roleUser = 'ROLE_USER';
        $roleAdmin = 'ROLE_ADMIN';
        $user = array('login' => 'chuppthe', 'password' => 'super',
            'first_name' => 'theo', 'last_name' => 'chupp',
            'email' => 'theo@thisiscool.com', 'activated' => false,
            'activation_key' => 'blahHA473810ji903h1',
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
        $user = array('login' => 'chuppthe', 'first_name' => 'theo',
            'last_name' => 'chupp', 'email' => 'theo@thisiscool.com');

        $databaseConnection = Phake::mock('Api\Database\DatabaseConnection');

        $userRepository = new UserRepository($databaseConnection);

        $this->assertEquals(0, $userRepository->save($user));
        Phake::verifyNoInteraction($databaseConnection);
    }

    public function testUpdateCallsQueryOnDatabaseWithCorrectQueryCorrectInput() {
        $query = "UPDATE treadstone_user
                  SET password_hash = :password,
                      first_name = :first_name, last_name = :last_name, email = :email,
                      activated = :activated, activation_key = :activation_key
                  WHERE login = :login";

        $user = array('login' => 'chuppthe', 'password' => 'super',
            'first_name' => 'theo', 'last_name' => 'chupp',
            'email' => 'theo@thisiscool.com',
            'activated' => false, 'activation_key' => 'blahHA473810ji903h1');

        $rowsModified = 1;

        $databaseConnection = Phake::mock('Api\Database\DatabaseConnection');
        Phake::when($databaseConnection)
            ->query($query)
            ->thenReturn($rowsModified);

        $userRepository = new UserRepository($databaseConnection);

        $this->assertEquals($rowsModified, $userRepository->update($user));

        Phake::inOrder(
            Phake::verify($databaseConnection)->bindMore($user),
            Phake::verify($databaseConnection)->query($query)
        );
    }

    public function testUpdateDoesNotCallQueryOnDatabaseConnectionIfUserIsMalformed() {
        $loginMissing = array('password' => 'super',
            'first_name' => 'theo', 'last_name' => 'chupp',
            'email' => 'theo@thisiscool.com',
            'activated' => false, 'activation_key' => 'blahHA473810ji903h1');
        $passwordMissing = array('login' => 'chuppthe',
            'first_name' => 'theo', 'last_name' => 'chupp',
            'email' => 'theo@thisiscool.com',
            'activated' => false, 'activation_key' => 'blahHA473810ji903h1');
        $firstNameMissing = array('login' => 'chuppthe', 'password' => 'super',
            'last_name' => 'chupp',
            'email' => 'theo@thisiscool.com',
            'activated' => false, 'activation_key' => 'blahHA473810ji903h1');
        $lastNameMissing = array('login' => 'chuppthe', 'password' => 'super',
            'first_name' => 'theo',
            'email' => 'theo@thisiscool.com',
            'activated' => false, 'activation_key' => 'blahHA473810ji903h1');
        $emailMissing = array('login' => 'chuppthe', 'password' => 'super',
            'first_name' => 'theo', 'last_name' => 'chupp',
            'activated' => false, 'activation_key' => 'blahHA473810ji903h1');
        $activatedMissing = array('login' => 'chuppthe', 'password' => 'super',
            'first_name' => 'theo', 'last_name' => 'chupp',
            'email' => 'theo@thisiscool.com',
            'activation_key' => 'blahHA473810ji903h1');
        $activationKeyMissing = array('login' => 'chuppthe', 'password' => 'super',
            'first_name' => 'theo', 'last_name' => 'chupp',
            'email' => 'theo@thisiscool.com',
            'activated' => false);

        $databaseConnection = Phake::mock('Api\Database\DatabaseConnection');

        $userRepository = new UserRepository($databaseConnection);

        $this->assertEquals(0, $userRepository->update($loginMissing));
        Phake::verifyNoInteraction($databaseConnection);

        $this->assertEquals(0, $userRepository->update($passwordMissing));
        Phake::verifyNoInteraction($databaseConnection);

        $this->assertEquals(0, $userRepository->update($firstNameMissing));
        Phake::verifyNoInteraction($databaseConnection);

        $this->assertEquals(0, $userRepository->update($lastNameMissing));
        Phake::verifyNoInteraction($databaseConnection);

        $this->assertEquals(0, $userRepository->update($emailMissing));
        Phake::verifyNoInteraction($databaseConnection);

        $this->assertEquals(0, $userRepository->update($activatedMissing));
        Phake::verifyNoInteraction($databaseConnection);

        $this->assertEquals(0, $userRepository->update($activationKeyMissing));
        Phake::verifyNoInteraction($databaseConnection);
    }

    public function testFindAllCallsQueryOnDatabaseConnectionWithCorrectQuery() {
        $query = "SELECT User.login, User.password_hash AS password,
                    User.first_name, User.last_name, User.email,
                    User.activated, User.activation_key,
                    User.reset_key, User.reset_date, Auth.authority_name AS role
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
        $query = "SELECT User.login, User.password_hash AS password,
                    User.first_name, User.last_name, User.email,
                    User.activated, User.activation_key,
                    User.reset_key, User.reset_date, Auth.authority_name AS role
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
        $query = "SELECT User.login, User.password_hash AS password,
                    User.first_name, User.last_name, User.email,
                    User.activated, User.activation_key,
                    User.reset_key, User.reset_date, Auth.authority_name AS role
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

    public function testFindOneByActivationKeyCallsQueryOnDatabaseConnectionWithCorrectQuery() {
        $query = "SELECT User.login, User.password_hash AS password,
                    User.first_name, User.last_name, User.email,
                    User.activated, User.activation_key,
                    User.reset_key, User.reset_date, Auth.authority_name AS role
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
        $this->assertSame($this->buildFindOneUser(),
            $userRepository->findOneByActivationKey($activationKeyValue));

        Phake::inOrder(
            Phake::verify($databaseConnection)->bind($activationKey, $activationKeyValue),
            Phake::verify($databaseConnection)->query($query)
        );
    }

    private function buildFindOneData() {
        $data[] = array(
            'login' => 'administrator',
            'password' => '$2a$10$mE.qfsV0mji5NcKhb:0w.z4ueI/.bDWbj0T1BYyqP481kGGarKLG',
            'first_name' => 'Admin', 'last_name' => 'Admin', 'email' => 'admin@localhost',
            'activated' => 0, 'activation_key' => null,
            'reset_key' => null, 'reset_date' => null,
            'role' => 'ROLE_ADMIN');
        $data[] = array(
            'login' => 'administrator',
            'password' => '$2a$10$mE.qfsV0mji5NcKhb:0w.z4ueI/.bDWbj0T1BYyqP481kGGarKLG',
            'first_name' => 'Admin', 'last_name' => 'Admin', 'email' => 'admin@localhost',
            'activated' => 0, 'activation_key' => null,
            'reset_key' => null, 'reset_date' => null,
            'role' => 'ROLE_USER');
        return $data;
    }

    private function buildFindAllData() {
        $data = $this->buildFindOneData();
        $data[] = array(
            'login' => 'chullupabatman',
            'password' => '$2a$10$fd27ad9546b355d167jb1*BNR7qTDYyVTj/7BqFtdNjAIQMwKBbKe',
            'first_name' => 'batman', 'last_name' => 'chullupa', 'email' => 'chullupabatman@msu.edu',
            'activated' => 1, 'activation_key' => null,
            'reset_key' => null, 'reset_date' => null,
            'role' => 'ROLE_USER');
        return $data;
    }

    private function buildFindOneUser() {
        $user = array(
            'login' => 'administrator', 'password' => '$2a$10$mE.qfsV0mji5NcKhb:0w.z4ueI/.bDWbj0T1BYyqP481kGGarKLG',
            'first_name' => 'Admin', 'last_name' => 'Admin', 'email' => 'admin@localhost',
            'activated' => 0, 'activation_key' => null,
            'reset_key' => null, 'reset_date' => null,
            'role' => array('ROLE_ADMIN', 'ROLE_USER'));
        return $user;
    }

    private function buildFindAllUsers() {
        $users['administrator'] = $this->buildFindOneUser();
        $users['chullupabatman'] = array(
            'login' => 'chullupabatman', 'password' => '$2a$10$fd27ad9546b355d167jb1*BNR7qTDYyVTj/7BqFtdNjAIQMwKBbKe',
            'first_name' => 'batman', 'last_name' => 'chullupa',
            'email' => 'chullupabatman@msu.edu',
            'activated' => 1, 'activation_key' => null,
            'reset_key' => null, 'reset_date' => null,
            'role' => array('ROLE_USER'));
        return $users;
    }
}
