<?php

namespace Api\Web\Rest;

use Api\Application;
use Api\Database\UserRepository;
use Exception;

class UserResource {

    public static function registerApi(Application $app) {
        $app->get('/users', self::getAll($app));

        $app->get('/users/:login', self::getOne($app));
    }

    public static function documentation() {
        $userSchema = array('login' => 'string', 'firstName' => 'string', 'lastName' => 'string', 'email' => 'string',
            'activated' => 'int', 'activationKey' => 'string', 'role' => array('string'));
        $errorSchema = array('status' => 'int', 'statusText' => 'string', 'description' => 'string');

        $docs[] = array('uri' => '/users', 'method' => 'GET',
            'responses' => array(
                array('status' => 200,
                    'body' => array('string' => $userSchema)),
                array('status' => 401,
                    'body' => $errorSchema),
                array('status' => 404,
                    'body' => $errorSchema)
            ));
        $docs[] = array('uri' => '/users/:id', 'method' => 'GET',
            'responses' => array(
                array('status' => 200,
                    'body' => $userSchema),
                array('status' => 401,
                    'body' => $errorSchema),
                array('status' => 404,
                    'body' => $errorSchema)
            ));
        return $docs;
    }

    private static function getAll(Application $app) {
        return function () use ($app) {
            $response = $app->response;

            $userRepository = UserRepository::autowire();
            $users = $userRepository->findAll();
            if (!empty($users)) {
                $response->status(200);
                $response->body(json_encode($users, JSON_PRETTY_PRINT));
            } else {
                throw new Exception('Users not found', 404);
            }
        };
    }

    private static function getOne(Application $app) {
        return function ($login) use ($app) {
            $response = $app->response;

            $userRepository = UserRepository::autowire();
            $user = $userRepository->findOneByLogin($login);
            if (!empty($user)) {
                $response->status(200);
                $response->body(json_encode($user, JSON_PRETTY_PRINT));
            } else {
                throw new Exception('User not found', 404);
            }
        };
    }
}
