<?php

namespace Api\Web\Rest;

use Api\Application;
use Api\Database\UserRepository;
use Exception;

class UserResource {

    public static function registerApi(Application $app) {
        $app->get('/users', self::getAllUsers($app));

        $app->get('/users/:login', self::getOneUsers($app));
    }

    public static function documentation() {
        $userSchema = ['login'     => 'string', 'firstName' => 'string', 'lastName' => 'string', 'email' => 'string',
                       'activated' => 'int', 'activationKey' => 'string', 'role' => ['string']];
        $errorSchema = ['status' => 'int', 'statusText' => 'string', 'description' => 'string', 'path' => 'string'];

        $docs[] = ['uri'       => '/users', 'method' => 'GET',
                   'responses' => [
                       ['status' => 200,
                        'body'   => ['string' => $userSchema]],
                       ['status' => 401,
                        'body'   => $errorSchema],
                       ['status' => 404,
                        'body'   => $errorSchema]
                   ]];
        $docs[] = ['uri'       => '/users/:login', 'method' => 'GET',
                   'responses' => [
                       ['status' => 200,
                        'body'   => $userSchema],
                       ['status' => 401,
                        'body'   => $errorSchema],
                       ['status' => 404,
                        'body'   => $errorSchema]
                   ]];
        return $docs;
    }

    private static function getAllUsers(Application $app) {
        return function () use ($app) {
            $response = $app->response;

            $userRepository = UserRepository::autowire();
            $users = $userRepository->findAll();
            if (!empty($users)) {
                $response->setStatus(200);
                $response->body(json_encode($users, JSON_PRETTY_PRINT));
            } else {
                throw new Exception('Users not found', 404);
            }
        };
    }

    private static function getOneUsers(Application $app) {
        return function ($login) use ($app) {
            $response = $app->response;

            $userRepository = UserRepository::autowire();
            $user = $userRepository->findOneByLogin($login);
            if (!empty($user)) {
                $response->setStatus(200);
                $response->body(json_encode($user, JSON_PRETTY_PRINT));
            } else {
                throw new Exception('User not found', 404);
            }
        };
    }
}
