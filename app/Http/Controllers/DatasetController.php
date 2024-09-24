<?php

namespace App\Http\Controllers;

use App\Alert;
use App\Location;
use Carbon\Carbon;
use App\Spec2kReport;
use App\ValidationProfiler;
use Illuminate\Http\Request;
use App\ShopFindings\ShopFinding;
use Illuminate\Support\Facades\Gate;
use Illuminate\Pagination\LengthAwarePaginator as Paginator;

class DatasetController extends Controller
{
    /**
     * Whitelist of allowed orderby parameters.
     *
     * @var array
     */
    public static $orderbyWhitelist = [
        'id' => 'ID',
        'material' => 'RCS_MPN',
        'serial' => 'RCS_SER',
        'roc' => 'HDR_ROC',
        'ron' => 'HDR_RON',
        'date' => 'RCS_MRD',
        'user' => 'acronym'
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
        $statuses = ShopFinding::$statuses;
        $reportingOrganisations = Location::filter('view-all-shopfindings');
        $orderby = self::$orderbyWhitelist[self::$defaultOrderBy];
        $order = self::$defaultOrder;
        
        if ($request->has('orderby') && array_key_exists($request->orderby, self::$orderbyWhitelist)) {
            $orderby = self::$orderbyWhitelist[$request->orderby];
        }
        
        if ($request->has('order') && in_array($request->order, ['desc', 'asc'])) {
            $order = $request->order;
        }
        
        $search = $request->search ?? NULL;
        $filter = $request->filter;
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
        
        $dateStart = $request->date_start ?? NULL;
        $dateEnd = $request->date_end ?? NULL;
        
        $datasets = ShopFinding::getDatasets($filter, $search, $dateStart, $dateEnd, $plantCode, $orderby, $order, $status, false);
        
        // Have to manually paginate results because of Laravel bug #5515 https://github.com/laravel/framework/pull/5515
		$resultsChunked = array_chunk($datasets->toArray(), 20);
		
		if (count($resultsChunked) < $request->get('page')) {
    		$request->offsetUnset('page');
		}
		
		$currentPage = $request->has('page') ? $request->get('page') - 1 : 0;
			
		if (count($datasets)) {
			$datasets = new Paginator($resultsChunked[$currentPage], count($datasets), 20, $request->get('page'), [
                'path' => Paginator::resolveCurrentPath()
            ]);
		}
        
        $request->merge(['order' => $order == 'asc' ? 'desc' : 'asc'])->flash(); // swap order
        
        return view('dataset.index')
            ->with('datasets', $datasets)
            ->with('statuses', $statuses)
            ->with('reportingOrganisations', $reportingOrganisations);
    }
}
