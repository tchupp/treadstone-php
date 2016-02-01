<?php

namespace Api\Service;

use Api\Database\UserRepository;
use Api\Model\User;
use Api\Security\BCryptPasswordEncoder;
use Api\Service\Util\RandomUtil;
use Exception;

class UserService {

    private $userRepository;

    private $passwordEncoder;

    private $randomUtil;

    public static function autowire() {
        return new UserService(UserRepository::autowire(), new BCryptPasswordEncoder(), new RandomUtil());
    }

    public function __construct(UserRepository $userRepository, BCryptPasswordEncoder $passwordEncoder, RandomUtil $randomUtil) {
        $this->userRepository = $userRepository;
        $this->passwordEncoder = $passwordEncoder;
        $this->randomUtil = $randomUtil;
    }

    public function createUserInformation($login, $password, $firstName, $lastName, $email) {
        $encodedPassword = $this->passwordEncoder->encode($password);
        $activationKey = $this->randomUtil->generateActivationKey();

        $activated = false;
        $resetKey = null;
        $roles = array('ROLE_USER');

        $user = new User($login, $encodedPassword, $email, $firstName, $lastName,
            $activated, $activationKey, $resetKey, $roles);

        $this->userRepository->save($user);
        return $user;
    }

    public function activateRegistration($key) {
        $user = $this->userRepository->findOneByActivationKey($key);
        if (empty($user)) {
            throw new Exception('User not found', 400);
        }
        $user->setActivated(true);
        $user->setActivationKey(null);

        $this->userRepository->update($user);
        return $user;
    }

    public function changePassword($login, $password) {
        $user = $this->userRepository->findOneByLogin($login);
        if (empty($user)) {
            throw new Exception('User not found', 500);
        }
        $passwordHash = $this->passwordEncoder->encode($password);
        $user->setPassword($passwordHash);

        $this->userRepository->update($user);
    }

    public function updateUserInformation($login, $email, $firstName, $lastName) {
        $user = $this->userRepository->findOneByLogin($login);
        if (empty($user)) {
            throw new Exception('User not found', 404);
        }
        $user->setEmail($email);
        $user->setFirstName($firstName);
        $user->setLastName($lastName);

        $this->userRepository->update($user);
    }

    public function requestPasswordReset($email) {
        $user = $this->userRepository->findOneByEmail($email);
        if (empty($user)) {
            throw new Exception('User not found', 404);
        }
        if (!$user->getActivated()) {
            throw new Exception('User not activated', 400);
        }
        $resetKey = $this->randomUtil->generateResetKey();

        $user->setResetKey($resetKey);
        $this->userRepository->update($user);

        return $user;
    }

    public function completePasswordReset($password, $resetKey) {
        $user = $this->userRepository->findOneByResetKey($resetKey);
        if (empty($user)) {
            throw new Exception('User not found', 400);
        }
        $passwordHash = $this->passwordEncoder->encode($password);
        $user->setResetKey(null);
        $user->setPassword($passwordHash);

        $this->userRepository->update($user);
        return $user;
    }
}
