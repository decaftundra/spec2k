<?php

namespace App\Console\Commands;

use App\AircraftDetail;
use App\Notification;
use App\ShopFindings\EID_Segment;
use App\ShopFindings\ShopFinding;
use App\ValidationProfiler;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Validator;

class AddMissingEngineInformation extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'spec2kapp:add_missing_engine_info';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Adds missing Engine Information to saved and unsaved Notifications.';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        // Find all notifications with Airframe Information, but no Engine Information.
        $notifications = Notification::whereNotNull('aidREG')->whereNull('eidAET')->get();
        
        if (count($notifications)) {
            foreach ($notifications->chunk(100) as $chunk) {
                foreach ($chunk as $notification) {
                    $aircraft = AircraftDetail::where('aircraft_fully_qualified_registration_no', $notification->aidREG)->get();
                
                    if (count($aircraft) == 1) {
                        // Add Engine Information to Notification.
                        $notification->eidAET = substr($aircraft[0]->engine_type, 0, 20);
                        $notification->eidAEM = substr($aircraft[0]->engines_series, 0, 32);
                        $notification->eidEPC = substr($aircraft[0]->engine_position_identifier, 0, 25);
                        $notification->eidMFR = substr($aircraft[0]->engine_manufacturer_code, 0, 5);
                        $notification->save();
                    }
                }
            }
        }
            
        // Find all Shop Findings with saved AirFrame Information Segments and no Engine Information.
        $shopFindings = Shopfinding::with('ShopFindingsDetail.AID_Segment')
            ->with('ShopFindingsDetail.EID_Segment')
            ->whereHas('ShopFindingsDetail.AID_Segment', function($query){
                $query->whereNotNull('REG')->whereNotNull('MFN');
            })
            ->whereDoesntHave('ShopFindingsDetail.EID_Segment')
            ->whereIn('status', ['complete_shipped', 'complete_scrapped'])
            ->whereNotNull('plant_code')
            ->get();
        
        if (count($shopFindings)) {
            
            // Autosave Engine Information Segments.
            foreach ($shopFindings->chunk(100) as $chunk) {
                foreach ($chunk as $shopFinding) {
                    $reg = $shopFinding->ShopFindingsDetail->AID_Segment->get_AID_REG();
                    $mfn = $shopFinding->ShopFindingsDetail->AID_Segment->get_AID_MFN();
                    
                    // See if there is a unique aircraft record in the Database with this reg number.
                    $aircraft = AircraftDetail::where('aircraft_fully_qualified_registration_no', $reg)
                        ->where('manufacturer_name', $mfn)
                        ->get();
                    
                    if (count($aircraft) == 1) {
                        $EID_Segment = new EID_Segment;
                        
                        $EID_Segment->shop_findings_detail_id = $shopFinding->ShopFindingsDetail->id;
                        $EID_Segment->AET = substr($aircraft[0]->engine_type, 0, 20);
                        $EID_Segment->AEM = substr($aircraft[0]->engines_series, 0, 32);
                        $EID_Segment->EPC = substr($aircraft[0]->engine_position_identifier, 0, 25);
                        $EID_Segment->MFR = substr($aircraft[0]->engine_manufacturer_code, 0, 5);
                        
                        // Validate...
                        $profiler = new ValidationProfiler('EID_Segment', $EID_Segment, $shopFinding->id);
                        $attributes = $EID_Segment::getKeys();
                        
                        $data = [];
                        
                        foreach ($attributes as $attribute) {
                            $methodName = $EID_Segment->getPrefix().$attribute;
                            
                            if (method_exists($EID_Segment, $methodName)) {
                                $data[$attribute] = $EID_Segment->$methodName();
                            }
                        }
                        
                        $validator = Validator::make($data, $profiler->getValidationRules($shopFinding->id));
                
                        // Add any conditional validation.
                        $validatedConditionally = $profiler->conditionalValidation($validator);
                        
                        if (!$validatedConditionally->fails()) {
                            $EID_Segment->save();
                        } else {
                            //mydd($validatedConditionally->errors()->all());
                            //mydd($data);
                            //$this->error("$segmentName ID: {$notification->get_RCS_SFI()} invalid!!!");
                            $this->error(mydd($data));
                            $this->error(mydd($validatedConditionally->errors()->all()));
                        }
                    }
                }
            }
        }
        
        return 0;
    }
}
