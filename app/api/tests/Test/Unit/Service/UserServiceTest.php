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

    public function testCreateUserInformationPassesCorrectUserToUserRepository() {
        $login = 'chuppthe';
        $password = 'awesomePassword';
        $hash = 'hashedPassword';
        $firstName = 'theo';
        $lastName = 'chupp';
        $email = 'theo@thisiscool.com';
        $activationKey = 'jibechansmoob123love';

        $user = new User($login, $hash, $email, $firstName, $lastName, false, $activationKey, null, array('ROLE_USER'));

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

    public function testActivateRegistrationThrowsExceptionIfNoUserIsFound() {
        $activationKey = 'jibechansmoob123love';
        $user = null;

        $userRepository = Phake::mock('Api\Database\UserRepository');
        $passwordEncoder = Phake::mock('Api\Security\BCryptPasswordEncoder');
        $randomUtil = Phake::mock('Api\Service\Util\RandomUtil');

        Phake::when($userRepository)
            ->findOneByActivationKey($activationKey)
            ->thenReturn($user);

        $userService = new UserService($userRepository, $passwordEncoder, $randomUtil);

        try {
            $userService->activateRegistration($activationKey);

            $this->fail('Should of thrown exception');
        } catch (Exception $ex) {
            $this->assertEquals(400, $ex->getCode());
            $this->assertEquals('User not found', $ex->getMessage());
        }

        Phake::verifyNoFurtherInteraction($userRepository);
    }

    public function testChangePasswordCallsUpdateOnUserRepositoryWithCorrectUser() {
        $user = UserRepositoryTest::buildFindOneUser();
        $login = $user->getLogin();
        $password = 'awesomePassword';
        $passwordHash = 'awesomePassword$H4sH3D';

        $userRepository = Phake::mock('Api\Database\UserRepository');
        $passwordEncoder = Phake::mock('Api\Security\BCryptPasswordEncoder');
        $randomUtil = Phake::mock('Api\Service\Util\RandomUtil');

        Phake::when($userRepository)
            ->findOneByLogin($login)
            ->thenReturn($user);

        Phake::when($passwordEncoder)
            ->encode($password)
            ->thenReturn($passwordHash);

        $userService = new UserService($userRepository, $passwordEncoder, $randomUtil);

        $userService->changePassword($login, $password);

        Phake::verify($userRepository)->update(Phake::capture($user));

        $this->assertEquals($passwordHash, $user->getPassword());
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

    public function testUpdateUserInformationCallsFindOneByLoginThenUpdates() {
        $login = 'chuppthe';
        $email = 'chuppthe@msu.edu';
        $firstName = 'Theo';
        $lastName = 'Chupp';
        $user = UserRepositoryTest::buildFindOneUser();

        $userRepository = Phake::mock('Api\Database\UserRepository');
        $passwordEncoder = Phake::mock('Api\Security\BCryptPasswordEncoder');
        $randomUtil = Phake::mock('Api\Service\Util\RandomUtil');

        Phake::when($userRepository)
            ->findOneByLogin($login)
            ->thenReturn($user);

        $userService = new UserService($userRepository, $passwordEncoder, $randomUtil);

        $userService->updateUserInformation($login, $email, $firstName, $lastName);

        Phake::verify($userRepository)->update(Phake::capture($user));

        $this->assertEquals($email, $user->getEmail());
        $this->assertEquals($firstName, $user->getFirstName());
        $this->assertEquals($lastName, $user->getLastName());
    }

    public function testUpdateUserInformationThrowsExceptionIfUserDoesNotExist() {
        $login = 'chuppthe';
        $email = 'chuppthe@msu.edu';
        $firstName = 'Theo';
        $lastName = 'Chupp';
        $user = null;

        $userRepository = Phake::mock('Api\Database\UserRepository');
        $passwordEncoder = Phake::mock('Api\Security\BCryptPasswordEncoder');
        $randomUtil = Phake::mock('Api\Service\Util\RandomUtil');

        Phake::when($userRepository)
            ->findOneByLogin($login)
            ->thenReturn($user);

        $userService = new UserService($userRepository, $passwordEncoder, $randomUtil);

        try {
            $userService->updateUserInformation($login, $email, $firstName, $lastName);

            $this->fail("Should of thrown exception");
        } catch (Exception $ex) {
            $this->assertEquals(404, $ex->getCode());
            $this->assertEquals("User not found", $ex->getMessage());
        }
        Phake::verifyNoFurtherInteraction($userRepository);
    }

    public function testRequestPasswordResetCallsFindOneByEmailGeneratesResetKeyThenUpdatesUser() {
        $user = UserRepositoryTest::buildFindOneUser();
        $user->setActivated(true);
        $email = $user->getEmail();
        $resetKey = '7589n4nb43892bnmr32i';

        $userRepository = Phake::mock('Api\Database\UserRepository');
        $passwordEncoder = Phake::mock('Api\Security\BCryptPasswordEncoder');
        $randomUtil = Phake::mock('Api\Service\Util\RandomUtil');

        Phake::when($userRepository)
            ->findOneByEmail($email)
            ->thenReturn($user);

        Phake::when($randomUtil)
            ->generateResetKey()
            ->thenReturn($resetKey);

        $userService = new UserService($userRepository, $passwordEncoder, $randomUtil);

        $updatedUser = $userService->requestPasswordReset($email);
        $this->assertEquals($resetKey, $updatedUser->getResetKey());

        Phake::verify($userRepository)->update($updatedUser);
    }

    public function testRequestPasswordResetThrowsExceptionIfUserDoesNotExist() {
        $user = null;
        $email = 'chuppthe@msu.edu';

        $userRepository = Phake::mock('Api\Database\UserRepository');
        $passwordEncoder = Phake::mock('Api\Security\BCryptPasswordEncoder');
        $randomUtil = Phake::mock('Api\Service\Util\RandomUtil');

        Phake::when($userRepository)
            ->findOneByEmail($email)
            ->thenReturn($user);

        $userService = new UserService($userRepository, $passwordEncoder, $randomUtil);

        try {
            $userService->requestPasswordReset($email);

            $this->fail('Should of thrown Exception');
        } catch (Exception $ex) {
            $this->assertEquals(404, $ex->getCode());
            $this->assertEquals('User not found', $ex->getMessage());
        }
    }

    public function testRequestPasswordResetThrowsExceptionIfUserIsNotActivated() {
        $user = UserRepositoryTest::buildFindOneUser();
        $user->setActivated(false);
        $email = $user->getEmail();

        $userRepository = Phake::mock('Api\Database\UserRepository');
        $passwordEncoder = Phake::mock('Api\Security\BCryptPasswordEncoder');
        $randomUtil = Phake::mock('Api\Service\Util\RandomUtil');

        Phake::when($userRepository)
            ->findOneByEmail($email)
            ->thenReturn($user);

        $userService = new UserService($userRepository, $passwordEncoder, $randomUtil);

        try {
            $userService->requestPasswordReset($email);

            $this->fail('Should of thrown Exception');
        } catch (Exception $ex) {
            $this->assertEquals(400, $ex->getCode());
            $this->assertEquals('User not activated', $ex->getMessage());
        }
    }

    public function testCompletePasswordReset() {
        $user = UserRepositoryTest::buildFindOneUser();
        $resetKey = '671982t4h782hj7';
        $user->setResetKey($resetKey);

        $password = 'awesomePassword';
        $passwordHash = 'awesomePassword$H4sH3D';

        $userRepository = Phake::mock('Api\Database\UserRepository');
        $passwordEncoder = Phake::mock('Api\Security\BCryptPasswordEncoder');
        $randomUtil = Phake::mock('Api\Service\Util\RandomUtil');

        Phake::when($userRepository)
            ->findOneByResetKey($resetKey)
            ->thenReturn($user);

        Phake::when($passwordEncoder)
            ->encode($password)
            ->thenReturn($passwordHash);

        $userService = new UserService($userRepository, $passwordEncoder, $randomUtil);

        $updatedUser = $userService->completePasswordReset($password, $resetKey);
        $this->assertEquals(null, $updatedUser->getResetKey());
        $this->assertEquals($passwordHash, $updatedUser->getPassword());

        Phake::verify($userRepository)->update($updatedUser);
    }

    public function testCompletePasswordResetThrowsExceptionIfUserDoesNotExist() {
        $user = null;
        $resetKey = '671982t4h782hj7';
        $password = 'awesomePassword';

        $userRepository = Phake::mock('Api\Database\UserRepository');
        $passwordEncoder = Phake::mock('Api\Security\BCryptPasswordEncoder');
        $randomUtil = Phake::mock('Api\Service\Util\RandomUtil');

        Phake::when($userRepository)
            ->findOneByResetKey($resetKey)
            ->thenReturn($user);

        $userService = new UserService($userRepository, $passwordEncoder, $randomUtil);

        try {
            $userService->completePasswordReset($password, $resetKey);

            $this->fail("Should of thrown Exception");
        } catch (Exception $ex) {
            $this->assertEquals(400, $ex->getCode());
            $this->assertEquals('User not found', $ex->getMessage());
        }
    }
}
