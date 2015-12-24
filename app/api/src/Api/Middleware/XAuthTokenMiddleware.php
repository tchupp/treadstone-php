<?php

namespace Api\Middleware;

use Api\Security\TokenProvider;
use Api\Service\UserDetailsService;
use Exception;
use Slim\Middleware;

class XAuthTokenMiddleware extends Middleware {

    private static $XAUTH_TOKEN_HEADER = "x-auth-token";

    private $userDetailService;
    private $tokenProvider;
    private $pureResources;

    public function __construct(UserDetailsService $userDetailService, TokenProvider $tokenProvider, $pureResources = array()) {
        $this->userDetailService = $userDetailService;
        $this->tokenProvider = $tokenProvider;
        $this->pureResources = $pureResources;
    }

    public function call() {
        $req = $this->app->request;
        $res = $this->app->response;

        if (!in_array($req->getResourceUri(), $this->pureResources)) {
            $xAuthHeader = $req->headers(self::$XAUTH_TOKEN_HEADER);
            if (empty($xAuthHeader)) {
                $res->status(401);
                return;
            }

            try {
                $login = $this->tokenProvider->getLoginFromToken($xAuthHeader);
                $user = $this->userDetailService->loadUserByLogin($login);
                if (!$this->tokenProvider->validateToken($xAuthHeader, $user['login'], $user['password'])) {
                    throw new Exception("Authentication Failed", 401);
                }
            } catch (Exception $ex) {
                $res->status(401);
                $res->body("Authentication Failed");
                return;
            }
        }
        $this->next->call();
    }
}
