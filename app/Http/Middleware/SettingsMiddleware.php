<?php

namespace App\Http\Middleware;

use Closure;

/**
 * Class ThemeMiddleware.
 */
class SettingsMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure                 $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        // Show cookie consent if option is enabled in the admin dashboard
        if (config('settings.cookie_consent')) {
            config()->set('cookie-consent.enabled', true);
        }

        return $next($request);
    }
}
