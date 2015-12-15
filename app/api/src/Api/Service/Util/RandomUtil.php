<?php

namespace Api\Service\Util;

class RandomUtil {

    private $KEY_LENGTH = 20;

    public function generateActivationKey() {
        return substr(bin2hex(openssl_random_pseudo_bytes($this->KEY_LENGTH)), 0, $this->KEY_LENGTH);
    }
}
