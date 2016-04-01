<?php

namespace Api\Middleware;

use Api\Service\UserDetailsService;
use Exception;
use Slim\Http\Request;
use Slim\Middleware;

class UserAuthorityMiddleware extends Middleware {

    private $userDetailService;
    private $protectedResource;

    public function __construct(UserDetailsService $userDetailService, array $protectedResources = []) {
        $this->userDetailService = $userDetailService;
        $this->protectedResource = $protectedResources;
    }

    public function call() {
        $request = $this->app->request;

        if ($role = $this->needsAuthority($request)) {
            $login = $request->headers->get('User');
            if (empty($login)) {
                throw new Exception('Authentication Missing', 401);
            }

            $user = $this->userDetailService->loadUserByLogin($login);
            if (!in_array($role, $user['roles'])) {
                throw new Exception('Authority Missing', 403);
            }
        }
        $this->next->call();
    }

    private function needsAuthority(Request $request) {
        $method = strtolower($request->getMethod());
        foreach (array_keys($this->protectedResource) as $resourceUri) {
            if (strpos($request->getResourceUri(), $resourceUri) === 0
                && $method === $this->protectedResource[$resourceUri]['method']) {
                return $this->protectedResource[$resourceUri]['role'];
            }
        }
        return false;
    }
}
