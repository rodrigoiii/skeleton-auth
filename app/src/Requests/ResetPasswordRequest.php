<?php

namespace App\Requests;

use Core\BaseRequest;
use Respect\Validation\Validator as v;

class ResetPasswordRequest extends BaseRequest
{
    /**
     * Reset password rules
     *
     * @return array
     */
    public function rules()
    {
        $inputs = $this->request->getParams();

        return [
            'new_password' => v::notEmpty()->passwordStrength(),
            'confirm_new_password' => v::notEmpty()->passwordMatch($inputs['new_password'])
        ];
    }
}
