<?php

namespace Test\Unit;

use Api\Database\AuthorityRepository;
use Api\Database\DatabaseConnection;
use Phake;
use PHPUnit_Framework_TestCase;

class AuthorityRepositoryTest extends PHPUnit_Framework_TestCase {

    public function testAutowire() {
        $authorityRepository = AuthorityRepository::autowire();

        $this->assertAttributeInstanceOf(DatabaseConnection::class, 'databaseConnection', $authorityRepository);
    }

    public function testFindOneCallsBindAndQueryOnDatabaseConnection() {
        $query = "SELECT name
                  FROM treadstone_authority
                  WHERE name = :name";

        $nameKey = "name";
        $nameValue = "ROLE_USER";

        $auth = array('name' => $nameValue);
        $data = array($auth);

        $databaseConnection = Phake::mock('Api\Database\DatabaseConnection');
        Phake::when($databaseConnection)
            ->query($query)
            ->thenReturn($data);

        $authorityRepository = new AuthorityRepository($databaseConnection);

        $this->assertSame($auth, $authorityRepository->findOne($nameValue));

        Phake::inOrder(
            Phake::verify($databaseConnection)->bind($nameKey, $nameValue),
            Phake::verify($databaseConnection)->query($query)
        );
    }
}
