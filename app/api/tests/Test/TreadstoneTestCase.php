<?php

namespace Test;

use PHPUnit_Framework_TestCase;
use ReflectionClass;

class TreadstoneTestCase extends PHPUnit_Framework_TestCase {

    protected function getPrivateProperty($object, $propertyName) {
        $reflectionClass = new ReflectionClass($object);
        $reflectionProperty = $reflectionClass->getProperty($propertyName);
        $reflectionProperty->setAccessible(true);
        $databaseConnection = $reflectionProperty->getValue($object);
        return $databaseConnection;
    }
}
