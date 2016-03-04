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

            if (in_array($method, ['post', 'put', 'patch']) && '' !== $this->app->request()->getBody()) {
                if (empty($mediaType) || $mediaType !== 'application/json') {
                    $res->status(415);
                    $res->body(json_encode([
                        'status'      => 415,
                        'statusText'  => 'Unsupported Media Type',
                        'description' => "application/json required for $method",
                        'path'        => $req->getResourceUri()
                    ]));
                    return;
                }
            }
        }
        $this->next->call();
    }
}
