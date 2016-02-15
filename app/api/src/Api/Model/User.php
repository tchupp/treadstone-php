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
    private $resetKey;
    private $roles;

    public function __construct($login, $password, $email, $firstName, $lastName, $activated, $activationKey, $resetKey, array $roles) {
        $this->login = $login;
        $this->password = $password;
        $this->email = $email;
        $this->firstName = $firstName;
        $this->lastName = $lastName;
        $this->activated = ($activated == true) ? 1 : 0;
        $this->activationKey = $activationKey;
        $this->resetKey = $resetKey;
        $this->roles = $roles;
        $this->resetKey = $resetKey;
    }

    public function toDatabaseArray() {
        return array('login' => $this->login, 'password' => $this->password, 'email' => $this->email,
            'firstName' => $this->firstName, 'lastName' => $this->lastName,
            'activated' => $this->activated, 'activationKey' => $this->activationKey, 'resetKey' =>$this->resetKey);
    }

    function jsonSerialize() {
        return array('login' => $this->login, 'password' => null, 'email' => $this->email,
            'firstName' => $this->firstName, 'lastName' => $this->lastName,
            'activated' => $this->activated, 'role' => $this->roles);
    }

    public function setPassword($password) {
        $this->password = $password;
    }

    public function setEmail($email) {
        $this->email = $email;
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

    public function setResetKey($resetKey) {
        $this->resetKey = $resetKey;
    }

    public function addRole($role) {
        $this->roles[] = $role;
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

    public function getResetKey() {
        return $this->resetKey;
    }

    public function getRoles() {
        return $this->roles;
    }
}
