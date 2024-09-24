<?php

namespace App\Providers;

use App\HDR_Segment;
use App\Interfaces\RCS_SegmentInterface;
use App\MaintenanceNotice;
use App\ShopFindings\ShopFinding;
use App\ShopFindings\ShopFindingsDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use JavaScript;

class ComposerServiceProvider extends ServiceProvider
{
    /**
     * Compile variables for info table.
     *
     * @param \App\Interfaces\RCS_SegmentInterface $notification
     * @return array
     */
    public function infoTable(RCS_SegmentInterface $notification)
    {
        $report = ShopFinding::with('HDR_Segment')
                ->with('ShopFindingsDetail.RCS_Segment')
                ->find($notification->get_RCS_SFI());
        
        $header = $report->HDR_Segment ?? $notification;
        $rcsSegment = $report->ShopFindingsDetail->RCS_Segment ?? $notification;
        
        $infoTable = [
            'notificationId' => $notification->get_RCS_SFI(),
            'RCS_MRD' => $rcsSegment->get_RCS_MRD(),
            'HDR_RON' => $header->get_HDR_RON(),
            'HDR_WHO' => $header->get_HDR_WHO(),
            'RCS_MPN' => $rcsSegment->get_RCS_MPN(),
            'RCS_SER' => $rcsSegment->get_RCS_SER()
        ];
        
        return $infoTable;
    }
    
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        // Push session alert to javascript 'sweetalert' variable.
        View::composer('*', function ($view) {
            
            $maintenanceNotices = MaintenanceNotice::where('display', 1)->orderBy('updated_at', 'desc')->get();
            
            $view->with('notices', $maintenanceNotices);
            
            $view->with('showAllSegments', session('show_all_segments')); // Add show all fields session.
            $view->with('showAllFields', session('show_all_fields')); // Add show all fields session.
            
            JavaScript::put(['sweetalert' => session('alert')]); // Add sweetalert session.
            
            View::share('viewName', $view->getName()); // Add viewName variable to all views.
        });
        
        // Data for information table.
        View::composer('partials.report-header', function($view){
            $view->with('infoTable', $this->infoTable(request()->route('notification')));
        });
    }
}