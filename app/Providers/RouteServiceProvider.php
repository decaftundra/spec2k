<?php

namespace App\Providers;

use App\Notification;
use App\ShopFindings\ShopFinding;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * This namespace is applied to your controller routes.
     *
     * In addition, it is set as the URL generator's root namespace.
     *
     * @var string
     */
    protected $namespace = 'App\Http\Controllers';
    
    /**
     * The path to the "home" route for your application.
     *
     * @var string
     */
    public const HOME = '/home';

    /**
     * Define your route model bindings, pattern filters, etc.
     *
     * @return void
     */
    public function boot()
    {
        $this->configureRateLimiting();
        
        $this->routes(function () {
            Route::prefix('api')
                ->middleware('api')
                ->namespace($this->namespace)
                ->group(base_path('routes/api.php'));

            Route::middleware('web')
                ->namespace($this->namespace)
                ->group(base_path('routes/web.php'));
        });
        
        // Look for Notification first.
        Route::bind('notification', function ($value, $route) {
            $notification = Notification::where('rcsSFI', $value)->first();
            
            // It's possible that the original notification no longer exists in the database.
            if (!$notification) {
                // If a shop finding record exists with a header create a dummy notification to use, else return 404 error.
                $shopFinding = ShopFinding::with('HDR_Segment')->findOrFail($value);
                
                $notification = new Notification;
                $notification->id = $value;
                $notification->rcsSFI = $value;
                $notification->plant_code = $shopFinding->plant_code;
                
                // If there is a header segment, get the cage code to authorize users to view the record.
                //$notification->hdrROC = $shopFinding->HDR_Segment ? $shopFinding->HDR_Segment->get_HDR_ROC() : NULL;
            }
            
            return $notification;
        });
        
        Route::bind('shop_finding', function ($value, $route) {
            return ShopFinding::with('HDR_Segment')
                ->with('ShopFindingsDetail.AID_Segment')
                ->with('ShopFindingsDetail.EID_Segment')
                ->with('ShopFindingsDetail.API_Segment')
                ->with('ShopFindingsDetail.RCS_Segment')
                ->with('ShopFindingsDetail.SAS_Segment')
                ->with('ShopFindingsDetail.SUS_Segment')
                ->with('ShopFindingsDetail.RLS_Segment')
                ->with('ShopFindingsDetail.LNK_Segment')
                ->with('ShopFindingsDetail.ATT_Segment')
                ->with('ShopFindingsDetail.SPT_Segment')
                ->with('ShopFindingsDetail.Misc_Segment')
                ->with('PiecePart.PiecePartDetails.WPS_Segment')
                ->with('PiecePart.PiecePartDetails.NHS_Segment')
                ->with('PiecePart.PiecePartDetails.RPS_Segment')
                ->find($value);
        });
    }
    
    /**
     * Configure the rate limiters for the application.
     *
     * @return void
     */
    protected function configureRateLimiting()
    {
        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(60)->by(optional($request->user())->id ?: $request->ip());
        });
    }
}
