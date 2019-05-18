<?php

namespace App\Models;

use Carbon\Carbon;
use Core\Log;
use Illuminate\Database\Eloquent\Model;

class AuthToken extends Model
{
    const TYPE_REGISTER = "register";
    const TYPE_RESET_PASSWORD = "reset-password";
    const IS_USED = 1;

    protected $fillable = ["token", "is_used", "type", "payload"];

    public function getId()
    {
        return $this->id;
    }

    public function getPayload()
    {
        return $this->payload;
    }

    public function markTokenAsUsed()
    {
        $this->is_used = 1;
        return $this->save();
    }

    public function scopeRegisterToken($query)
    {
        return $query->where('type', static::TYPE_REGISTER);
    }

    public function scopeResetPasswordToken($query)
    {
        return $query->where('type', static::TYPE_RESET_PASSWORD);
    }

    public function scopeToken($query, $token)
    {
        return $query->where('token', $token);
    }

    public function isExpired($seconds)
    {
        return Carbon::now() >= Carbon::parse($this->created_at)->addSeconds($seconds);
    }

    public function isUsed()
    {
        return $this->is_used === static::IS_USED;
    }

    /**
     * Token is valid if:
     * - not expired
     * - not used
     *
     * @return boolean [description]
     */
    public function isValid($token_lifespan, $enabled_log=false)
    {
        if ($enabled_log)
        {
            if (!$this->isExpired($token_lifespan))
            {
                if (!$this->isUsed())
                {
                    return true;
                }
                else
                {
                    Log::warning("Warning: Token " . $this->token . " is already used!");
                }
            }
            else
            {
                Log::warning("Warning: Token " . $this->token . " is already expired!");
            }
        }

        return !$this->isExpired($token_lifespan) && !$this->isUsed();
    }

    public static function createRegisterToken($payload=[])
    {
        $registerToken = static::create([
            'token' => static::generateUniqueToken(),
            'type' => static::TYPE_REGISTER,
            'payload' => $payload
        ]);

        return $registerToken;
    }

    public static function createResetPasswordToken($payload=[])
    {
        $resetPasswordToken = static::create([
            'token' => static::generateUniqueToken(),
            'type' => static::TYPE_RESET_PASSWORD,
            'payload' => $payload
        ]);

        return $resetPasswordToken;
    }

    public static function generateUniqueToken()
    {
        $last = static::all()->last();

        $last_id = (string) !is_null($last) ? $last->getId() : 0;

        return uniqid("{$last_id}_");
    }
}
