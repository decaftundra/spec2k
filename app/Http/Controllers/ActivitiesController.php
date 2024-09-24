<?php

namespace App\Http\Controllers;

use App\User;
use App\Activity;
use App\Location;
use Illuminate\Http\Request;
use App\Policies\ActivityPolicy;
use Illuminate\Support\Facades\Gate;
use Illuminate\Pagination\LengthAwarePaginator as Paginator;

class ActivitiesController extends Controller
{
    /**
     * Whitelist of allowed orderby parameters.
     *
     * @var array
     */
    public static $orderbyWhitelist = [
        'user' => 'actioner.last_name',
        'action' => 'name',
        'notification_id' => 'notification_id',
        'date' => 'activities.created_at'
    ];
    
    /**
     * The default order by column.
     *
     * @var string
     */
    public static $defaultOrderBy = 'date';
    
    /**
     * The default order.
     *
     * @var string
     */
    public static $defaultOrder = 'desc';
    
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $this->authorize('index', Activity::class);
        
        $timezone = $this->getUserTimezone();
        $reportingOrganisations = Location::filter('view-all-activities');
        
        $types = [
            'User' => 'User',
            'PartList' => 'PartList',
            'HDR_Segment' => 'HDR_Segment',
            'WPS_Segment' => 'WPS_Segment',
            'NHS_Segment' => 'NHS_Segment',
            'RPS_Segment' => 'RPS_Segment',
            'AID_Segment' => 'AID_Segment',
            'EID_Segment' => 'EID_Segment',
            'API_Segment' => 'API_Segment',
            'RCS_Segment' => 'RCS_Segment',
            'SAS_Segment' => 'SAS_Segment',
            'SUS_Segment' => 'SUS_Segment',
            'RLS_Segment' => 'RLS_Segment',
            'LNK_Segment' => 'LNK_Segment',
            'ATT_Segment' => 'ATT_Segment',
            'SPT_Segment' => 'SPT_Segment',
            'Misc_Segment' => 'Misc_Segment',
        ];
        
        $search = $request->search ?? NULL;
        $orderby = self::$orderbyWhitelist[self::$defaultOrderBy];
        $order = self::$defaultOrder;
        
        if ($request->has('orderby') && array_key_exists($request->orderby, self::$orderbyWhitelist)) {
            $orderby = self::$orderbyWhitelist[$request->orderby];
        }
        
        if ($request->has('order') && in_array($request->order, ['desc', 'asc'])) {
            $order = $request->order;
        }
        
        $type = $request->type ?? NULL;
        $plantCode = $request->pc ?? NULL;
        $dateStart = $request->date_start ?? NULL;
        $dateEnd = $request->date_end ?? NULL;
        
        if (auth()->check() && Gate::denies('view-all-activities')) {
            $plantCode = Location::findOrFail(auth()->user()->location_id)->plant_code;
        }
        
        $activities = Activity::getActivities($search, $type, $plantCode, $dateStart, $dateEnd, $orderby, $order);
        
        $request->merge(['order' => $order == 'asc' ? 'desc' : 'asc'])->flash(); // swap order
        
        return view('activity.index')
            ->with('timezone', $timezone)
            ->with('activities', $activities)
            ->with('types', $types)
            ->with('reportingOrganisations', $reportingOrganisations);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(User $user)
    {
        $authUser = auth()->user();
        
        if (Gate::denies('view-a-users-activities', $user)) {
            abort(403, 'You are not authorised to view that page.');
        }
        
        $activities = $user->activities()
            ->orderBy('created_at', 'DESC')
            ->paginate(20);
            
        $timezone = $this->getUserTimezone();
        
        return view('activity.show')
            ->with('user', $user)
            ->with('timezone', $timezone)
            ->with('activities', $activities);
    }
    
    /**
     * Show the logged in user's activity.
     *
     * @return \Illuminate\Http\Response
     */
    public function showMyActivity()
    {
        $user = auth()->user();
        $timezone = $this->getUserTimezone();
        
        $activities = $user->activities()
            ->orderBy('created_at', 'DESC')
            ->paginate(20);
        
        return view('activity.show-user-activity')
            ->with('user', $user)
            ->with('timezone', $timezone)
            ->with('activities', $activities);
    }
    
    /**
     * Get the timezone of the authenticated user.
     *
     * @return string
     */
    public function getUserTimezone()
    {
        return auth()->check() && auth()->user()->location ? auth()->user()->location->timezone : 'UTC';
    }
}
