<?php

namespace App\Auth\Traits\Register;

use App\Mailer\RegisterVerification;
use App\Models\AuthToken;
use Core\Log;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

trait Handler
{
    /**
     * Send verification link handler
     *
     * @param  AuthToken $authToken
     * @return integer
     */
    public function sendVerificationLink(AuthToken $authToken)
    {
        $inputs = json_decode($authToken->getPayload());

        $fullname = $inputs->first_name . " " . $inputs->last_name;
        $link = base_url("auth/register/verify/" . $authToken->token);

        $registerVerification = new RegisterVerification($fullname, $inputs->email, $link);
        $recipient_nums = $registerVerification->send();

        if ($recipient_nums > 0)
        {
            Log::info("Info: Register user verification link {$link}", 1);
        }

        return $recipient_nums;
    }
}
