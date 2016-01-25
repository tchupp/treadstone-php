<?php

namespace Test\Functional;

use Api\Application;
use PHPUnit_Framework_TestCase;
use Slim\Environment;

class FeaturesTest extends PHPUnit_Framework_TestCase {
    public function setUp() {
        $_SESSION = array();
    }

    public function testIndex() {
        $app = new Application();

        Environment::mock(array(
            'PATH_INFO' => '/features',
        ));

        $expected = array();
        foreach ($app->config['features'] as $id => $feature) {
            $expected[] = array(
                'id' => $id,
                'name' => $feature['name'],
                'href' => './api/features/' . $id,
            );
        }

        $response = $app->invoke();
        $this->assertEquals(json_encode($expected, JSON_PRETTY_PRINT), $response->getBody());
        $this->assertEquals(200, $response->getStatus());
    }

    public function testGet() {
        $app = new Application();

        $this->assertNotEquals(0, count($app->config['features']));
        foreach ($app->config['features'] as $id => $feature) {
            $app = new Application();
            Environment::mock(array(
                'PATH_INFO' => '/features/' . $id,
            ));
            $response = $app->invoke();
            $this->assertEquals(
                json_encode(
                    array_merge(array('id' => $id), $feature, array('href' => './api/features/' . $id)),
                    JSON_PRETTY_PRINT),
                $response->getBody()
            );
            $this->assertEquals(200, $response->getStatus());
        }
    }

    public function testUnknownFeatureGets404() {
        $app = new Application();

        Environment::mock(array(
            'PATH_INFO' => '/features/unknown',
        ));
        $response = $app->invoke();
        $this->assertEquals(json_encode(array(
            "status" => 404,
            "statusText" => "Not Found",
            "description" => "Resource /features/unknown using GET method does not exist.",
        )), $response->getBody());
        $this->assertEquals(404, $response->getStatus());
    }
}
