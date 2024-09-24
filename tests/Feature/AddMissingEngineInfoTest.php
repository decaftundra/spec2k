<?php

namespace Tests\Feature;

use App\AircraftDetail;
use App\HDR_Segment;
use App\Notification;
use App\ShopFindings\AID_Segment;
use App\ShopFindings\ShopFinding;
use App\ShopFindings\ShopFindingsDetail;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Artisan;
use Tests\TestCase;

class AddMissingEngineInfoTest extends TestCase
{
    /**
     * Test command adds missing engine info in notification.
     *
     * @return void
     */
    public function testMissingEngineInfoInNotification()
    {
        $notification = factory(Notification::class)->states('all_segments_real_arcraft_data')->create();
        
        $notification->eidAET = NULL;
        $notification->eidEPC = NULL;
        $notification->eidAEM = NULL;
        $notification->eidEMS = NULL;
        $notification->eidMFR = NULL;
        $notification->eidETH = NULL;
        $notification->eidETC = NULL;
        $notification->save();
        
        Artisan::call('spec2kapp:add_missing_engine_info');
        
        $this->assertEquals('', Artisan::output());
        
        $aircraft = AircraftDetail::where('aircraft_fully_qualified_registration_no', $notification->aidREG)->first();
        
        // Re-fetch Notification.
        $notification->refresh();
        
        $this->assertEquals($notification->eidAET, $aircraft->engine_type);
        $this->assertEquals($notification->eidAEM, $aircraft->engines_series);
        $this->assertEquals($notification->eidEPC, $aircraft->engine_position_identifier);
        $this->assertEquals($notification->eidMFR, $aircraft->engine_manufacturer_code);
    }
    
    /**
     * Test command adds missing engine info in shop finding.
     *
     * @return void
     */
    public function testMissingEngineInfoInShopFinding()
    {
        ShopFinding::flushEventListeners(); // Prevents activities being recorded during seeding.
        
        $ShopFinding = factory(ShopFinding::class)->create([
                'plant_code' => $this->adminUser->location->plant_code,
                'status' => 'complete_shipped',
                'shipped_at' => $this->faker->dateTimeBetween(Carbon::now()->format('Y-m-d 00:00:00'), Carbon::now()->format('Y-m-d 23:59:59'))->format('Y-m-d 00:00:00')
            ])->each(function($sf) {
            
            $sf->HDR_Segment()->save(factory(HDR_Segment::class)->make([
                'shop_finding_id' => (string) $sf->id,
                'RON' => $this->adminUser->location->name
            ]));
            
            $sf->ShopFindingsDetail()->saveMany(
                factory(ShopFindingsDetail::class, 1)->create(['shop_finding_id' => (string) $sf->id])
                ->each(function($sfd) use($sf) {
                    $sfd->AID_Segment()->save(
                        factory(AID_Segment::class)->states('with_real_aircraft_data')->make(['shop_findings_detail_id' => $sfd->id])
                    );
                })
            );
        });
        
        ShopFinding::boot();
        
        $shopFinding = ShopFinding::with('ShopFindingsDetail.EID_Segment')
            ->with('ShopFindingsDetail.AID_Segment')
            ->first();
        
        $aircraft = AircraftDetail::where('aircraft_fully_qualified_registration_no', $shopFinding->ShopFindingsDetail->AID_Segment->get_AID_REG())->first();
        
        $this->assertEquals($shopFinding->ShopFindingsDetail->EID_Segment, NULL);
        
        Artisan::call('spec2kapp:add_missing_engine_info');
        
        $this->assertEquals('', Artisan::output());
        
        // Re-fetch data.
        $shopFinding->refresh();
        
        $this->assertEquals($shopFinding->ShopFindingsDetail->EID_Segment->get_EID_AET(), $aircraft->engine_type);
        $this->assertEquals($shopFinding->ShopFindingsDetail->EID_Segment->get_EID_AEM(), $aircraft->engines_series);
        $this->assertEquals($shopFinding->ShopFindingsDetail->EID_Segment->get_EID_EPC(), $aircraft->engine_position_identifier);
        $this->assertEquals($shopFinding->ShopFindingsDetail->EID_Segment->get_EID_MFR(), $aircraft->engine_manufacturer_code);
    }
}
