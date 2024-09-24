<?php

namespace App\Http\Middleware;

use Auth;
use Closure;
use App\Alert;

class Admin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next, $guard = null)
    {
        if (!Auth::guard($guard)->check() || Auth::user()->isUser()) {
            abort(403, 'You are not authorised to view that page.');
        }

        return $next($request);
    }
}