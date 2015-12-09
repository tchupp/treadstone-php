<?php

namespace Api\Web\Rest;

use Slim\Slim;

class UserResource {

    /**
     * @param $app Slim context to register too
     */
    public static function registerApi($app) {
        $app->get('/users', function () use ($app) {

        });

        $app->get('/users/:id', function ($id) use ($app) {
            echo $id;
        });
    }
}
