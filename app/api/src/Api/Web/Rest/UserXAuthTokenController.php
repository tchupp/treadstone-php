<?php

namespace Api\Web\Rest;

use Api\Application;
use Api\Database\DatabaseConnection;
use Api\Database\UserRepository;
use Api\Service\UserDetailsService;

class UserXAuthTokenController {

    public static function registerApi(Application $app) {
        $app->post('/authenticate', self::authorize($app));
    }

    private static function authorize(Application $app) {
        return function () use ($app) {
            $request = $app->request;
            $response = $app->response;

            $body = json_decode($request->getBody());

            $login = $body->login;
            $password = $body->password;

            if (empty($login) || empty($password)) {
                $response->status(401);
                return;
            }

            //TODO: finish XAuthController

            $databaseConnection = new DatabaseConnection();
            $userRepository = new UserRepository($databaseConnection);
            $userDetailsService = new UserDetailsService($userRepository);
        };
    }
}
