<?php

namespace App\Observers;

use App\Models\Wishlist;
use Illuminate\Support\Facades\Cache;

class WishlistObserver
{
    /**
     * Listen to the Wishlist deleting event.
     *
     * @param Wishlist $wishlist
     * @return void
     */
    public function deleting(Wishlist $wishlist): void
    {
        Cache::forget('wishlist_'.$wishlist->user_id);
        Cache::forget('popular_games');
    }

    /**
     * Listen to the Wishlist created event.
     *
     * @param Wishlist $wishlist
     * @return void
     */
    public function created(Wishlist $wishlist): void
    {
        Cache::forget('wishlist_'.$wishlist->user_id);
        Cache::forget('popular_games');
    }
}
