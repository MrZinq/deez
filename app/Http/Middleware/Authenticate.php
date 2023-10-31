<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Auth\Middleware\Authenticate as BaseAuthenticate;
use Illuminate\Http\Request;

class Authenticate extends BaseAuthenticate
{
    /**
     * Handle an incoming request.
     *
     * @param  Request  $request
     * @param Closure $next
     * @param  string[]  ...$guards
     * @return mixed
     *
     * @throws AuthenticationException
     */
    public function handle($request, Closure $next, ...$guards): mixed
    {
        // check if user account is active
        if (auth()->check() && ! auth()->user()?->isActive()) {
            auth()->logout();

            return redirect('login')->with('error', trans('auth.deactivated'));
        }

        return parent::handle($request, $next, ...$guards);
    }
}
