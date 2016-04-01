<?php

namespace Api\Middleware;

use Exception;
use Slim\Http\Response;
use Slim\Middleware;

class ExceptionHandlingMiddleware extends Middleware {

    public function call() {
        try {
            $this->next->call();
        } catch (Exception $ex) {
            $request = $this->app->request;
            $response = $this->app->response;

            $status = $ex->getCode();
            $statusText = Response::getMessageForCode($status);
            if ($statusText === null) {
                $status = 500;
                $statusText = 'Internal Server Error';
            }

            $response->setStatus($status);
            $response->setBody(json_encode([
                'status'      => $status,
                'statusText'  => preg_replace('/^[0-9]+ (.*)$/', '$1', $statusText),
                'description' => $ex->getMessage(),
                'path'        => $request->getResourceUri()
            ]));
        }
    }
}
