<?php

namespace Api\Web\Rest;

use Api\Database\DatabaseConnection;
use Api\Database\UserRepository;
use Api\Security\BCryptPasswordEncoder;
use Api\Service\UserService;
use Api\Service\Util\RandomUtil;
use Slim\Slim;

class AccountResource {

    /**
     * @param $app Slim context to register too
     */
    public static function registerApi($app) {
        $app->post('/register', self::registerAccount($app));
    }

    /**
     * @param $app Slim
     * @return \Closure
     */
    private static function registerAccount($app) {
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

            if (!empty($userRepository->findOneByLogin($login))) {
                $response->status(400);
                $response->body("login already in use");
                return;
            }

            if (!empty($userRepository->findOneByEmail($email))) {
                $response->status(400);
                $response->body("e-mail address already in use");
                return;
            }

            $userService = new UserService($userRepository, new BCryptPasswordEncoder(), new RandomUtil());
            $userService->createUserInformation($login, $password, $firstName, $lastName, $email);

            $response->status(201);
        };
    }
}
