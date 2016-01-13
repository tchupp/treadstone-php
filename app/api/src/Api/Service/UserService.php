<?php

namespace Api\Service;

use Api\Database\UserRepository;
use Api\Security\BCryptPasswordEncoder;
use Api\Service\Util\RandomUtil;

class UserService {

    private $userRepository;

    private $passwordEncoder;

    private $randomUtil;

    public function __construct(UserRepository $userRepository, BCryptPasswordEncoder $passwordEncoder, RandomUtil $randomUtil) {
        $this->userRepository = $userRepository;
        $this->passwordEncoder = $passwordEncoder;
        $this->randomUtil = $randomUtil;
    }

    public function createUserInformation($login, $password, $firstName, $lastName, $email) {
        $encodedPassword = $this->passwordEncoder->encode($password);
        $activationKey = $this->randomUtil->generateActivationKey();

        $user = array('login' => $login, 'password' => $encodedPassword,
            'firstName' => $firstName, 'lastName' => $lastName,
            'email' => $email, 'activated' => false,
            'activationKey' => $activationKey,
            'role' => array('ROLE_USER'));

        $this->userRepository->save($user);

        return $user;
    }
}
