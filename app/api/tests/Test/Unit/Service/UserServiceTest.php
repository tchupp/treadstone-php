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
            'first_name' => $firstName, 'last_name' => $lastName,
            'email' => $email,
            'activated' => false, 'activation_key' => $activationKey,
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

    public function testActivateRegistrationCallsFindOneByActivationKeyAndSaveOnUserRepository() {
        $user = $this->buildFindOneUser();
        $activationKey = $user['activation_key'];

        $passwordEncoder = Phake::mock('Api\Security\BCryptPasswordEncoder');
        $userRepository = Phake::mock('Api\Database\UserRepository');
        $randomUtil = Phake::mock('Api\Service\Util\RandomUtil');

        Phake::when($userRepository)
            ->findOneByActivationKey($activationKey)
            ->thenReturn($user);

        $userService = new UserService($userRepository, $passwordEncoder, $randomUtil);

        $user['activated'] = true;
        $user['activation_key'] = null;

        $this->assertSame($this->buildSavedUser(), $userService->activateRegistration($activationKey));

        Phake::verify($userRepository)->update($this->buildSavedUser());
    }

    public function testActivateRegistrationDoesNotCallSaveOnUserRepositoryIfNoUserIsFound() {
        $activationKey = 'jibechansmoob123love';
        $user = array();

        $passwordEncoder = Phake::mock('Api\Security\BCryptPasswordEncoder');
        $userRepository = Phake::mock('Api\Database\UserRepository');
        $randomUtil = Phake::mock('Api\Service\Util\RandomUtil');

        Phake::when($userRepository)
            ->findOneByActivationKey($activationKey)
            ->thenReturn($user);

        $userService = new UserService($userRepository, $passwordEncoder, $randomUtil);

        $this->assertSame($user, $userService->activateRegistration($activationKey));

        Phake::verify($userRepository, Phake::never())->update(Phake::anyParameters());
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

    private function buildSavedUser() {
        $user = array(
            'login' => 'administrator', 'password' => '$2a$10$mE.qfsV0mji5NcKhb:0w.z4ueI/.bDWbj0T1BYyqP481kGGarKLG',
            'first_name' => 'Admin', 'last_name' => 'Admin', 'email' => 'admin@localhost',
            'activated' => true, 'activation_key' => null
        );
        return $user;
    }
}
