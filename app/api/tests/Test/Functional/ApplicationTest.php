<?php

namespace Test\Functional;

use Api\Application;
use Exception;
use Slim\Environment;

class ApplicationTest extends \PHPUnit_Framework_TestCase {
    public function setUp() {
        $_SESSION = [];
    }

    public function testMissingConfigurationDirectoryGeneratesException() {
        try {
            new Application([], 'missingConfigDirectory');

            $this->fail("Expected Exception");
        } catch (Exception $ex) {
            $this->assertEquals("Config directory is missing: ", substr($ex->getMessage(), 0, 29));
            $this->assertEquals(500, $ex->getCode());
        }
    }

    public function testHttpExceptionGenerates500() {
        $app = new Application();
        $app->get('/api/test/http-exception', function () {
            throw new Exception('HTTP exception', 406);
        });

        Environment::mock([
            'PATH_INFO' => '/api/test/http-exception',
        ]);
        $response = $app->invoke();
        $this->assertEquals(json_encode([
            'status'      => 406,
            'statusText'  => 'Not Acceptable',
            'description' => 'HTTP exception',
            'path'        => '/api/test/http-exception'
        ]), $response->getBody());
        $this->assertEquals(406, $response->getStatus());
    }

    public function testUndefinedExceptionGenerates500() {
        $app = new Application();
        $app->get('/api/test/undefined-exception', function () {
            throw new Exception('Undefined exception');
        });

        Environment::mock([
            'PATH_INFO' => '/api/test/undefined-exception',
        ]);
        $response = $app->invoke();
        $this->assertEquals(json_encode([
            'status'      => 500,
            'statusText'  => 'Internal Server Error',
            'description' => 'Undefined exception',
            'path'        => '/api/test/undefined-exception'
        ]), $response->getBody());
        $this->assertEquals(500, $response->getStatus());
    }

    public function testUnkownHttpStatusExceptionGenerates500() {
        $app = new Application();
        $app->get('/api/test/undefined-exception', function () {
            throw new Exception('Exception with unknown HTTP status', 999);
        });

        Environment::mock([
            'PATH_INFO' => '/api/test/undefined-exception',
        ]);
        $response = $app->invoke();
        $this->assertEquals(json_encode([
            'status'      => 500,
            'statusText'  => 'Internal Server Error',
            'description' => 'Exception with unknown HTTP status',
            'path'        => '/api/test/undefined-exception'
        ]), $response->getBody());
        $this->assertEquals(500, $response->getStatus());
    }
}
