<?php

namespace App\Auth\Traits;

use App\Mailer\ResetPassword;
use App\Models\AuthToken;
use App\Models\User;
use Core\Log;
use Psr\Http\Message\ResponseInterface as Response;

trait ForgotPassword
{
    /**
     * Send reset password link handler
     *
     * @param  AuthToken $authToken
     * @return integer
     */
    public function sendResetPassword(AuthToken $authToken)
    {
        $payload = json_decode($authToken->getPayload());
        $user = User::find($payload->user_id);

        $fullname = $user->getFullName();
        $link = base_url("auth/reset-password/" . $authToken->token);

        $registerVerification = new ResetPassword($fullname, $user->email, $link);
        $recipient_nums = $registerVerification->send();

        if ($recipient_nums > 0)
        {
            Log::info("Info: Forgot password link for ". $user->getFullName() ." {$link}", 1);
        }

        return $recipient_nums;
    }
}
