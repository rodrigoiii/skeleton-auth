<?php

namespace App\Controllers\Auth;

use App\Models\AuthToken;
use App\Models\User;
use App\Requests\ResetPasswordRequest;
use Core\BaseController;
use Core\Log;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Exception\NotFoundException;

class ResetPasswordController extends BaseController
{
    /**
     * Display reset password page
     *
     * @param  Response $response
     * @return Response
     */
    public function getResetPassword(Request $request, Response $response, $token)
    {
        $authToken = AuthToken::resetPasswordToken()
                                ->token($token)
                                ->get()
                                ->last();

        if (!is_null($authToken))
        {
            $config = config("auth.reset_password");

            if ($authToken->isValid($config['token_lifespan'], true))
            {
                return $this->view->render($response, "auth/reset-password.twig", compact('token'));
            }
        }

        throw new NotFoundException($request, $response);
        exit;
    }

    /**
     * Post data
     *
     * @param  ResetPasswordRequest $_request
     * @param  Response $response
     * @param  string   $token
     * @return Response
     */
    public function postResetPassword(ResetPasswordRequest $_request, Response $response, $token)
    {
        $authToken = AuthToken::resetPasswordToken()
                                ->token($token)
                                ->get()
                                ->last();

        if (!is_null($authToken))
        {
            $config = config("auth.reset_password");

            if ($authToken->isValid($config['token_lifespan'], true))
            {
                $new_password = $_request->getParam('new_password');

                $payload = json_decode($authToken->getPayload());

                $user = User::find($payload->user_id);
                $user->password = password_hash($new_password, PASSWORD_DEFAULT);

                if ($user->save())
                {
                    Log::info("Info: " . $user->getFullName() . " successfully reset his/her password.");

                    $authToken->markTokenAsUsed();

                    $this->flash->addMessage('success', "Your password was successfully changed!");
                }
                else
                {
                    $this->flash->addMessage('error', "Change password not working properly. Please try again later!");
                }

                return $response->withRedirect($this->router->pathFor('auth.login'));
            }
        }

        throw new NotFoundException($_request, $response);
        exit;
    }
}
