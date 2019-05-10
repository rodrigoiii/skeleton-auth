<?php

namespace App\Auth\Traits\Register;

use App\Mailer\RegisterVerification;
use App\Models\AuthToken;
use App\Models\User;
use Core\Log;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Exception\NotFoundException;

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

    /**
     * Email successfully sent handler
     *
     * @param  Response $response
     * @return integer
     */
    public function sendEmailLinkSuccess(Response $response)
    {
        $this->flash->addMessage('success', "Success! Please check your email to verify your account.");
        return $response->withRedirect($this->router->pathFor('auth.login'));
    }

    /**
     * Email not sent handler
     *
     * @param  Response $response
     * @param  string   $error_message
     * @return Response
     */
    public function sendEmailLinkError(Response $response, $error_message=null)
    {
        Log::error($error_message);

        return $this->registerError($response);
    }

    /**
     * Save user info handler
     *
     * @param  array  $inputs
     * @return User
     */
    public function saveUserInfo(array $inputs)
    {
        $user = User::create($inputs);
        return $user;
    }

    /**
     * Success register handler
     *
     * @param  Response $response
     * @return Response
     */
    public function registerSuccess(Response $response)
    {
        $this->flash->addMessage('success', "Successfully Register!");
        return $response->withRedirect($this->router->pathFor('auth.login'));
    }

    /**
     * Success register and redirect to home handler
     *
     * @param  Response $response
     * @return Response
     */
    public function registerSuccessRedirectToHome(Response $response)
    {
        $this->flash->addMessage('success', "Successfully Register!");
        return $response->withRedirect($this->router->pathFor('auth.home'));
    }

    /**
     * Error register handler
     *
     * @param  Response $response
     * @param  string   $error_message
     * @return Response
     */
    public function registerError(Response $response, $error_message=null)
    {
        Log::error($error_message);

        $this->flash->addMessage('error', "Registration not working properly this time. Please try again later.");
        return $response->withRedirect($this->router->pathFor('auth.register'));
    }

    /**
     * Save auth token handler
     *
     * @param  Response $response
     * @param  string   $error_message
     * @return Response
     */
    public function saveAuthTokenError(Response $response, $error_message=null)
    {
        Log::error($error_message);

        return $this->registerError($response);
    }

    /**
     * Success verify account handler
     *
     * @param  Response $response
     * @return Response
     */
    public function verifySuccess(Response $response)
    {
        $this->flash->addMessage('success', "Your account has been verified. Please login using your new account.");
        return $response->withRedirect($this->router->pathFor('auth.login'));
    }

    /**
     * Error verify account handler
     *
     * @param  Request $request
     * @param  Response $response
     * @param  string   $error_message
     * @return Response
     */
    public function verifyError(Request $request, Response $response, $error_message=null)
    {
        Log::error($error_message);

        throw new NotFoundException($request, $response);
        exit;
    }
}
