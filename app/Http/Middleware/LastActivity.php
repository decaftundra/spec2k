<?php

namespace App\Http\Middleware;

use App\User;
use Carbon\Carbon;
use Closure;

class LastActivity
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
            $dispatcher = User::getEventDispatcher();
            
            // Disabling the events.
            User::unsetEventDispatcher();
            
            auth()->user()->last_active_at = Carbon::now();
            auth()->user()->save();
            
            // Enabling the event dispatcher.
            User::setEventDispatcher($dispatcher);
        }
        
        return $next($request);
    }
}
