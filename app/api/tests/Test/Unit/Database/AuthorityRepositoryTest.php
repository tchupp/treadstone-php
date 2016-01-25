<?php

namespace Test\Unit;

use Api\Database\AuthorityRepository;
use Api\Database\DatabaseConnection;
use Phake;
use Test\TreadstoneTestCase;

class AuthorityRepositoryTest extends TreadstoneTestCase {

    public function testAutowire() {
        $authorityRepository = AuthorityRepository::autowire();

        $databaseConnection = $this->getPrivateProperty($authorityRepository, 'databaseConnection');

        $this->assertEquals(DatabaseConnection::class, get_class($databaseConnection));
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
