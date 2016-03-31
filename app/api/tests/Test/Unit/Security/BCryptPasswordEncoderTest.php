<?php

namespace Test\Unit\Security;

use Api\Security\BCryptPasswordEncoder;
use PHPUnit_Framework_TestCase;

class BCryptPasswordEncoderTest extends PHPUnit_Framework_TestCase {

    public function testEncodeGeneratesUniqueHashForSamePass() {
        $passwordHashes = [];
        $password = 'thisIsMySuperSecretPassword';
        $passwordEncoder = new BCryptPasswordEncoder();

        for ($i = 0; $i < 5; $i++) {
            $passwordHash = $passwordEncoder->encode($password);
            $this->assertFalse(in_array($passwordHash, $passwordHashes));
            $passwordHashes[] = $passwordHash;
        }
    }

    public function testVerifyReturnsTrueWhenPasswordIsCorrect() {
        $password = 'firstSuperSecretPassword';

        $passwordEncoder = new BCryptPasswordEncoder();

        $passwordHash = $passwordEncoder->encode($password);
        $this->assertTrue($passwordEncoder->verify($passwordHash, $password));

        $password = 'secondAwesome&SecretPassword';

        $passwordHash = $passwordEncoder->encode($password);
        $this->assertTrue($passwordEncoder->verify($passwordHash, $password));
    }

    public function testVerifyReturnsFalseWhenPasswordIsWrong() {
        $password = 'firstSuperSecretPassword';

        $passwordEncoder = new BCryptPasswordEncoder();

        $passwordHash = $passwordEncoder->encode($password);

        $password = 'secondAwesome&SecretPassword';
        $this->assertFalse($passwordEncoder->verify($passwordHash, $password));
    }
}
