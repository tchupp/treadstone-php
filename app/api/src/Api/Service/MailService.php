<?php

namespace Api\Service;

class MailService {

    public function sendEmail($to, $from, $subject, $message) {
        $headers = "From: $from\n";
        $headers .= "Content-Type: text/html; charset=UTF-8";
        return mail($to, $subject, $message, $headers);
    }

    public function sendActivationEmail($user, $baseUrl) {
        $to = $user['email'];
        $from = 'no-reply@treadcourse.com';
        $subject = 'Account Activation';
        $activationKey = $user['activation_key'];
        $activationUrl = "http://$baseUrl/api/activate?key=$activationKey";
        $message = "<html><a href='$activationUrl'>Activate Account</a></html>";

        return $this->sendEmail($to, $from, $subject, $message);
    }
}
