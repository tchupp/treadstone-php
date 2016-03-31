<?php

namespace Test\Unit\Service\Util;

use Api\Service\Util\RandomUtil;
use PHPUnit_Framework_TestCase;

class RandomUtilTest extends PHPUnit_Framework_TestCase {

    public function testGenerateActivationKeyCreates100UniqueKeys() {
        $keys = [];
        $randomUtil = new RandomUtil();

        for ($i = 0; $i < 100; $i++) {
            $activationKey = $randomUtil->generateActivationKey();
            $this->assertFalse(in_array($activationKey, $keys));
            $keys[] = $activationKey;
        }
    }

    public function testGenerateActivationKeyCreates10KeysWithCorrectLength() {
        $randomUtil = new RandomUtil();

        for ($i = 0; $i < 10; $i++) {
            $this->assertEquals(20, strlen($randomUtil->generateActivationKey()));
        }
    }

    public function testGenerateResetKeyCreates100UniqueKeys() {
        $keys = [];
        $randomUtil = new RandomUtil();

        for ($i = 0; $i < 100; $i++) {
            $resetKey = $randomUtil->generateResetKey();
            $this->assertFalse(in_array($resetKey, $keys));
            $keys[] = $resetKey;
        }
    }

    public function testGenerateResetKeyCreates10KeysWithCorrectLength() {
        $randomUtil = new RandomUtil();

        for ($i = 0; $i < 10; $i++) {
            $this->assertEquals(20, strlen($randomUtil->generateResetKey()));
        }
    }
}
