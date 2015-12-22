<?php

namespace Api\Web\Rest;

use Api\Application;
use Api\Database\DatabaseConnection;
use Api\Database\UserRepository;
use Api\Security\BCryptPasswordEncoder;
use Api\Security\TokenProvider;
use Api\Service\AuthenticationProvider;
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

            $databaseConnection = new DatabaseConnection();
            $userRepository = new UserRepository($databaseConnection);
            $userDetailsService = new UserDetailsService($userRepository);

            $passwordEncoder = new BCryptPasswordEncoder();
            $authenticationProvider = new AuthenticationProvider($userDetailsService, $passwordEncoder);

            $tokenProvider = new TokenProvider();

            $authenticatedUser = $authenticationProvider->authenticate($login, $password);
            $token = $tokenProvider->createToken($authenticatedUser['login'], $authenticatedUser['password']);

            $response->status(200);
            $response->body(json_encode($token, JSON_PRETTY_PRINT));
        };
    }
}
