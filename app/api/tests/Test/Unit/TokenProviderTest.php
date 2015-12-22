<?php

namespace Test\Unit;

use Api\Security\TokenProvider;
use PHPUnit_Framework_TestCase;

class TokenProviderTest extends PHPUnit_Framework_TestCase {


    public function testCreateTokenReturnsArrayWithProperIndices() {
        $username = "chuppthe";
        $password = "\$hashedpassword!";

        $tokenProvider = new TokenProvider();

        $token = $tokenProvider->createToken($username, $password);

        $this->assertEquals(2, sizeof($token));
        $this->assertArrayHasKey('expires', $token);
        $this->assertArrayHasKey('authToken', $token);
    }

    public function testGetLoginFromTokenWithNullToken() {
        $authToken = null;

        $tokenProvider = new TokenProvider();

        $this->assertNull($tokenProvider->getLoginFromToken($authToken));
    }

    public function testGetLoginFromTokenWithRandomStrings() {
        $authToken1 = "thisisarandomstringwithnocolons";
        $authToken2 = "adifferentstringbutstillnocolons";

        $tokenProvider = new TokenProvider();

        $this->assertSame($authToken1, $tokenProvider->getLoginFromToken($authToken1));
        $this->assertSame($authToken2, $tokenProvider->getLoginFromToken($authToken2));
    }

    public function testGetLoginFromTokenReturnsCorrectLogin() {
        $login1 = "chuppthe";
        $login2 = "subadoo";
        $authToken1 = "$login1:123456";
        $authToken2 = "$login2:654321";

        $tokenProvider = new TokenProvider();

        $this->assertEquals($login1, $tokenProvider->getLoginFromToken($authToken1));
        $this->assertEquals($login2, $tokenProvider->getLoginFromToken($authToken2));
    }

    public function testValidateTokenReturnsTrueIfTokenIsValid() {
        $login = "chuppers";
        $password = "hahaitsame,passwordo";

        $tokenProvider = new TokenProvider();

        $token = $tokenProvider->createToken($login, $password);

        $authToken = $token['authToken'];
        $tokenValid = $tokenProvider->validateToken($authToken, $login, $password);
        $this->assertTrue($tokenValid);
    }
}
