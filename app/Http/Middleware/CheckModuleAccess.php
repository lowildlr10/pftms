<?php

namespace App\Http\Middleware;

use Closure;

class CheckModuleAccess
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
        $actions = $request->route()->getAction();
        $role = $request->user()->roles ? $request->user()->roles : null;
        $module = isset($actions['module']) ? $actions['module'] : null;
        $access = isset($actions['access']) ? $actions['access'] : null;

        if ($request->user() === null){
            return redirect(url('/'));
        }

        if ($request->user()->hasModuleAccess($role, $module, $access)) {
            return $next($request);
        }

        $ancor = '<a href="' . url()->previous() . '">Go back</a>';

        return response("Access denied. $ancor", 401);
    }
}
