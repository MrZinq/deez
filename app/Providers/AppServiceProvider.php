<?php

namespace App\Providers;

use App\Models\Game;
use App\Models\Language;
use App\Models\Listing;
use App\Models\MenuItem;
use App\Models\Page;
use App\Models\Platform;
use App\Models\User;
use App\Models\Wishlist;
use App\Observers\GameObserver;
use App\Observers\LanguageObserver;
use App\Observers\ListingObserver;
use App\Observers\MenuItemObserver;
use App\Observers\PageObserver;
use App\Observers\PlatformObserver;
use App\Observers\UserObserver;
use App\Observers\WishlistObserver;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        // Use legacy laravel bootstrap pagination
        Paginator::useBootstrap();

        Listing::observe(ListingObserver::class);
        Game::observe(GameObserver::class);
        User::observe(UserObserver::class);
        MenuItem::observe(MenuItemObserver::class);
        Language::observe(LanguageObserver::class);
        Wishlist::observe(WishlistObserver::class);
        Page::observe(PageObserver::class);
        Platform::observe(PlatformObserver::class);
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        if ($this->app->environment() === 'local') {
            // Jeffrey Way's generators
            $this->app->register('Laracasts\Generators\GeneratorsServiceProvider');
        }
    }
}
