<?php

namespace App\Http\Controllers;

use App\Extract;
use App\Location;
use Illuminate\Http\Request;
use App\ShopFindings\ShopFinding;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Cache;
use App\Notification;
use Illuminate\Pagination\LengthAwarePaginator as Paginator;

class NotificationsController extends Controller
{
    /**
     * Whitelist of allowed orderby parameters.
     *
     * @var array
     */
    public static $orderbyWhitelist = [
        'id' => 'notifications.rcsSFI',
        'material' => 'notifications.rcsMPN',
        'serial' => 'notifications.rcsSER',
        'roc' => 'notifications.hdrROC',
        'ron' => 'notifications.hdrRON',
        'date' => 'notifications.rcsMRD'
    ];
    
    /**
     * The default order by column.
     *
     * @var string
     */
    public static $defaultOrderBy = 'id';
    
    /**
     * The default order.
     *
     * @var string
     */
    public static $defaultOrder = 'asc';
    
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $statuses = Notification::$statuses;
        
        $reportingOrganisations = Location::filter('view-all-notifications');
        $orderby = self::$orderbyWhitelist[self::$defaultOrderBy];
        $order = self::$defaultOrder;
        
        if ($request->has('orderby') && array_key_exists($request->orderby, self::$orderbyWhitelist)) {
            $orderby = self::$orderbyWhitelist[$request->orderby];
        }
        
        if ($request->has('order') && in_array($request->order, ['desc', 'asc'])) {
            $order = $request->order;
        }
        
        $search = $request->search ?? NULL;
        $dateStart = $request->date_start ?? NULL;
        $dateEnd = $request->date_end ?? NULL;
        $status = $request->status ?? NULL;
        
        if ($request->pc == 'All') {
            $plantCode = NULL;
        } else if ($request->pc) {
            $plantCode = $request->pc;
        } else {
            // Sometimes there may be no results from the default location.
            $plantCode = array_key_exists(auth()->user()->defaultLocation(), $reportingOrganisations) ? auth()->user()->defaultLocation() : NULL;
        }
        
        // Determine if the user can view all notifications and restrict accordingly.
        if (Gate::denies('view-all-notifications')) {
            $plantCode = auth()->user()->location->plant_code;
        }
        
        $notifications = Notification::getToDoList($search, $status, $plantCode, $dateStart, $dateEnd, $orderby, $order);
        
        // Have to manually paginate results because of Laravel bug #5515 https://github.com/laravel/framework/pull/5515
		$resultsChunked = array_chunk($notifications->toArray(), 20);
		
		if (count($resultsChunked) < $request->get('page')) {
    		$request->offsetUnset('page');
		}
		
		$currentPage = $request->has('page') ? $request->get('page') - 1 : 0;
			
		if (count($notifications)) {
			$notifications = new Paginator($resultsChunked[$currentPage], count($notifications), 20, $request->get('page'), [
                'path' => Paginator::resolveCurrentPath()
            ]);
		}
        
        $latestExtract = Cache::get('latest_extract', function () {
            return Extract::where('errors', 0)->orderBy('created_at', 'DESC')->first();
        });
        
        $timezone = auth()->check() && auth()->user()->location ? auth()->user()->location->timezone : 'UTC';
        
        $request->merge(['order' => $order == 'asc' ? 'desc' : 'asc'])->flash(); // swap order
        
        return view('notifications.index')
            ->with('notifications', $notifications)
            ->with('statuses', $statuses)
            ->with('latestExtract', $latestExtract)
            ->with('timezone', $timezone)
            ->with('reportingOrganisations', $reportingOrganisations);
    }
}
