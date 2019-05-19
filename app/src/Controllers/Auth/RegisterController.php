<?php

namespace App\Controllers\Auth;

use App\Auth\Auth;
use App\Auth\Traits\Register;
use App\Models\AuthToken;
use App\Models\User;
use App\Requests\RegisterRequest;
use Core\BaseController;
use Core\Log;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Exception\NotFoundException;

class RegisterController extends BaseController
{
    use Register;
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
            $authToken = AuthToken::createRegisterToken(json_encode($data));

            if ($authToken instanceof AuthToken)
            {
                // send verification link
                $recipient_num = $this->sendVerificationLink($authToken);

                if ($recipient_num > 0)
                {
                    $this->flash->addMessage('success', "Success! Please check your email to verify your account.");
                    return $response->withRedirect($this->router->pathFor('auth.login'));
                }
            }

            $this->flash->addMessage('error', "Registration not working properly this time. Please try again later.");
            return $response->withRedirect($this->router->pathFor('auth.register'));
        }

        // else
        $user = User::create($data);

        if ($user instanceof User)
        {
            $this->flash->addMessage('success', "Successfully Register!");

            if ($config['is_log_in_after_register'])
            {
                // login user automatically
                Auth::logInByUserId($user->getId());

                return $response->withRedirect($this->router->pathFor('auth.home'));
            }

            return $response->withRedirect($this->router->pathFor('auth.login'));
        }

        $this->flash->addMessage('error', "Registration not working properly this time. Please try again later.");
        return $response->withRedirect($this->router->pathFor('auth.register'));
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
        $authToken = AuthToken::registerToken()
                            ->token($token)
                            ->get()
                            ->last();

        if (!is_null($authToken))
        {
            $config = config("auth.register");

            if ($authToken->isValid($config['token_lifespan'], true))
            {
                $authToken->markTokenAsUsed();

                // save user info
                $user = User::create(json_decode($authToken->getPayload(), true));

                if ($user instanceof User)
                {
                    if ($config['is_log_in_after_register'])
                    {
                        // login user automatically
                        Auth::logInByUserId($user->getId());

                        $this->flash->addMessage('success', "Successfully Register!");
                        return $response->withRedirect($this->router->pathFor('auth.home'));
                    }

                    $this->flash->addMessage('success', "Your account has been verified. Please login using your new account.");
                    return $response->withRedirect($this->router->pathFor('auth.login'));
                }
                else
                {
                    Log::error("Error: Saving user fail!");
                }
            }
        }

        throw new NotFoundException($request, $response);
        exit;
    }
}
