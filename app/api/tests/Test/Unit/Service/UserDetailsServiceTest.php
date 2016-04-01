<?php

namespace Test\Unit;

use Api\Database\UserRepository;
use Api\Service\UserDetailsService;
use Exception;
use Phake;
use PHPUnit_Framework_TestCase;

class UserDetailsServiceTest extends PHPUnit_Framework_TestCase {

    public function testAutowire() {
        $userDetailsService = UserDetailsService::autowire();

        $this->assertAttributeInstanceOf(UserRepository::class, 'userRepository', $userDetailsService);
    }

    public function testLoadUserByLoginReturnsArrayWithOneUsernameAndPasswordAndRolesArray() {
        $user = UserRepositoryTest::buildFindOneUser();
        $login = $user->getLogin();
        $password = $user->getPassword();
        $roles = $user->getRoles();
        $user->setActivated(true);

        $userRepository = Phake::mock('Api\Database\UserRepository');
        Phake::when($userRepository)
            ->findOneByLogin($login)
            ->thenReturn($user);

        $userDetailsService = new UserDetailsService($userRepository);

        $userDetails = $userDetailsService->loadUserByLogin($login);

        $this->assertEquals(3, sizeof($userDetails));
        $this->assertEquals($login, $userDetails['login']);
        $this->assertEquals($password, $userDetails['password']);
        $this->assertEquals($roles, $userDetails['roles']);
    }

    public function testLoadUserByLoginThrowsErrorIfUserDoesNotExist() {
        $login = "subadooo";

        $userRepository = Phake::mock('Api\Database\UserRepository');
        Phake::when($userRepository)
            ->findOneByLogin($login)
            ->thenReturn(null);

        $userDetailsService = new UserDetailsService($userRepository);

        try {
            $userDetailsService->loadUserByLogin($login);

            $this->fail("Should have thrown an exception");
        } catch (Exception $ex) {
            $expectedMessage = "User '$login' was not found in the database";
            $expectedCode = 404;

            $this->assertEquals($expectedMessage, $ex->getMessage());
            $this->assertEquals($expectedCode, $ex->getCode());
        }
    }

    public function testLoadUserByLoginThrowsExceptionIfUserIsNotActivated() {
        $user = UserRepositoryTest::buildFindOneUser();
        $login = $user->getLogin();
        $user->setActivated(false);

        $userRepository = Phake::mock('Api\Database\UserRepository');
        Phake::when($userRepository)
            ->findOneByLogin($login)
            ->thenReturn($user);

        $userDetailsService = new UserDetailsService($userRepository);

        try {
            $userDetailsService->loadUserByLogin($login);

            $this->fail("Should have thrown an exception");
        } catch (Exception $ex) {
            $expectedMessage = "User '$login' was not activated";
            $expectedCode = 401;

            $this->assertEquals($expectedMessage, $ex->getMessage());
            $this->assertEquals($expectedCode, $ex->getCode());
        }
    }
}
