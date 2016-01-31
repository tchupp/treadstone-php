<?php

namespace Test\Unit;

use Api\Database\UserRepository;
use Api\Model\User;
use Api\Security\BCryptPasswordEncoder;
use Api\Service\UserService;
use Api\Service\Util\RandomUtil;
use Exception;
use Phake;
use PHPUnit_Framework_TestCase;

class UserServiceTest extends PHPUnit_Framework_TestCase {

    public function testAutowire() {
        $userService = UserService::autowire();

        $this->assertAttributeInstanceOf(UserRepository::class, 'userRepository', $userService);
        $this->assertAttributeInstanceOf(BCryptPasswordEncoder::class, 'passwordEncoder', $userService);
        $this->assertAttributeInstanceOf(RandomUtil::class, 'randomUtil', $userService);
    }

    public function testCreateUserInformationPassesCorrectUserArrayToUserRepository() {
        $login = 'chuppthe';
        $password = 'awesomePassword';
        $hash = 'hashedPassword';
        $firstName = 'theo';
        $lastName = 'chupp';
        $email = 'theo@thisiscool.com';
        $activationKey = 'jibechansmoob123love';

        $user = new User($login, $hash, $email, $firstName, $lastName,
            false, $activationKey, array('ROLE_USER'));

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

        $actualUser = $userService->createUserInformation($login, $password, $firstName, $lastName, $email);
        $this->assertEquals($user, $actualUser);

        Phake::verify($userRepository)->save($user);
    }

    public function testActivateRegistrationCallsFindOneByActivationKeyAndSaveOnUserRepository() {
        $user = UserRepositoryTest::buildFindOneUser();
        $activationKey = $user->getActivationKey();

        $passwordEncoder = Phake::mock('Api\Security\BCryptPasswordEncoder');
        $userRepository = Phake::mock('Api\Database\UserRepository');
        $randomUtil = Phake::mock('Api\Service\Util\RandomUtil');

        Phake::when($userRepository)
            ->findOneByActivationKey($activationKey)
            ->thenReturn($user);

        $userService = new UserService($userRepository, $passwordEncoder, $randomUtil);

        $user->setActivated(true);
        $user->setActivationKey(null);

        $this->assertSame($user, $userService->activateRegistration($activationKey));

        Phake::verify($userRepository)->update($user);
    }

    public function testActivateRegistrationDoesNotCallSaveOnUserRepositoryIfNoUserIsFound() {
        $activationKey = 'jibechansmoob123love';
        $user = null;

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

    public function testChangePasswordCallsUpdateOnUserRepositoryWithCorrectUser() {
        $user = UserRepositoryTest::buildFindOneUser();
        $login = $user->getLogin();
        $password = 'awesomePassword';
        $passwordHash = 'awesomePassword$H4sH3D';

        $userRepository = Phake::mock('Api\Database\UserRepository');
        $passwordEncoder = Phake::mock('Api\Security\BCryptPasswordEncoder');
        $randomUtil = Phake::mock('Api\Service\Util\RandomUtil');

        $userService = new UserService($userRepository, $passwordEncoder, $randomUtil);

        Phake::when($userRepository)
            ->findOneByLogin($login)
            ->thenReturn($user);

        Phake::when($passwordEncoder)
            ->encode($password)
            ->thenReturn($passwordHash);

        $userService->changePassword($login, $password);

        $user->setPassword($passwordHash);

        Phake::verify($userRepository)->update($user);
    }

    public function testChangePasswordThrowsExceptionIfUserIsNotFound() {
        $login = 'awesomeLogin';
        $password = 'awesomePassword';

        $userRepository = Phake::mock('Api\Database\UserRepository');
        $passwordEncoder = Phake::mock('Api\Security\BCryptPasswordEncoder');
        $randomUtil = Phake::mock('Api\Service\Util\RandomUtil');

        $userService = new UserService($userRepository, $passwordEncoder, $randomUtil);

        Phake::when($userRepository)
            ->findOneByLogin($login)
            ->thenReturn(null);

        try {
            $userService->changePassword($login, $password);
        } catch (Exception $ex) {
            $expectedCode = 500;
            $expectedMessage = 'User not found';

            $this->assertEquals($expectedCode, $ex->getCode());
            $this->assertEquals($expectedMessage, $ex->getMessage());
        }
    }
}
