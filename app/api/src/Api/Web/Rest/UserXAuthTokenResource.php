<?php

namespace Api\Web\Rest;

use Api\Application;
use Api\Security\AuthenticationProvider;
use Api\Security\TokenProvider;
use Api\Service\UserDetailsService;
use Exception;

class UserXAuthTokenResource {

    public static $XAUTH_TOKEN_HEADER = "x-auth-token";

    public static function registerApi(Application $app) {
        $app->post('/authenticate', self::authenticate($app));

        $app->get('/renew', self::renew($app));
    }

    public static function documentation() {
        $authSchema = ['login' => 'string', 'password' => 'string'];
        $tokenSchema = ['expires' => 'int', 'authToken' => 'string'];
        $errorSchema = ['status' => 'int', 'statusText' => 'string', 'description' => 'string'];

        $docs[] = ['uri'       => '/authenticate', 'method' => 'POST', 'roles' => [],
                   'request'   => ['body' => $authSchema],
                   'responses' => [
                       ['status' => 200,
                        'body'   => $tokenSchema],
                       ['status' => 401,
                        'body'   => $errorSchema]
                   ]];
        $docs[] = ['uri'       => '/renew', 'method' => 'GET', 'roles' => ['ROLE_USER'],
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

            $body = json_decode(base64_decode($request->getBody()));

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

    private static function renew(Application $app) {
        return function () use ($app) {
            $request = $app->request;
            $response = $app->response;

            $xAuthHeader = $request->headers(UserXAuthTokenResource::$XAUTH_TOKEN_HEADER);
            if (empty($xAuthHeader)) {
                throw new Exception("Authentication Missing", 401);
            }
            
            $userDetailsService = UserDetailsService::autowire();
            $tokenProvider = new TokenProvider();

            $login = $tokenProvider->getLoginFromToken($xAuthHeader);
            $user = $userDetailsService->loadUserByLogin($login);

            if (!$tokenProvider->validateToken($xAuthHeader, $user['login'], $user['password'])) {
                throw new Exception("Authentication Failed", 401);
            }

            $token = $tokenProvider->createToken($user['login'], $user['password']);

            $response->setStatus(200);
            $response->body(json_encode($token, JSON_PRETTY_PRINT));
        };
    }
}
