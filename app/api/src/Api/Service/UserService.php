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

        $user = new User($login, $encodedPassword, $email, $firstName, $lastName,
            false, $activationKey, array('ROLE_USER'));

        $this->userRepository->save($user);

        return $user;
    }

    public function activateRegistration($key) {
        $user = $this->userRepository->findOneByActivationKey($key);

        if (!empty($user)) {
            $user->setActivated(true);
            $user->setActivationKey(null);

            $this->userRepository->update($user);
        }
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
}
