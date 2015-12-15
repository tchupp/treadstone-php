<?php

namespace Test\Unit;

use Api\Service\UserService;
use Phake;
use PHPUnit_Framework_TestCase;

class UserServiceTest extends PHPUnit_Framework_TestCase {

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
            'activationKey' => $activationKey);

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

    /*public function testCreateUserInformationAddsUserIdToUserReturned() {
        $login = 'chuppthe';
        $password = 'awesomePassword';
        $hash = 'hashedPassword';
        $firstName = 'theo';
        $lastName = 'chupp';
        $email = 'theo@thisiscool.com';
        $activationKey = 'jibechansmoob123love';
        $userId = 17;

        $user = array('login' => $login, 'password' => $hash,
            'firstName' => $firstName, 'lastName' => $lastName,
            'email' => $email, 'activated' => false,
            'activationKey' => $activationKey);

        $passwordEncoder = Phake::mock('Api\Security\BCryptPasswordEncoder');
        $userRepository = Phake::mock('Api\Database\UserRepository');
        $randomUtil = Phake::mock('Api\Service\Util\RandomUtil');

        Phake::when($passwordEncoder)
            ->encode($password)
            ->thenReturn($hash);

        Phake::when($randomUtil)
            ->generateActivationKey()
            ->thenReturn($activationKey);

        Phake::when($userRepository)
            ->save($user)
            ->thenReturn($userId);

        $userService = new UserService($userRepository, $passwordEncoder, $randomUtil);

        $actualUser = $userService->createUserInformation($login, $password, $firstName, $lastName, $email);

        $this->assertEquals($userId, $actualUser['id']);
    }*/
}
