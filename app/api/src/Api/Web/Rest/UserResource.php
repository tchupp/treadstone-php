<?php

namespace Api\Web\Rest;

use Api\Database\DatabaseConnection;
use Api\Database\UserRepository;
use Slim\Slim;

class UserResource {

    /**
     * @param $app Slim context to register too
     */
    public static function registerApi($app) {
        $app->get('/users', self::getAll($app));

        $app->get('/users/:login', self::getUser($app));
    }

    /**
     * @param $app Slim
     * @return \Closure
     */
    private static function getAll($app) {
        return function () use ($app) {
            $databaseConnection = new DatabaseConnection();
            $userRepository = new UserRepository($databaseConnection);

            $users = $userRepository->findAll();

            $response = $app->response;
            if (isset($users)) {
                foreach ($users as &$user) {
                    unset($user['password_hash']);
                }

                $response->status(200);
                $response->body(json_encode($users, JSON_PRETTY_PRINT));
            } else {
                $response->status(404);
            }
        };
    }

    /**
     * @param $app Slim
     * @return \Closure
     */
    private static function getUser($app) {
        return function ($login) use ($app) {
            $databaseConnection = new DatabaseConnection();
            $userRepository = new UserRepository($databaseConnection);

            $users = $userRepository->findOneByLogin($login);

            $response = $app->response;
            if (!empty($users)) {
                $user = $users[0];
                unset($user['password_hash']);

                $response->status(200);
                $response->body(json_encode($user, JSON_PRETTY_PRINT));
            } else {
                $response->status(404);
            }
        };
    }
}
