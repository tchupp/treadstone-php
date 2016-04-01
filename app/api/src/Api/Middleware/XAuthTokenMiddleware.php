<?php

namespace Api\Middleware;

use Api\Security\TokenProvider;
use Api\Service\UserDetailsService;
use Api\Web\Rest\UserXAuthTokenResource;
use Exception;
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
        $request = $this->app->request;

        if ($this->needsAuthentication($request)) {
            $xAuthHeader = $request->headers(UserXAuthTokenResource::$XAUTH_TOKEN_HEADER);
            if (empty($xAuthHeader)) {
                throw new Exception('Authentication Missing', 401);
            }

            $login = $this->tokenProvider->getLoginFromToken($xAuthHeader);
            $request->headers->set('User', $login);

            $user = $this->userDetailsService->loadUserByLogin($login);
            if (!$this->tokenProvider->validateToken($xAuthHeader, $user['login'], $user['password'])) {
                throw new Exception('Authentication Failed', 401);
            }
        }
        $this->next->call();
    }

    private function needsAuthentication(Request $request) {
        foreach ($this->protectedResource as $resource) {
            if (strpos($request->getResourceUri(), $resource) === 0) {
                return true;
            }
        }
        return false;
    }
}
