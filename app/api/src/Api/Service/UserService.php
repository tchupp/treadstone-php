<?php

namespace Api\Service;


use Api\Database\UserRepository;
use Api\Security\BCryptPasswordEncoder;
use Api\Service\Util\RandomUtil;

class UserService {

    private $userRepository;

    private $passwordEncoder;

    private $randomUtil;

    /**
     * UserService constructor.
     * @param $userRepository UserRepository
     * @param $passwordEncoder BCryptPasswordEncoder
     * @param $randomUtil RandomUtil
     */
    public function __construct($userRepository, $passwordEncoder, $randomUtil) {
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
            'activationKey' => $activationKey);

        $this->userRepository->save($user);

        return $user;
    }
}
