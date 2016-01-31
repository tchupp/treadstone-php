<?php

namespace Api\Web\Rest;

use Api\Application;
use Api\Database\UserRepository;
use Api\Service\MailService;
use Api\Service\UserService;
use Exception;

class AccountResource {

    public static function registerApi(Application $app) {
        $app->post('/register', self::registerAccount($app));

        $app->get('/activate', self::activateAccount($app));

        $app->get('/account', self::getAccount($app));

        $app->post('/account', self::updateAccount($app));

        $app->post('/account/change_password', self::changePassword($app));
    }

    public static function documentation() {
        $registerSchema = array('login' => 'string', 'password' => 'string',
            'firstName' => 'string', 'lastName' => 'string', 'email' => 'string');
        $accountSchema = array('login' => 'string', 'password' => null,
            'firstName' => 'string', 'lastName' => 'string', 'email' => 'string',
            'activated' => 'int', 'role' => array('string'));
        $updateAccountSchema = array('login' => 'string',
            'firstName' => 'string', 'lastName' => 'string', 'email' => 'string');
        $errorSchema = array('status' => 'int', 'statusText' => 'string', 'description' => 'string');
        $passwordSchema = array('password' => 'string');

        $docs[] = array('uri' => '/register', 'method' => 'POST',
            'request' => array('body' => $registerSchema),
            'responses' => array(
                array('status' => 201),
                array('status' => 400,
                    'body' => $errorSchema),
                array('status' => 500,
                    'body' => $errorSchema)
            ));
        $docs[] = array('uri' => '/activate', 'method' => 'GET',
            'request' => array('key' => 'string'),
            'responses' => array(
                array('status' => 200),
                array('status' => 400,
                    'body' => $errorSchema),
                array('status' => 500,
                    'body' => $errorSchema)
            ));
        $docs[] = array('uri' => '/account', 'method' => 'GET',
            'responses' => array(
                array('status' => 200,
                    'body' => $accountSchema),
                array('status' => 401,
                    'body' => $errorSchema),
                array('status' => 500,
                    'body' => $errorSchema)
            ));
        $docs[] = array('uri' => '/account', 'method' => 'POST',
            'request' => array('body' => $updateAccountSchema),
            'responses' => array(
                array('status' => 401,
                    'body' => $errorSchema),
                array('status' => 501,
                    'body' => $errorSchema)
            ));
        $docs[] = array('uri' => '/account/change_password', 'method' => 'POST',
            'request' => array('body' => $passwordSchema),
            'responses' => array(
                array('status' => 200,
                    'body' => $accountSchema),
                array('status' => 401,
                    'body' => $errorSchema),
                array('status' => 500,
                    'body' => $errorSchema)
            ));
        return $docs;
    }

    private static function registerAccount(Application $app) {
        return function () use ($app) {
            $request = $app->request;
            $response = $app->response;

            $body = json_decode($request->getBody());

            $email = $body->email;
            $firstName = $body->firstName;
            $lastName = $body->lastName;
            $login = $body->login;
            $password = $body->password;

            if (empty($email) || empty($firstName) || empty($lastName) || empty($login) || empty($password)) {
                throw new Exception("Malformed body", 400);
            }

            $userRepository = UserRepository::autowire();

            $oneByLogin = $userRepository->findOneByLogin($login);
            if (!empty($oneByLogin)) {
                throw new Exception("Login already in use", 400);
            }

            $oneByEmail = $userRepository->findOneByEmail($email);
            if (!empty($oneByEmail)) {
                throw new Exception("E-mail address already in use", 400);
            }

            $userService = UserService::autowire();
            $mailService = new MailService();

            $user = $userService->createUserInformation($login, $password, $firstName, $lastName, $email);
            $success = $mailService->sendActivationEmail($user, $request->getHostWithPort());

            if ($success) {
                $response->status(201);
            } else {
                throw new Exception("Failed to send activation email", 500);
            }
        };
    }

    private static function activateAccount(Application $app) {
        return function () use ($app) {
            $request = $app->request;
            $response = $app->response;

            $key = $request->params('key');

            if (empty($key)) {
                throw new Exception("Missing parameter 'key'", 400);
            }

            $userService = UserService::autowire();
            $user = $userService->activateRegistration($key);

            if (!empty($user)) {
                $response->status(200);
            } else {
                throw new Exception("User could not be found by activation key", 500);
            }
        };
    }

    private static function getAccount(Application $app) {
        return function () use ($app) {
            $request = $app->request;
            $response = $app->response;

            $login = $request->headers('User');

            $userRepository = UserRepository::autowire();
            $user = $userRepository->findOneByLogin($login);
            if (!empty($user)) {
                $response->status(200);
                $response->body(json_encode($user, JSON_PRETTY_PRINT));
            } else {
                throw new Exception('User not found', 500);
            }
        };
    }

    private static function updateAccount(Application $app) {
        return function () use ($app) {
            throw new Exception("Coming soon!", 501);
        };
    }

    private static function changePassword(Application $app) {
        return function () use ($app) {
            $request = $app->request;
            $response = $app->response;

            $login = $request->headers('User');

            $body = json_decode($request->getBody());
            $password = $body->password;

            if (empty($password)) {
                throw new Exception("Malformed body", 400);
            }

            $userService = UserService::autowire();
            $userService->changePassword($login, $password);

            $response->status(200);
        };
    }
}
