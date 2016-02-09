<?php

namespace Api\Web\Rest;

use Api\Application;

class DocumentationResource {

    public static function registerApi(Application $app) {
        $app->get('/docs', self::getDocs($app));
    }

    private static function getDocs(Application $app) {
        return function () use ($app) {
            $response = $app->response;

            $docs['AccountResource'] = AccountResource::documentation();
            $docs['CourseResource'] = CourseResource::documentation();
            $docs['FeaturesResource'] = FeaturesResource::documentation();
            $docs['UserResource'] = UserResource::documentation();
            $docs['UserXAuthTokenController'] = UserXAuthTokenController::documentation();

            $response->setStatus(200);
            $response->body(json_encode($docs, JSON_PRETTY_PRINT));
        };
    }
}
