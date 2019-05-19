<?php

namespace App\Controllers\Auth;

use App\Auth\Traits\ForgotPassword;
use App\Models\AuthToken;
use App\Models\User;
use App\Requests\ForgotPasswordRequest;
use Core\BaseController;
use Psr\Http\Message\ResponseInterface as Response;

class ForgotPasswordController extends BaseController
{
    use ForgotPassword;

    /**
     * Display forgot password page
     *
     * @param  Response $response
     * @return Response
     */
    public function getForgotPassword(Response $response)
    {
        return $this->view->render($response, "auth/forgot-password.twig");
    }

    /**
     * Post data
     *
     * @param  ForgotPasswordRequest $_request
     * @param  Response              $response
     * @return Response
     */
    public function postForgotPassword(ForgotPasswordRequest $_request, Response $response)
    {
        $user = User::findByEmail($_request->getParam('email'));

        // create token reset password type
        $authToken = AuthToken::createResetPasswordToken(json_encode(['user_id' => $user->getId()]));

        $recipient_num = $this->sendResetPassword($authToken);

        if ($recipient_num > 0)
        {
            $this->flash->addMessage('success', "Success! Please check your email to reset your password.");
            return $response->withRedirect($this->router->pathFor('auth.login'));
        }
        else
        {
            $this->flash->addMessage('error', "Reset password not working properly this time. Please try again later.");
            return $response->withRedirect($this->router->pathFor('auth.forgot-password'));
        }
    }
}
