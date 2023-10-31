<?php

namespace App\Http\Middleware;

use Carbon;
use Closure;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class LogLastUserActivity
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if (auth()->check()) {
            $expiresAt = Carbon::now()->addMinutes(5);
            Cache::put('user-is-online-'.auth()->user()->id, true, $expiresAt);
            if (auth()->user()->last_activity_at < Carbon::now()) {
                auth()->user()->lastActivity($expiresAt);
            }
        }

        return $next($request);
    }
}
