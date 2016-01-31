<?php

namespace Test\Unit;

use Api\Model\User;
use JsonSerializable;
use PHPUnit_Framework_TestCase;

class UserTest extends PHPUnit_Framework_TestCase {

    public function testImplementsInterface() {
        $user = new User('', '', '', '', '', '', '', []);
        $this->assertContains(JsonSerializable::class, class_implements($user));
    }

    public function testJsonSerialize() {
        $login = 'chuppthe';
        $password = 'thisisagoodpassword';
        $email = 'chuppthe@msu.edu';
        $firstName = 'Theo';
        $lastName = 'Chupp';
        $activated = 1;
        $activatedKey = 'activationkkey!!!';
        $role = array('ROLE_USER');

        $expectedUser = array('login' => $login, 'password' => null, 'email' => $email,
            'firstName' => $firstName, 'lastName' => $lastName,
            'activated' => true, 'role' => $role);

        $user = new User($login, $password, $email, $firstName, $lastName, $activated, $activatedKey, $role);

        $this->assertEquals(json_encode($expectedUser), json_encode($user));
    }

    public function testToDatabaseArray() {
        $login = 'chuppthe';
        $password = 'thisisagoodpassword';
        $email = 'chuppthe@msu.edu';
        $firstName = 'Theo';
        $lastName = 'Chupp';
        $activated = 1;
        $activatedKey = 'activationkkey!!!';
        $role = array('ROLE_USER');

        $userDatabaseArray = array('login' => $login, 'password' => $password, 'email' => $email,
            'firstName' => $firstName, 'lastName' => $lastName,
            'activated' => true, 'activationKey' => $activatedKey);

        $user = new User($login, $password, $email, $firstName, $lastName, $activated, $activatedKey, $role);

        $this->assertEquals($userDatabaseArray, $user->toDatabaseArray());
    }
}
