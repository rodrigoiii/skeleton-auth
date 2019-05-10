<?php

namespace App\Requests;

use Core\BaseRequest;
use Respect\Validation\Validator as v;

class RegisterRequest extends BaseRequest
{
    /**
     * Registration rules
     *
     * @return array
     */
    public function rules()
    {
        $inputs = $this->request->getParams();

        return [
            'picture' => v::optional2(v::uploaded()->file()->image()->size(null, "5mb")),
            'first_name' => v::notEmpty()->not(v::numeric()),
            'last_name' => v::notEmpty()->not(v::numeric()),
            'email' => v::notEmpty()->email()->not(v::emailExist()),
            'password' => v::notEmpty()->passwordStrength(),
            'confirm_password' => v::notEmpty()->passwordMatch($inputs['password'])
        ];
    }

    public function messages()
    {
        return [
            'uploaded' => "Picture must not be empty"
        ];
    }
}
