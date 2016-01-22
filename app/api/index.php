<?php
use Api\Application;
use Api\Database\DatabaseConnection;
use Api\Database\UserRepository;
use Api\Middleware\JsonMiddleware;
use Api\Middleware\XAuthTokenMiddleware;
use Api\Security\TokenProvider;
use Api\Service\UserDetailsService;

error_reporting(-1);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
date_default_timezone_set('UTC');

try {
    // Initialize Composer autoloader
    $autoload = __DIR__ . '/vendor/autoload.php';
    if (!file_exists($autoload)) {
        throw new \Exception('Composer dependencies not installed. Run `make install --directory app/api`');
    }
    require_once "$autoload";

    // Initialize Slim Framework
    if (!class_exists('\\Slim\\Slim')) {
        throw new \Exception(
            'Missing Slim from Composer dependencies.'
            . ' Ensure slim/slim is in composer.json and run `make update --directory app/api`'
        );
    }
    $app = new Application();

    $userDetailService = new UserDetailsService(new UserRepository(new DatabaseConnection()));
    $protectedRoots = array('/users');

    // (middleware) first one added -> last one run
    $app->add(new JsonMiddleware('/'));
    $app->add(new XAuthTokenMiddleware($userDetailService, new TokenProvider(), $protectedRoots));

    $app->run();

} catch (\Exception $e) {
    if (isset($app)) {
        $app->handleException($e);
    } else {
        http_response_code(500);
        header('Content-Type: application/json');
        echo json_encode(array(
            'status' => 500,
            'statusText' => 'Internal Server Error',
            'description' => $e->getMessage(),
        ));
    }
}
