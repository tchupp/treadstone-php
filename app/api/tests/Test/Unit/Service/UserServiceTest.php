<?php

namespace Test\Unit;

use Api\Database\UserRepository;
use Api\Security\BCryptPasswordEncoder;
use Api\Service\UserService;
use Api\Service\Util\RandomUtil;
use Phake;
use Test\TreadstoneTestCase;

class UserServiceTest extends TreadstoneTestCase {

    public function testAutowire() {
        $userService = UserService::autowire();

        $userRepository = $this->getPrivateProperty($userService, 'userRepository');
        $passwordEncoder = $this->getPrivateProperty($userService, 'passwordEncoder');
        $randomUtil = $this->getPrivateProperty($userService, 'randomUtil');

        $this->assertEquals(UserRepository::class, get_class($userRepository));
        $this->assertEquals(BCryptPasswordEncoder::class, get_class($passwordEncoder));
        $this->assertEquals(RandomUtil::class, get_class($randomUtil));
    }

    public function testCreateUserInformationCallsEncodeOnPasswordEncoder() {
        $password = 'awesomePassword';

        $userRepository = Phake::mock('Api\Database\UserRepository');
        $passwordEncoder = Phake::mock('Api\Security\BCryptPasswordEncoder');
        $randomUtil = Phake::mock('Api\Service\Util\RandomUtil');

        $userService = new UserService($userRepository, $passwordEncoder, $randomUtil);

        $userService->createUserInformation('', $password, '', '', '');

        Phake::verify($passwordEncoder)->encode($password);
    }

    public function testCreateUserInformationPassesCorrectUserArrayToUserRepository() {
        $login = 'chuppthe';
        $password = 'awesomePassword';
        $hash = 'hashedPassword';
        $firstName = 'theo';
        $lastName = 'chupp';
        $email = 'theo@thisiscool.com';
        $activationKey = 'jibechansmoob123love';

        $user = array('login' => $login, 'password' => $hash,
            'firstName' => $firstName, 'lastName' => $lastName,
            'email' => $email, 'activated' => false,
            'activationKey' => $activationKey,
            'role' => array('ROLE_USER'));

        $passwordEncoder = Phake::mock('Api\Security\BCryptPasswordEncoder');
        $userRepository = Phake::mock('Api\Database\UserRepository');
        $randomUtil = Phake::mock('Api\Service\Util\RandomUtil');

        Phake::when($passwordEncoder)
            ->encode($password)
            ->thenReturn($hash);

        Phake::when($randomUtil)
            ->generateActivationKey()
            ->thenReturn($activationKey);

        $userService = new UserService($userRepository, $passwordEncoder, $randomUtil);

        $userService->createUserInformation($login, $password, $firstName, $lastName, $email);

        Phake::verify($userRepository)->save($user);
    }
}
