<?php

namespace Api\Middleware;

use Api\Security\TokenProvider;
use Api\Service\UserDetailsService;
use Slim\Http\Request;
use Slim\Middleware;

class XAuthTokenMiddleware extends Middleware {

    private static $XAUTH_TOKEN_HEADER = "x-auth-token";

    private $userDetailService;
    private $tokenProvider;
    private $protectedRoots;

    public function __construct(UserDetailsService $userDetailService, TokenProvider $tokenProvider, $protectedRoots = []) {
        $this->userDetailService = $userDetailService;
        $this->tokenProvider = $tokenProvider;
        $this->protectedRoots = $protectedRoots;
    }

    public function call() {
        $req = $this->app->request;
        $res = $this->app->response;

        if ($this->needsAuthentication($req)) {
            $xAuthHeader = $req->headers(self::$XAUTH_TOKEN_HEADER);
            if (empty($xAuthHeader)) {
                $res->status(401);
                $res->body(json_encode([
                    'status'      => 401,
                    'statusText'  => 'Unauthorized',
                    'description' => 'Authentication Missing',
                    'path'        => $req->getResourceUri()
                ]));
                return;
            }

            $login = $this->tokenProvider->getLoginFromToken($xAuthHeader);
            $req->headers->set('User', $login);

            $user = $this->userDetailService->loadUserByLogin($login);
            if (!$this->tokenProvider->validateToken($xAuthHeader, $user['login'], $user['password'])) {
                $res->status(401);
                $res->body(json_encode([
                    'status'      => 401,
                    'statusText'  => 'Unauthorized',
                    'description' => 'Authentication Failed',
                    'path'        => $req->getResourceUri()
                ]));
                return;
            }
        }
        $this->next->call();
    }

    private function needsAuthentication(Request $req) {
        foreach ($this->protectedRoots as $root) {
            if (strpos($req->getResourceUri(), $root) === 0) {
                return true;
            }
        }
        return false;
    }
}
