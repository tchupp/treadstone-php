<?php

namespace Api;

use Api\Web\Rest\AccountResource;
use Api\Web\Rest\DocumentationResource;
use Api\Web\Rest\FeaturesResource;
use Api\Web\Rest\UserResource;
use Api\Web\Rest\UserXAuthTokenController;
use Exception;
use Slim\Http\Response;
use Slim\Middleware;
use Slim\Slim;

class Application extends Slim {
    public $configDirectory;
    public $config;

    protected function initConfig() {
        $config = array();
        if (!file_exists($this->configDirectory) || !is_dir($this->configDirectory)) {
            throw new Exception('Config directory is missing: ' . $this->configDirectory, 500);
        }
        foreach (preg_grep('/\\.php$/', scandir($this->configDirectory)) as $filename) {
            $config = array_replace_recursive($config, include $this->configDirectory . '/' . $filename);
        }
        return $config;
    }

    public function __construct(array $userSettings = array(), $configDirectory = 'config') {
        parent::__construct($userSettings);
        $this->config('debug', false);
        $this->notFound(function () {
            $this->handleNotFound();
        });
        $this->error(function ($e) {
            $this->handleException($e);
        });

        $this->configDirectory = __DIR__ . '/../../' . $configDirectory;
        $this->config = $this->initConfig();

        FeaturesResource::registerApi($this);
        UserResource::registerApi($this);
        AccountResource::registerApi($this);
        UserXAuthTokenController::registerApi($this);

        DocumentationResource::registerApi($this);
    }

    public function handleNotFound() {
        throw new Exception(
            'Resource ' . $this->request->getResourceUri() .
            ' using ' . $this->request->getMethod() . ' method does not exist.',
            404
        );
    }

    public function handleException(Exception $e) {
        $status = $e->getCode();
        $statusText = Response::getMessageForCode($status);
        if ($statusText === null) {
            $status = 500;
            $statusText = 'Internal Server Error';
        }

        $this->response->setStatus($status);
        $this->response->setBody(json_encode(array(
            'status' => $status,
            'statusText' => preg_replace('/^[0-9]+ (.*)$/', '$1', $statusText),
            'description' => $e->getMessage()
        )));
    }

    public function invoke() {
        foreach ($this->middleware as $middleware) {
            /** @var Middleware $middleware */
            $middleware->call();
        }
        $this->response()->finalize();
        return $this->response();
    }
}
