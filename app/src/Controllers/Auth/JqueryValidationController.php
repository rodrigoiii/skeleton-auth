<?php

namespace App\Controllers\Auth;

use Core\BaseController;
use Core\Log;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Respect\Validation\Validator as v;

class JqueryValidationController extends BaseController
{
    /**
     * Check if email parameter existed in database.
     *
     * @param  Request $request
     * @return string
     */
    public function emailExist(Request $request)
    {
        $params = $request->getParams();
        $invert = isset($params['invert']);
        $email_exception = isset($params['except']) ? $params['except'] : null;

        if (isset($params['email']))
        {
            $result = !$invert ?
                        v::emailExist($email_exception)->validate($params['email']) :
                        v::not(v::emailExist($email_exception))->validate($params['email']);

            return $result ? "true" : "false";
        }

        Log::error("Error: Email must be define on '/jv/email-exist' api.", 1);
        return "Parameter is missing!";
    }
}
