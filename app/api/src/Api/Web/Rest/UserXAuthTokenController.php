<?php

namespace Api\Web\Rest;

use Api\Application;
use Api\Security\AuthenticationProvider;
use Api\Security\TokenProvider;

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

            $authenticationProvider = AuthenticationProvider::autowire();
            $tokenProvider = new TokenProvider();

            $authenticatedUser = $authenticationProvider->authenticate($login, $password);
            $token = $tokenProvider->createToken($authenticatedUser['login'], $authenticatedUser['password']);

            $response->status(200);
            $response->body(json_encode($token, JSON_PRETTY_PRINT));
        };
    }
}
