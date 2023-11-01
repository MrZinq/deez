<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class SocialLogin.
 *
 * @mixin IdeHelperSocialLogin
 */
class SocialLogin extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'social_logins';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['user_id', 'provider', 'provider_id', 'token', 'avatar'];
}
