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

    private static function getAll(Application $app) {
        return function () use ($app) {
            $response = $app->response;

            $userRepository = UserRepository::autowire();
            $users = $userRepository->findAll();
            if (!empty($users)) {
                foreach ($users as &$user) {
                    unset($user['password']);
                }

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
                unset($user['password']);

                $response->status(200);
                $response->body(json_encode($user, JSON_PRETTY_PRINT));
            } else {
                throw new Exception('User not found', 404);
            }
        };
    }
}
