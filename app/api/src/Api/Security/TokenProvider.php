<?php

namespace Api\Security;

class TokenProvider {

    private $secretKey;

    private $tokenDuration;

    public function __construct() {
        $this->initConfiguration();
    }

    public function createToken($login, $password) {
        $expires = time() + $this->tokenDuration;
        $authToken = $login . ":$expires:" . $this->computeSignature($login, $password, $expires);
        return ['expires' => $expires, 'authToken' => $authToken];
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

        return $expires >= time() && hash_equals($signature, $signatureToMatch);
    }

    private function computeSignature($username, $password, $expires) {
        $signature = $username . ":";
        $signature .= $expires . ":";
        $signature .= $password . ":";
        $signature .= $this->secretKey;

        return md5($signature);
    }

    private function initConfiguration() {
        require_once __DIR__ . '/../../../config/security/config.php';
        $this->secretKey = XAUTH_SECRET;
        $this->tokenDuration = XAUTH_DURATION;
    }
}
