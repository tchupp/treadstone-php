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
                $response->status(200);
                $response->headers->set('Content-Type', 'application/json');

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
                $response->status(200);
                $response->headers->set('Content-Type', 'application/json');

                $response->body(json_encode($users[0], JSON_PRETTY_PRINT));
            } else {
                $response->status(404);
            }
        };
    }
}
