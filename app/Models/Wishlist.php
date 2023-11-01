<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @mixin IdeHelperWishlist
 */
class Wishlist extends Model
{
    /*
    |--------------------------------------------------------------------------
    | GLOBAL VARIABLES
    |--------------------------------------------------------------------------
    */

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'notification' => 'boolean',
    ];

    protected $table = 'game_wishlists';

    protected $primaryKey = 'id';

    // protected $appends = [];
    // public $timestamps = false;
    // protected $guarded = ['id'];
    protected $fillable = ['game_id', 'user_id'];
    // protected $hidden = [];
    // protected $dates = [];

    /*
    |--------------------------------------------------------------------------
    | FUNCTIONS
    |--------------------------------------------------------------------------
    */

    /*
    |
    | Method to get delivery price with or without symbol
    |
    */
    public function getMaxPrice($currency = true)
    {
        return money($this->max_price, config('settings.currency'))->format($currency, config('settings.decimal_place'));
    }

    /*
    |--------------------------------------------------------------------------
    | RELATIONS
    |--------------------------------------------------------------------------
    */

    public function game()
    {
        return $this->belongsTo('App\Models\Game');
    }

    public function user()
    {
        return $this->belongsTo('App\Models\User');
    }

    public function listings()
    {
        return $this->hasMany('App\Models\Listing', 'game_id', 'game_id')->where('status', null)->whereHas('user', function ($query) {
            $query->where('status', 1);
        })->orWhere('status', 0)->whereHas('user', function ($query) {
            $query->where('status', 1);
        });
    }

    /*
    |--------------------------------------------------------------------------
    | SCOPES
    |--------------------------------------------------------------------------
    */

    /*
    |--------------------------------------------------------------------------
    | ACCESORS
    |--------------------------------------------------------------------------
    */

    /*
    |--------------------------------------------------------------------------
    | MUTATORS
    |--------------------------------------------------------------------------
    */
}
