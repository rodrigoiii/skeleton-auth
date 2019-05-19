<?php

namespace App\Requests;

use Core\BaseRequest;
use Respect\Validation\Validator as v;

class ForgotPasswordRequest extends BaseRequest
{
    /**
     * Forgot password rules
     *
     * @return array
     */
    public function rules()
    {
        return [
            'email' => v::notEmpty()->email()->emailExist()
        ];
    }
}
