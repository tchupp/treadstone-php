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
        return self::timeConstantEquals($hash, $newHash);
    }

    private static function uniqueSalt() {
        return substr(bin2hex(openssl_random_pseudo_bytes(30)), 0, 22);
    }

    private static function timeConstantEquals($a, $b) {
        if (strlen($a) !== strlen($b)) {
            return false;
        } else {
            $equal = 0;
            for ($i = 0; $i < strlen($a); $i++) {
                $equal |= $a[$i] ^ $b[$i];
            }
            return $equal === 0;
        }
    }
}
