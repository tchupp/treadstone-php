<?php

namespace Test\Unit;

use Api\Database\UserRepository;
use Phake;
use PHPUnit_Framework_TestCase;

class UserRepositoryTest extends PHPUnit_Framework_TestCase {

    public function testFindAllCallsQueryOnDatabaseConnectionWithCorrectQuery() {
        $query = "SELECT * FROM TREADSTONE_USER";

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

    public function testFindOneByLoginCallsQueryOnDatabaseConnectionWithCorrectQuery_UserExists() {
        $query = "SELECT * FROM TREADSTONE_USER WHERE login = :login LIMIT 1";

        $data = array(
            'chuppthe' => array('login' => 'chuppthe', 'email' => 'theo@theoiscool.com'));

        $databaseConnection = Phake::mock('Api\Database\DatabaseConnection');
        Phake::when($databaseConnection)
            ->query($query)
            ->thenReturn($data);

        $userRepository = new UserRepository($databaseConnection);

        $loginKey = 'login';
        $loginValue = 'chuppthe';
        $this->assertSame($data, $userRepository->findOneByLogin($loginValue));

        Phake::inOrder(
            Phake::verify($databaseConnection)->bind($loginKey, $loginValue),
            Phake::verify($databaseConnection)->query($query)
        );
    }
}
