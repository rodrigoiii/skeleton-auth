<?php

namespace App\Auth\Traits\Register;

use App\Auth\Auth;
use App\Models\AuthToken;
use App\Models\User;
use App\Requests\RegisterRequest;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

trait Register
{
    use Handler;

    /**
     * Display registration page
     *
     * @param  Response $response
     * @return Response
     */
    public function getRegister(Response $response)
    {
        return $this->view->render($response, "auth/register.twig");
    }

    /**
     * Post data
     *
     * @param  RegisterRequest $_request
     * @param  Response $response
     * @return Response
     */
    public function postRegister(RegisterRequest $_request, Response $response)
    {
        $inputs = $_request->getParams();
        $files = $_request->getUploadedFiles();

        $config = config("auth.register");

        // upload picture and pass the path
        $picture = upload($files['picture'], $config['upload_path']);

        $data = [
            'picture' => $picture,
            'first_name' => $inputs['first_name'],
            'last_name' => $inputs['last_name'],
            'email' => $inputs['email'],
            'password' => password_hash($inputs['password'], PASSWORD_DEFAULT)
        ];

        if ($config['is_verification_enabled'])
        {
            // create token register type
            $authToken = AuthToken::createRegisterType(json_encode($data));

            if ($authToken instanceof AuthToken)
            {
                // send verification link
                $recipient_num = $this->sendVerificationLink($authToken);

                return $recipient_num > 0 ?
                        $this->sendEmailLinkSuccess($response) :
                        $this->sendEmailLinkError($response, "Error: Sending email contains verification link fail.");
            }

            return $this->saveAuthTokenError($response, "Error: Saving Auth token fail!");
        }

        // else
        $user = $this->saveUserInfo($data);

        if ($user instanceof User)
        {
            if ($config['is_log_in_after_register'])
            {
                // login user automatically
                Auth::logInByUserId($user->getId());
                return $this->registerSuccessRedirectToHome($response);
            }

            return $this->registerSuccess($response);
        }

        return $this->registerError($response, "Error: Saving user info fail!");
    }

    /**
     * Save user info after the token verify
     *
     * @param  Request  $request
     * @param  Response $response
     * @param  string   $token
     * @return Response
     */
    public function verify(Request $request, Response $response, $token)
    {
        $authToken = AuthToken::findRegisterToken($token);

        $error_message = "";

        // check if token exist
        if (! is_null($authToken))
        {
            $config = config("auth.register");

            $is_token_expired = $config['token_expiration'] == false ? false : $authToken->isExpired($config['token_expiration']);

            // check if token not expired
            if (!$is_token_expired)
            {
                // check if token is not already used
                if (! $authToken->isUsed())
                {
                    $authToken->markTokenAsUsed();

                    // save user info
                    $user = $this->saveUserInfo(json_decode($authToken->getPayload(), true));

                    if ($user instanceof User)
                    {
                        if ($config['is_log_in_after_register'])
                        {
                            // login user automatically
                            Auth::logInByUserId($user->getId());
                            return $this->registerSuccessRedirectToHome($response);
                        }

                        return $this->verifySuccess($response);
                    }

                    $error_message = "Error: Saving user info fail!";
                }
                else
                {
                    $error_message = "Warning: Token " . $authToken->token . " is already used!";
                }
            }
            else
            {
                $error_message = "Warning: Token " . $authToken->token . " is already expired!";
            }
        }
        else
        {
            $error_message = "Warning: Token " . $authToken->token . " is not exist!";
        }

        return $this->verifyError($request, $response, $error_message);
    }
}
