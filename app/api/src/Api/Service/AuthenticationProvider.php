<?php

namespace Api\Service;

use Api\Security\BCryptPasswordEncoder;
use Exception;

class AuthenticationProvider {

    private $userDetailService;
    private $passwordEncoder;

    public function __construct(UserDetailsService $userDetailService, BCryptPasswordEncoder $passwordEncoder) {
        $this->userDetailService = $userDetailService;
        $this->passwordEncoder = $passwordEncoder;
    }

    public function authenticate($login, $password) {
        $user = $this->userDetailService->loadUserByLogin($login);

        $passwordHash = $user['password'];
        if (!$this->passwordEncoder->verify($passwordHash, $password)) {
            throw new Exception("Invalid username/password");
        }
        return $user;
    }
}
