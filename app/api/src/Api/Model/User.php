<?php

namespace Api\Model;

use JsonSerializable;

class User implements JsonSerializable {

    private $login;
    private $password;
    private $email;
    private $firstName;
    private $lastName;
    private $activated;
    private $activationKey;
    private $roles;

    public function __construct($login, $password, $email, $firstName, $lastName, $activated, $activationKey, array $roles) {
        $this->login = $login;
        $this->password = $password;
        $this->email = $email;
        $this->firstName = $firstName;
        $this->lastName = $lastName;
        $this->activated = ($activated === 1) ? true : false;
        $this->activationKey = $activationKey;
        $this->roles = $roles;
    }

    public function toDatabaseArray() {
        return array('login' => $this->login, 'password' => $this->password, 'email' => $this->email,
            'firstName' => $this->firstName, 'lastName' => $this->lastName,
            'activated' => $this->activated, 'activationKey' => $this->activationKey);
    }

    function jsonSerialize() {
        return array('login' => $this->login, 'password' => null, 'email' => $this->email,
            'firstName' => $this->firstName, 'lastName' => $this->lastName,
            'activated' => $this->activated, 'role' => $this->roles);
    }

    public function setPassword($password) {
        $this->password = $password;
    }

    public function setFirstName($firstName) {
        $this->firstName = $firstName;
    }

    public function setLastName($lastName) {
        $this->lastName = $lastName;
    }

    public function setActivated($activated) {
        $this->activated = $activated;
    }

    public function setActivationKey($activationKey) {
        $this->activationKey = $activationKey;
    }

    public function addRole($role) {
        $this->roles[] = $role;
    }

    public function setEmail($email) {
        $this->email = $email;
    }

    public function getLogin() {
        return $this->login;
    }

    public function getPassword() {
        return $this->password;
    }

    public function getEmail() {
        return $this->email;
    }

    public function getFirstName() {
        return $this->firstName;
    }

    public function getLastName() {
        return $this->lastName;
    }

    public function getActivated() {
        return $this->activated;
    }

    public function getActivationKey() {
        return $this->activationKey;
    }

    public function getRoles() {
        return $this->roles;
    }
}
