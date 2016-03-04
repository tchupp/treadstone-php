<?php

namespace Api\Middleware;

use Api\Service\UserDetailsService;
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
        $req = $this->app->request;
        $res = $this->app->response;

        if ($role = $this->needsAuthority($req)) {
            $login = $req->headers->get('User');
            if (empty($login)) {
                $res->status(401);
                $res->body(json_encode([
                    'status'      => 401,
                    'statusText'  => 'Unauthorized',
                    'description' => 'Authentication Missing',
                    'path'        => $req->getResourceUri()
                ]));
                return;
            }

            $user = $this->userDetailService->loadUserByLogin($login);
            if (!in_array($role, $user['roles'])) {
                $res->status(403);
                $res->body(json_encode([
                    'status'      => 403,
                    'statusText'  => 'Forbidden',
                    'description' => 'Authority Missing',
                    'path'        => $req->getResourceUri()
                ]));
                return;
            }
        }
        $this->next->call();
    }

    private function needsAuthority(Request $req) {
        $method = strtolower($req->getMethod());
        foreach (array_keys($this->protectedResource) as $resourceUri) {
            if (strpos($req->getResourceUri(), $resourceUri) === 0
                && $method === $this->protectedResource[$resourceUri]['method']) {
                return $this->protectedResource[$resourceUri]['role'];
            }
        }
        return false;
    }
}
