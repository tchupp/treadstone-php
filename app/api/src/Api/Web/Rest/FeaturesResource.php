<?php

namespace Api\Web\Rest;

use Api\Application;
use Api\Model\Features;

class FeaturesResource {

    public static function registerApi(Application $app) {
        $app->get('/features', self::getAll($app, $app->config['features']));

        $app->get('/features/:id', self::getOne($app, $app->config['features']));
    }

    private static function getAll(Application $app, $config) {
        return function () use ($app, $config) {
            $features = new Features($config);

            $app->response->setBody(json_encode($features->getFeatures(), JSON_PRETTY_PRINT));
        };
    }

    private static function getOne(Application $app, $config) {
        return function ($id) use ($app, $config) {
            $features = new Features($config);

            $feature = $features->getFeature($id);

            if ($feature === null) {
                $app->notFound();
                return;
            }
            $app->response->setBody(json_encode($feature, JSON_PRETTY_PRINT));
        };
    }
}
