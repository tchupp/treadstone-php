<?php

namespace Api\Service;

use Api\Model\User;

class MailService {

    private $from = 'no-reply@treadcourse.com';

    public function sendEmail($to, $from, $subject, $message) {
        $headers = "From: $from\n";
        $headers .= "Content-Type: text/html; charset=UTF-8";
        return mail($to, $subject, $message, $headers);
    }

    public function sendActivationEmail(User $user, $baseUrl) {
        $to = $user->getEmail();
        $activationKey = $user->getActivationKey();

        $subject = 'Account Activation';
        // TODO activation url
        $activationUrl = "http://$baseUrl/api/activate?key=$activationKey";
        $message = "<html><a href='$activationUrl'>Activate Account</a></html>";

        return $this->sendEmail($to, $this->from, $subject, $message);
    }

    public function sendPasswordResetEmail(User $user, $baseUrl) {
        $to = $user->getEmail();
        $resetKey = $user->getResetKey();

        $subject = 'Password Reset';
        // TODO reset url
        $resetUrl = "http://$baseUrl/api/account/reset_password/finish?key=$resetKey";
        $message = "<html><a href='$resetUrl'>Reset Password</a></html>";

        return $this->sendEmail($to, $this->from, $subject, $message);
    }
}
