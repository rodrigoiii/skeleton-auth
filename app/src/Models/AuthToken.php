<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class AuthToken extends Model
{
    const TYPE_REGISTER = "register";
    const TYPE_RESET_PASSWORD = "reset-password";

    /**
     * Define fillable columns to avoid
     * mass assignment exception.
     *
     * @var array
     */
    protected $fillable = ["token", "is_used", "type", "payload"];

    /**
     * Return model id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    public function isExpired($seconds)
    {
        return Carbon::now() >= Carbon::parse($this->created_at)->addSeconds($seconds);
    }

    public function isUsed()
    {
        return $this->is_used;
    }

    public function markTokenAsUsed()
    {
        $this->is_used = 1;
        return $this->save();
    }

    public function getPayload()
    {
        return $this->payload;
    }

    /**
     * Token is valid if:
     * - existed
     * - not expired
     * - not used
     *
     * @return boolean [description]
     */
    public function isValid($token, $seconds, $type)
    {
        $authToken = static::findRegisterToken($token);
        $error_message = "";

        // check if token exist
        if (! is_null($authToken))
        {
            $auth_config = config('sklt-auth');
            $modules_config = $auth_config['modules'];

            $is_token_expired = $modules_config['register']['token_expiration'] == false ? false : $authToken->isExpired($seconds);

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
                        if ($modules_config['register']['is_log_in_after_register'])
                        {
                            // login user automatically
                            Auth::logInByUserId($user->getId());
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
    }

    public static function findRegisterToken($token)
    {
        return static::where('token', $token)
                ->where('type', static::TYPE_REGISTER)
                ->get()
                ->last();
    }

    public static function findResetPasswordToken($token)
    {
        return static::where('token', $token)
                ->where('type', static::TYPE_RESET_PASSWORD)
                ->get()
                ->last();
    }

    public static function createRegisterType($payload)
    {
        $authToken = static::create([
            'token' => uniqid(),
            'type' => static::TYPE_REGISTER,
            'payload' => $payload
        ]);

        return $authToken;
    }

    public static function createResetPasswordType($payload)
    {
        $authToken = static::create([
            'token' => uniqid(),
            'type' => static::TYPE_RESET_PASSWORD,
            'payload' => $payload
        ]);

        return $authToken;
    }

    public static function isRegisterTokenValid($token, $seconds)
    {
        return $this->isValid($token, $seconds, static::TYPE_REGISTER);
    }

    public static function isResetPasswordTokenValid($token, $seconds)
    {
        return $this->isValid($token, $seconds, static::TYPE_RESET_PASSWORD);
    }
}
