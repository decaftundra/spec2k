<?php

namespace Tests\Browser;

use App\Codes\UtasReasonCode;
use App\Notification;
use App\ShopFindings\MISC_Segment;
use App\ShopFindings\RCS_Segment;
use App\ShopFindings\ShopFinding;
use App\ShopFindings\ShopFindingsDetail;
use App\UtasCode;
use Tests\DuskTestCase;
use Laravel\Dusk\Browser;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class MISC_SegmentTest extends DuskTestCase
{
    /**
     * A Dusk test example.
     *
     * @return void
     */
    public function testUpdateMISC_Segment()
    {
        $utasParts = UtasCode::getAllUtasCodes();
        
        $notification = Notification::whereIn('rcsMPN', $utasParts)
            ->where('plant_code', 3101)
            ->inRandomOrder()
            ->first();
            
        // We may need to create an RCS Segment & related for this notification first.
        
        $shopFinding = ShopFinding::find($notification->get_RCS_SFI());
        
        if (!$shopFinding) {
            $shopFinding = factory(ShopFinding::class)->create([
                'id' => $notification->get_RCS_SFI(),
                'plant_code' => $notification->plant_code
            ]);
        }
        
        $shopFindingsDetail = ShopFindingsDetail::where('shop_finding_id', $shopFinding->id)->first();
        
        if (!$shopFindingsDetail) {
            $shopFindingsDetail = factory(ShopFindingsDetail::class)->create([
                'shop_finding_id' => $shopFinding->id
            ]);
        }
        
        $RCS_Segment = factory(RCS_Segment::class)
            ->states('collins_part')
            ->create([
            'shop_findings_detail_id' => $shopFindingsDetail->id,
            'SFI' => $notification->get_RCS_SFI(),
            'MPN' => $notification->get_RCS_MPN(),
            'RRC' => $notification->get_RCS_RRC()
        ]);
        
        $MISC_Segment = factory(MISC_Segment::class)->make();
        
        $utasCodes = UtasCode::where('PLANT', $notification->plant_code)
                ->where('MATNR', $notification->get_RCS_MPN())
                ->inRandomOrder()
                ->first();
                
        $utasReasonCodes = UtasReasonCode::where('PLANT', $notification->plant_code)
            ->where('TYPE', $notification->get_RCS_RRC())
            ->where('PLANT', $notification->plant_code)
            ->inRandomOrder()
            ->first();
            
        //mydd($utasReasonCodes->toArray());
        
        //mydd($utasCodes->toArray());
        
        $this->browse(function (Browser $browser) use ($notification, $MISC_Segment, $utasCodes, $utasReasonCodes) {
            $browser->loginAs($this->dataAdminUser)
                    ->visit(route('misc-segment.edit', $notification->get_RCS_SFI()))
                    ->assertSeeIn('h1', 'Collins Fields')
                    ->check('#show_all_fields')
                    ->check('#show_all_segments')
                    ->pause(3000)
                    ->select('#Reason', $utasReasonCodes->REASON)
                    ->pause(3000)
                    ->select('#SubassemblyName', $utasCodes->SUB)
                    ->pause(3000)
                    ->select('#Component', $utasCodes->COMP)
                    ->pause(3000)
                    ->select('#FeatureName', $utasCodes->FEAT ? strtolower($utasCodes->FEAT) : NULL)
                    ->pause(3000)
                    ->select('#FailureDescription', strtolower($utasCodes->DESCR))
                    ->pause(3000)
                    ->type('#Modifier', $this->faker->optional()->word)
                    ->type('#Comments', $this->faker->optional()->word)
                	->assertSee('Collins Fields')
                	->press('Save')
                	->waitForText('Success')
                    ->assertSee('Misc segment saved successfully!')
                    ->pause(2000)
                    ->press('Delete')
                    ->assertSee('Are you sure?')
                    ->pause(2000)
                    ->press('.confirm')
                    ->waitForText('Success')
                    ->assertSee('Segment deleted successfully!');
        });
    }
}
