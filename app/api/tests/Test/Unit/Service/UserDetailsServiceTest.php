<?php

namespace Test\Unit;

use Api\Database\UserRepository;
use Api\Service\UserDetailsService;
use Exception;
use Phake;
use Test\TreadstoneTestCase;

class UserDetailsServiceTest extends TreadstoneTestCase {

    public function testAutowire() {
        $userDetailsService = UserDetailsService::autowire();

        $userRepository = $this->getPrivateProperty($userDetailsService, 'userRepository');

        $this->assertEquals(UserRepository::class, get_class($userRepository));
    }

    public function testLoadUserByLoginReturnsArrayWithOneUsernameAndPassword() {
        $login = "chuppthe";
        $password = "$2a$10hergblargimmapassword";

        $user = array(
            "id" => 1, "login" => $login, "password_hash" => $password,
            "first_name" => "System", "last_name" => "System", "email" => "system@localhost",
            "activated" => 1, "activation_key" => NULL,
            "reset_key" => NULL, "created_date" => "2015-08-18 21:48:37",
            "reset_date" => NULL, "last_modified_by" => NULL, "last_modified_date" => NULL);

        $userRepository = Phake::mock('Api\Database\UserRepository');
        Phake::when($userRepository)->findOneByLogin($login)->thenReturn($user);

        $userDetailsService = new UserDetailsService($userRepository);

        $userDetails = $userDetailsService->loadUserByLogin($login);

        $this->assertEquals(2, sizeof($userDetails));
        $this->assertEquals($login, $userDetails['login']);
        $this->assertEquals($password, $userDetails['password']);
    }

    public function testLoadUserByLoginThrowsErrorIfUserDoesNotExist() {
        $login = "subadooo";

        $user = array();

        $userRepository = Phake::mock('Api\Database\UserRepository');
        Phake::when($userRepository)->findOneByLogin($login)->thenReturn($user);

        $userDetailsService = new UserDetailsService($userRepository);

        try {
            $userDetailsService->loadUserByLogin($login);

            $this->fail("Should have thrown an exception");
        } catch (Exception $ex) {
            $expectedMessage = "User " . $login . " was not found in the database";
            $expectedCode = 404;

            $this->assertEquals($expectedMessage, $ex->getMessage());
            $this->assertEquals($expectedCode, $ex->getCode());
        }
    }

    public function testLoadUserByLoginThrowsExceptionIfUserIsNotActivated() {
        $login = "chuppthe";
        $password = "$2a$10hergblargimmapassword";

        $user = array("login" => $login, "password_hash" => $password,
            "activated" => 0);

        $userRepository = Phake::mock('Api\Database\UserRepository');
        Phake::when($userRepository)->findOneByLogin($login)->thenReturn($user);

        $userDetailsService = new UserDetailsService($userRepository);

        try {
            $userDetailsService->loadUserByLogin($login);

            $this->fail("Should have thrown an exception");
        } catch (Exception $ex) {
            $expectedMessage = "User " . $login . " was not activated";
            $expectedCode = 401;

            $this->assertEquals($expectedMessage, $ex->getMessage());
            $this->assertEquals($expectedCode, $ex->getCode());
        }
    }
}
