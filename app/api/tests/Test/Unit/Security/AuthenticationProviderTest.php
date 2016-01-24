<?php

namespace Test\Unit;

use Api\Security\AuthenticationProvider;
use Exception;
use Phake;
use PHPUnit_Framework_TestCase;

class AuthenticationProviderTest extends PHPUnit_Framework_TestCase {

    public function testAuthenticateCallsVerifyOnPasswordEncoderWithPasswordFromUserDetailsService() {
        $login = "chuppthe";
        $password = "hahaitsmypassword!";
        $passwordHash = "!its\$a4ash3dpa\$\$w0rd";

        $user = array('login' => $login, 'password' => $passwordHash);

        $userDetailsService = Phake::mock('Api\Service\UserDetailsService');
        $passwordEncoder = Phake::mock('Api\Security\BCryptPasswordEncoder');

        Phake::when($userDetailsService)
            ->loadUserByLogin($login)
            ->thenReturn($user);
        Phake::when($passwordEncoder)
            ->verify($passwordHash, $password)
            ->thenReturn(true);

        $authenticationProvider = new AuthenticationProvider($userDetailsService, $passwordEncoder);

        $this->assertEquals($user, $authenticationProvider->authenticate($login, $password));
    }

    public function testAuthenticateThrowsExceptionWhenVerifyReturnsFalse() {
        $login = "chuppthe";
        $password = "hahaitsmypassword!";
        $passwordHash = "!its\$a4ash3dpa\$\$w0rd";

        $user = array('login' => $login, 'password' => $passwordHash);

        $userDetailsService = Phake::mock('Api\Service\UserDetailsService');
        $passwordEncoder = Phake::mock('Api\Security\BCryptPasswordEncoder');

        Phake::when($userDetailsService)
            ->loadUserByLogin($login)
            ->thenReturn($user);
        Phake::when($passwordEncoder)
            ->verify($passwordHash, $password)
            ->thenReturn(false);

        $authenticationProvider = new AuthenticationProvider($userDetailsService, $passwordEncoder);

        try {
            $authenticationProvider->authenticate($login, $password);

            $this->fail("Exception should have been thrown");
        } catch (Exception $ex) {
            $this->assertEquals("Invalid username/password", $ex->getMessage());
            $this->assertEquals(401, $ex->getCode());
        }
    }
}
