<?php

namespace Api\Service;

use Api\Database\UserRepository;
use Exception;

class UserDetailsService {

    private $userRepository;

    public static function autowire() {
        return new UserDetailsService(UserRepository::autowire());
    }

    public function __construct(UserRepository $userRepository) {
        $this->userRepository = $userRepository;
    }

    public function loadUserByLogin($login) {
        $userFromDatabase = $this->userRepository->findOneByLogin($login);

        if (empty($userFromDatabase)) {
            throw new Exception("User " . $login . " was not found in the database", 404);
        }
        if ($userFromDatabase->getActivated() != true) {
            throw new Exception("User " . $login . " was not activated", 401);
        }

        $user = ['login'    => $userFromDatabase->getLogin(),
                 'password' => $userFromDatabase->getPassword(),
                 'roles'    => $userFromDatabase->getRoles()];

        return $user;
    }
}
