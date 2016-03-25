<?php

namespace Api\Security;

class BCryptPasswordEncoder {

    private static $algorithm = '$2a';
    private static $cost = '$10';

    public function encode($password) {
        return crypt($password, self::$algorithm . self::$cost . '$' . self::uniqueSalt());
    }

    public function verify($hash, $password) {
        $salt = substr($hash, 0, 29);
        $newHash = crypt($password, $salt);
        return hash_equals($hash, $newHash);
    }

    private static function uniqueSalt() {
        return substr(bin2hex(openssl_random_pseudo_bytes(30)), 0, 22);
    }
}
