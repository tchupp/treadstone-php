<?php

namespace Api\Middleware;

use Slim\Middleware;

class JsonMiddleware extends Middleware {

    private $root;

    public function __construct($root) {
        $this->root = $root;
    }

    public function call() {
        $req = $this->app->request;
        $res = $this->app->response;

        if (strpos($req->getResourceUri(), $this->root) === 0) {
            $res->headers->set('Content-Type', 'application/json');

            $method = strtolower($req->getMethod());
            $mediaType = $req->getMediaType();

            if (in_array($method, array('post', 'put', 'patch')) && '' !== $this->app->request()->getBody()) {
                if (empty($mediaType) || $mediaType !== 'application/json') {
                    $res->status(415);
                    return;
                }
            }
        }
        $this->next->call();
    }
}
