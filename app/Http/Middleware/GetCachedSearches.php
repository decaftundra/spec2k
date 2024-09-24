<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Cache;

class GetCachedSearches
{
    /**
     * Main listing pages.
     *
     * @var array
     */
    public static $mainListingPageRoutes = [
        'datasets.index',
        'notifications.index',
        'standby.index',
        'deleted.index'
    ];
    
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if (in_array(request()->route()->getName(), self::$mainListingPageRoutes)) {
            $key = auth()->check() ? auth()->id() . '.main-page-search' : 'main-page-search';
        } else {
            $key = auth()->check() ? auth()->id() . '.' . request()->route()->getName() : request()->route()->getName();
        }
        
        if ($request->has('reset')) {
            Cache::forget($key);
            
            $queryParams = [];
        } else if (count($request->all())) {
            Cache::forever($key, $request->query());
            $queryParams = Cache::get($key);
        } else {
            Cache::rememberForever($key, function() use ($request) {
                return $request->query();
            });
            $queryParams = Cache::get($key);
        }
        
        if (!empty($queryParams)) {
            ksort($queryParams);
            $request->merge($queryParams)->flash();
        }
        
        return $next($request);
    }
}
