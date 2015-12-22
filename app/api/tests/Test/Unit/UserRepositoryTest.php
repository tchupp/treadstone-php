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

        $user = array('login' => 'chuppthe', 'password' => 'super',
            'firstName' => 'theo', 'lastName' => 'chupp',
            'email' => 'theo@thisiscool.com', 'activated' => false,
            'activationKey' => 'blahHA473810ji903h1');

        $rowsModified = 1;

        $databaseConnection = Phake::mock('Api\Database\DatabaseConnection');
        Phake::when($databaseConnection)
            ->query($query)
            ->thenReturn($rowsModified);

        $userRepository = new UserRepository($databaseConnection);

        $this->assertEquals($rowsModified, $userRepository->save($user));

        Phake::inOrder(
            Phake::verify($databaseConnection)->bindMore($user),
            Phake::verify($databaseConnection)->query($query)
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
        $query = "SELECT *
                  FROM treadstone_user";

        $data = array(
            '0' => array('login' => 'chuppthe', 'email' => 'theo@theoiscool.com'),
            '1' => array('login' => 'suuuuuuu', 'email' => 'su@wildturkey.com'));

        $databaseConnection = Phake::mock('Api\Database\DatabaseConnection');
        Phake::when($databaseConnection)
            ->query($query)
            ->thenReturn($data);

        $userRepository = new UserRepository($databaseConnection);

        $this->assertSame($data, $userRepository->findAll());
    }

    public function testFindOneByLoginCallsQueryOnDatabaseConnectionWithCorrectQuery() {
        $query = "SELECT *
                  FROM treadstone_user WHERE login = :login LIMIT 1";

        $user = array('login' => 'chuppthe', 'email' => 'theo@theoiscool.com');
        $data = array(
            '0' => $user);

        $databaseConnection = Phake::mock('Api\Database\DatabaseConnection');
        Phake::when($databaseConnection)
            ->query($query)
            ->thenReturn($data);

        $userRepository = new UserRepository($databaseConnection);

        $loginKey = 'login';
        $loginValue = 'chuppthe';
        $this->assertSame($user, $userRepository->findOneByLogin($loginValue));

        Phake::inOrder(
            Phake::verify($databaseConnection)->bind($loginKey, $loginValue),
            Phake::verify($databaseConnection)->query($query)
        );
    }

    public function testFindOneByEmailCallsQueryOnDatabaseConnectionWithCorrectQuery() {
        $query = "SELECT *
                  FROM treadstone_user WHERE email = :email LIMIT 1";

        $data = array(
            '0' => array('login' => 'chuppthe', 'email' => 'theo@theoiscool.com'));

        $databaseConnection = Phake::mock('Api\Database\DatabaseConnection');
        Phake::when($databaseConnection)
            ->query($query)
            ->thenReturn($data);

        $userRepository = new UserRepository($databaseConnection);

        $emailKey = 'email';
        $emailValue = 'theo@theoiscool.com';
        $this->assertSame($data, $userRepository->findOneByEmail($emailValue));

        Phake::inOrder(
            Phake::verify($databaseConnection)->bind($emailKey, $emailValue),
            Phake::verify($databaseConnection)->query($query)
        );
    }
}
