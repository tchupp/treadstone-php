<?php

namespace Api\Security;

class TokenProvider {

    private $secretKey;

    private $tokenDuration;

    public function __construct() {
        $this->initConfiguration();
    }

    public function createToken($login, $password) {
        $expires = time() + 1000 * $this->tokenDuration;
        $authToken = $login . ":$expires:" . $this->computeSignature($login, $password, $expires);
        return array('expires' => $expires, 'authToken' => $authToken);
    }

    public function getLoginFromToken($authToken) {
        if ($authToken === null) {
            return null;
        }
        $parts = explode(':', $authToken);
        return $parts[0];
    }

    public function validateToken($authToken, $login, $password) {
        $parts = explode(':', $authToken);
        $expires = $parts[1];
        $signature = $parts[2];
        $signatureToMatch = $this->computeSignature($login, $password, $expires);

        return $expires >= time() && self::timeConstantEquals($signature, $signatureToMatch);
    }

    private function computeSignature($username, $password, $expires) {
        $signature = $username . ":";
        $signature .= $expires . ":";
        $signature .= $password . ":";
        $signature .= $this->secretKey;

        return md5($signature);
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

    private function initConfiguration() {
        require_once __DIR__ . '/../../../config/security/config.php';
        $this->secretKey = XAUTH_SECRET;
        $this->tokenDuration = XAUTH_DURATION;
    }
}
