<?php

namespace Api\Web\Rest;

use Api\Application;
use Api\Security\AuthenticationProvider;
use Api\Security\TokenProvider;
use Exception;

class UserXAuthTokenController {

    public static function registerApi(Application $app) {
        $app->post('/authenticate', self::authenticate($app));
    }

    public static function documentation() {
        $authSchema = ['login' => 'string', 'password' => 'string'];
        $tokenSchema = ['expires' => 'int', 'authToken' => 'string'];
        $errorSchema = ['status' => 'int', 'statusText' => 'string', 'description' => 'string'];

        $docs[] = ['uri'       => '/authenticate', 'method' => 'POST',
                   'request'   => ['body' => $authSchema],
                   'responses' => [
                       ['status' => 200,
                        'body'   => $tokenSchema],
                       ['status' => 401,
                        'body'   => $errorSchema]
                   ]];
        return $docs;
    }

    private static function authenticate(Application $app) {
        return function () use ($app) {
            $request = $app->request;
            $response = $app->response;

            $body = json_decode($request->getBody());

            $login = $body->login;
            $password = $body->password;

            if (empty($login) || empty($password)) {
                throw new Exception("Authentication Failed", 401);
            }

            $authenticationProvider = AuthenticationProvider::autowire();
            $tokenProvider = new TokenProvider();

            $authenticatedUser = $authenticationProvider->authenticate($login, $password);
            $token = $tokenProvider->createToken($authenticatedUser['login'], $authenticatedUser['password']);

            $response->setStatus(200);
            $response->body(json_encode($token, JSON_PRETTY_PRINT));
        };
    }
}
