<?php

namespace App\Http\Middleware;

use Closure;

class CheckActive
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
        if (Auth::check() && Auth::user()->active == 'y'){
            return $next($request);
        } else {
            Auth::logout();
            return redirect('/');
        }

        return $next($request);
    }
}
