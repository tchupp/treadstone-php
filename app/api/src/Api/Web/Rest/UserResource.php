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
        $app->get('/users', function () use ($app) {
            $databaseConnection = new DatabaseConnection();
            $userRepository = new UserRepository($databaseConnection);

            $users = $userRepository->findAll();

            $response = $app->response;
            if (isset($users)) {
                $response->status(200);
                $response->body(json_encode($users, JSON_PRETTY_PRINT));
            } else {
                $response->status(404);
            }
        });

        $app->get('/users/:login', function ($login) use ($app) {
            $databaseConnection = new DatabaseConnection();
            $userRepository = new UserRepository($databaseConnection);

            $users = $userRepository->findOneByLogin($login);

            $response = $app->response;
            if (!empty($users)) {
                $response->status(200);
                $response->body(json_encode($users[0], JSON_PRETTY_PRINT));
            } else {
                $response->status(404);
            }
        });
    }
}
