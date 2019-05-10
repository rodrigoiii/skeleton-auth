<?php

namespace App\Mailer;

use Core\Mailer;

class RegisterVerification extends Mailer
{
    /**
     * Sending email for register verification configuration.
     *
     * @param string $receiver_name
     * @param string $receiver_email
     * @param string $link
     */
    public function __construct($receiver_name, $receiver_email, $link)
    {
        $this->subject("Register Verification");
        $this->from(['foo@bar.com' => "Skeleton Auth"]);
        $this->to([$receiver_email => $receiver_name]);
        $this->message('Click this <a href="'.$link.'">link</a> to verify the account');
    }
}
