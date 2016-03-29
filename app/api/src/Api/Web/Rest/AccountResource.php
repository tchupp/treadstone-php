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

//        $app->post('/account/reset_password/init', self::requestPasswordReset($app));

//        $app->post('/account/reset_password/finish', self::finishPasswordReset($app));
    }

    public static function documentation() {
        $registerSchema = ['login'     => 'string', 'password' => 'string',
                           'firstName' => 'string', 'lastName' => 'string', 'email' => 'string'];
        $accountSchema = ['login'     => 'string', 'password' => null,
                          'firstName' => 'string', 'lastName' => 'string', 'email' => 'string',
                          'activated' => 'int', 'role' => ['string']];
        $updateAccountSchema = ['login'     => 'string',
                                'firstName' => 'string', 'lastName' => 'string', 'email' => 'string'];
        $errorSchema = ['status' => 'int', 'statusText' => 'string', 'description' => 'string', 'path' => 'string'];
        $passwordSchema = ['password' => 'string'];
//        $resetRequestSchema = array('email' => 'string');
//        $resetFinalizeSchema = array('resetKey' => 'string', 'password' => 'string');

        $docs[] = ['uri'       => '/register', 'method' => 'POST', 'roles' => [],
                   'request'   => ['body' => $registerSchema],
                   'responses' => [
                       ['status' => 201],
                       ['status' => 400,
                        'body'   => $errorSchema],
                       ['status' => 500,
                        'body'   => $errorSchema]
                   ]];
        $docs[] = ['uri'       => '/activate', 'method' => 'GET', 'roles' => [],
                   'request'   => ['key' => 'string'],
                   'responses' => [
                       ['status' => 200],
                       ['status' => 400,
                        'body'   => $errorSchema],
                       ['status' => 500,
                        'body'   => $errorSchema]
                   ]];
        $docs[] = ['uri'       => '/account', 'method' => 'GET', 'roles' => ['ROLE_USER'],
                   'responses' => [
                       ['status' => 200,
                        'body'   => $accountSchema],
                       ['status' => 401,
                        'body'   => $errorSchema],
                       ['status' => 500,
                        'body'   => $errorSchema]
                   ]];
        $docs[] = ['uri'       => '/account', 'method' => 'POST', 'roles' => ['ROLE_USER'],
                   'request'   => ['body' => $updateAccountSchema],
                   'responses' => [
                       ['status' => 200],
                       ['status' => 400,
                        'body'   => $errorSchema],
                       ['status' => 401,
                        'body'   => $errorSchema],
                   ]];
        $docs[] = ['uri'       => '/account/change_password', 'method' => 'POST', 'roles' => ['ROLE_USER'],
                   'request'   => ['body' => $passwordSchema],
                   'responses' => [
                       ['status' => 200,
                        'body'   => $accountSchema],
                       ['status' => 401,
                        'body'   => $errorSchema],
                       ['status' => 500,
                        'body'   => $errorSchema]
                   ]];
        /*$docs[] = array('uri' => '/account/reset_password/init', 'method' => 'POST', 'roles' => ['ROLE_USER'],
            'request' => array('body' => $resetRequestSchema),
            'responses' => array(
                array('status' => 200),
                array('status' => 400,
                    'body' => $errorSchema),
                array('status' => 401,
                    'body' => $errorSchema),
                array('status' => 404,
                    'body' => $errorSchema),
                array('status' => 500,
                    'body' => $errorSchema)
            ));
        $docs[] = array('uri' => '/account/reset_password/init', 'method' => 'POST', 'roles' => ['ROLE_USER'],
            'request' => array('body' => $resetFinalizeSchema),
            'responses' => array(
                array('status' => 200),
                array('status' => 400,
                    'body' => $errorSchema),
                array('status' => 401,
                    'body' => $errorSchema)
            ));*/
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
                $response->setStatus(201);
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
            $userService->activateRegistration($key);

            $response->setStatus(200);
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
                $response->setStatus(200);
                $response->body(json_encode($user, JSON_PRETTY_PRINT));
            } else {
                throw new Exception('User not found', 500);
            }
        };
    }

    private static function updateAccount(Application $app) {
        return function () use ($app) {
            $request = $app->request;
            $response = $app->response;

            $body = json_decode($request->getBody());

            $login = $body->login;
            $email = $body->email;
            $firstName = $body->firstName;
            $lastName = $body->lastName;

            if (empty($email) || empty($firstName) || empty($lastName) || empty($login)) {
                throw new Exception("Malformed body", 400);
            }

            $userService = UserService::autowire();
            $userService->updateUserInformation($login, $email, $firstName, $lastName);
            $response->setStatus(200);
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

            $response->setStatus(200);
        };
    }

    private static function requestPasswordReset(Application $app) {
        return function () use ($app) {
            $request = $app->request;
            $response = $app->response;

            $body = json_decode($request->getBody());

            $email = $body->email;

            if (empty($email)) {
                throw new Exception('Malformed body', 400);
            }

            $userService = UserService::autowire();
            $mailService = new MailService();

            $user = $userService->requestPasswordReset($email);
            $success = $mailService->sendPasswordResetEmail($user, $request->getHostWithPort());

            if ($success) {
                $response->setStatus(200);
            } else {
                throw new Exception("Failed to send password reset email", 500);
            }
        };
    }

    private static function finishPasswordReset(Application $app) {
        return function () use ($app) {
            $request = $app->request;
            $response = $app->response;

            $body = json_decode($request->getBody());

            $password = $body->password;
            $resetKey = $body->resetKey;

            if (empty($password) || empty($resetKey)) {
                throw new Exception('Malformed body', 400);
            }

            $userService = UserService::autowire();
            $userService->completePasswordReset($password, $resetKey);

            $response->setStatus(200);
        };
    }
}
