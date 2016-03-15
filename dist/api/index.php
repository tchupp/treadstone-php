<?php
use Api\Application;
use Api\Middleware\JsonMiddleware;
use Api\Middleware\UserAuthorityMiddleware;
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
        throw new Exception('Composer dependencies not installed. Run `make install --directory app/api`');
    }
    require_once "$autoload";

    // Initialize Slim Framework
    if (!class_exists('\\Slim\\Slim')) {
        throw new Exception(
            'Missing Slim from Composer dependencies.'
            . ' Ensure slim/slim is in composer.json and run `make update --directory app/api`'
        );
    }
    $app = new Application();

    $userDetailService = UserDetailsService::autowire();
    $authorityProtectedResources = ['/features' => ['method' => 'get', 'role' => 'ROLE_DEV'],
                                    '/docs'     => ['method' => 'get', 'role' => 'ROLE_DEV']];
    $authenticationProtectedResources = ['/features', '/account', '/semesters', '/docs'];

    // (Middleware) first one added -> last one run
    $app->add(new UserAuthorityMiddleware($userDetailService, $authorityProtectedResources));
    $app->add(new XAuthTokenMiddleware($userDetailService, new TokenProvider(), $authenticationProtectedResources));
    $app->add(new JsonMiddleware('/'));

    $app->run();

} catch (Exception $e) {
    if (isset($app)) {
        $app->handleException($e);
    } else {
        http_response_code(500);
        header('Content-Type: application/json');
        echo json_encode([
            'status'      => 500,
            'statusText'  => 'Internal Server Error',
            'description' => $e->getMessage(),
        ]);
    }
}
