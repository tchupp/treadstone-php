<?php

namespace Api\Middleware;

use Slim\Middleware;

class JsonMiddleware extends Middleware {

    private $root;

    public function __construct($root) {
        $this->root = $root;
    }

    public function call() {
        if (strpos($this->app->request->getResourceUri(), $this->root) === 0) {
            $this->app->response->headers->set('Content-Type', 'application/json');

            $method = strtolower($this->app->request->getMethod());
            $mediaType = $this->app->request->getMediaType();

            if (in_array($method, array('post', 'put', 'patch')) && '' !== $this->app->request()->getBody()) {
                if (empty($mediaType) || $mediaType !== 'application/json') {
                    $this->app->halt(415);
                }
            }
        }
        $this->next->call();
    }
}
