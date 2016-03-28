<?php

namespace Api\Security\Util;

class SecurityUtil {

    public static function timeConstantEquals($a, $b) {
        if (strlen($a) !== strlen($b)) {
            return false;
        } else {
            $equal = 0;
            for ($i = 0; $i < strlen($a); $i++) {
                $equal |= ord($a[$i]) ^ ord($b[$i]);
            }
            return $equal === 0;
        }
    }

}
