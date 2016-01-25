<?php

namespace Api\Web\Rest;

use Api\Application;
use Api\Database\DatabaseConnection;
use Api\Database\UserRepository;
use Api\Security\BCryptPasswordEncoder;
use Api\Service\UserService;
use Api\Service\Util\RandomUtil;

class AccountResource {

    public static function registerApi(Application $app) {
        $app->post('/register', self::registerAccount($app));

        $app->get('/activate', self::activateAccount($app));
    }

    private static function registerAccount(Application $app) {
        return function () use ($app) {
            $request = $app->request;
            $response = $app->response;

            $body = json_decode($request->getBody());

            $email = $body->email;
            $firstName = $body->firstName;
            $lastName = $body->lastName;
            $login = $body->login;
            $password = $body->password;

            if (empty($email) || empty($firstName) || empty($lastName)
                || empty($login) || empty($password)) {
                $response->status(406);
                return;
            }

            $databaseConnection = new DatabaseConnection();
            $userRepository = new UserRepository($databaseConnection);

            $oneByLogin = $userRepository->findOneByLogin($login);
            if (!empty($oneByLogin)) {
                $response->status(400);
                $response->body("login already in use");
                return;
            }

            $oneByEmail = $userRepository->findOneByEmail($email);
            if (!empty($oneByEmail)) {
                $response->status(400);
                $response->body("e-mail address already in use");
                return;
            }

            $userService = new UserService($userRepository, new BCryptPasswordEncoder(), new RandomUtil());
            $userService->createUserInformation($login, $password, $firstName, $lastName, $email);

            $response->status(201);
        };
    }

    private static function activateAccount(Application $app) {
        return function () use ($app) {
//            $request = $app->request;
//            $response = $app->response;
//            $userService = UserService::autowire();
        };
    }
}
