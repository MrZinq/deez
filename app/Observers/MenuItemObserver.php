<?php

namespace App\Observers;

use App\Models\MenuItem;
use Illuminate\Support\Facades\Cache;

class MenuItemObserver
{
    /**
     * Listen to the MenuItem created event.
     *
     * @param MenuItem $menu_item
     * @return void
     */
    public function created(MenuItem $menu_item): void
    {
        Cache::forget('menu_items');
    }

    /**
     * Listen to the MenuItem deleting event.
     *
     * @param MenuItem $menu_item
     * @return void
     */
    public function updated(MenuItem $menu_item): void
    {
        Cache::forget('menu_items');
    }

    /**
     * Listen to the MenuItem deleted event.
     *
     * @param MenuItem $menu_item
     * @return void
     */
    public function deleted(MenuItem $menu_item): void
    {
        Cache::forget('menu_items');
    }
}
