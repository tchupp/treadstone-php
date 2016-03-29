<?php

namespace Api\Middleware;

use Api\Security\TokenProvider;
use Api\Service\UserDetailsService;
use Api\Web\Rest\UserXAuthTokenResource;
use Slim\Http\Request;
use Slim\Middleware;

class XAuthTokenMiddleware extends Middleware {

    private $userDetailsService;
    private $tokenProvider;
    private $protectedResource;

    public function __construct(UserDetailsService $userDetailService, TokenProvider $tokenProvider, $protectedResources = []) {
        $this->userDetailsService = $userDetailService;
        $this->tokenProvider = $tokenProvider;
        $this->protectedResource = $protectedResources;
    }

    public function call() {
        $req = $this->app->request;
        $res = $this->app->response;

        if ($this->needsAuthentication($req)) {
            $xAuthHeader = $req->headers(UserXAuthTokenResource::$XAUTH_TOKEN_HEADER);
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

            $user = $this->userDetailsService->loadUserByLogin($login);
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
        foreach ($this->protectedResource as $resource) {
            if (strpos($req->getResourceUri(), $resource) === 0) {
                return true;
            }
        }
        return false;
    }
}
