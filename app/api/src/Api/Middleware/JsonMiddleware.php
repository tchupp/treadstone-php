<?php

namespace Api\Middleware;

use Exception;
use Slim\Middleware;

class JsonMiddleware extends Middleware {

    private $root;

    public function __construct($root) {
        $this->root = $root;
    }

    public function call() {
        $request = $this->app->request;
        $response = $this->app->response;

        if (strpos($request->getResourceUri(), $this->root) === 0) {
            $response->headers->set('Content-Type', 'application/json');

            $method = strtolower($request->getMethod());
            $mediaType = $request->getMediaType();

            if (in_array($method, ['post', 'put', 'patch']) && '' !== $request->getBody()) {
                if (empty($mediaType) || $mediaType !== 'application/json') {
                    throw new Exception("application/json required for $method", 415);
                }
            }
        }
        $this->next->call();
    }
}
