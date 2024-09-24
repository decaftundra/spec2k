<?php

namespace Tests\Browser;

use App\Notification;
use App\ShopFindings\RCS_Segment;
use App\ShopFindings\ShopFinding;
use App\ShopFindings\ShopFindingsDetail;
use App\UtasCode;
use Tests\DuskTestCase;
use Laravel\Dusk\Browser;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class InputTest extends DuskTestCase
{
    /**
     * Test that the admin only inputs aren't interactable by normal user in HDR_Segment.
     *
     * @return void
     */
    public function testHDR_SegmentFrozenInputs()
    {
        $notification = Notification::where('plant_code', $this->user->location->plant_code)
            ->inRandomOrder()
            ->first();
        
        $this->browse(function (Browser $browser) use ($notification) {
            $browser->loginAs($this->user)
                    ->visit(route('header.edit', $notification->get_RCS_SFI()))
                    ->assertSeeIn('h1', 'Header');
                    
            $this->assertEquals('true', $browser->attribute('#ROC', 'readonly'));
            $this->assertEquals('true', $browser->attribute('#RON', 'readonly'));
        });
    }
    
    /**
     * Test that the admin only inputs aren't interactable by normal user in the RCS_Segment.
     *
     * @return void
     */
    public function testRCS_SegmentFrozenInputs()
    {
        $notification = Notification::where('plant_code', $this->user->location->plant_code)
            ->inRandomOrder()
            ->first();
        
        $this->browse(function (Browser $browser) use ($notification) {
            $browser->loginAs($this->user)
                    ->visit(route('received-lru.edit', $notification->get_RCS_SFI()))
                    ->assertSeeIn('h1', 'Received LRU');
                    
            $this->assertEquals('true', $browser->attribute('#MRD', 'readonly'));
            $this->assertEquals('true', $browser->attribute('#MPN', 'readonly'));
            $this->assertEquals('true', $browser->attribute('#SER', 'readonly'));
        });
    }
    
    /**
     * Test that the admin only inputs aren't interactable by normal user in the RCS_Segment.
     *
     * @return void
     */
    public function testSUS_SegmentFrozenInputs()
    {
        $notification = Notification::where('plant_code', $this->user->location->plant_code)
            ->inRandomOrder()
            ->first();
        
        $this->browse(function (Browser $browser) use ($notification) {
            $browser->loginAs($this->user)
                    ->visit(route('shipped-lru.edit', $notification->get_RCS_SFI()))
                    ->assertSeeIn('h1', 'Shipped LRU');
                    
            $this->assertEquals('true', $browser->attribute('#MPN', 'readonly'));
            $this->assertEquals('true', $browser->attribute('#SER', 'readonly'));
        });
    }
    
    /**
     * Test that the admin only inputs aren't interactable by normal user in the WPS_Segment.
     *
     * @return void
     */
    public function testWPS_SegmentFrozenInputs()
    {
        $notification = Notification::with('pieceParts')
            ->where('plant_code', $this->user->location->plant_code)
            ->whereHas('pieceParts')
            ->inRandomOrder()
            ->first();
        
        $this->browse(function (Browser $browser) use ($notification) {
            $browser->loginAs($this->user)
                    ->visit(route('worked-piece-part.edit', [$notification->PieceParts[0]->get_WPS_SFI(), $notification->PieceParts[0]->get_WPS_PPI()]))
                    ->assertSeeIn('h1', 'Worked Piece Part');
                    
            $this->assertEquals('true', $browser->attribute('#MPN', 'readonly'));
            $this->assertEquals('true', $browser->attribute('#PDT', 'readonly'));
        });
    }
    
    /**
     * Test that the admin only inputs aren't interactable by normal user in the RPS_Segment.
     *
     * @return void
     */
    public function testRPS_SegmentFrozenInputs()
    {
        $notification = Notification::with('pieceParts')
            ->where('plant_code', $this->user->location->plant_code)
            ->whereHas('pieceParts')
            ->inRandomOrder()
            ->first();
        
        $this->browse(function (Browser $browser) use ($notification) {
            $browser->loginAs($this->user)
                    ->visit(route('replaced-piece-part.edit', [$notification->PieceParts[0]->get_WPS_SFI(), $notification->PieceParts[0]->get_WPS_PPI()]))
                    ->assertSeeIn('h1', 'Replaced Piece Part');
                    
            $this->assertEquals('true', $browser->attribute('#MPN', 'readonly'));
        });
    }
    
    /**
     * Test that the default values are added where appropriate in the RPS_Segment.
     *
     * @return void
     */
    public function testSAS_SegmentDefaultInputValue()
    {
        $notification = Notification::where('plant_code', $this->user->location->plant_code)
            ->inRandomOrder()
            ->first();
        
        // Make sure no value is set.
        $notification->sasSHL = NULL;
        $notification->save();
        
        $this->browse(function (Browser $browser) use ($notification) {
            $browser->loginAs($this->user)
                    ->visit(route('shop-action-details.edit', $notification->get_RCS_SFI()))
                    ->assertSeeIn('h1', 'Shop Action Details')
                    ->assertSelected('#SHL', 'R2');
        });
        
        // Make sure default value can be overidden.
        $notification->sasSHL = 'R1';
        $notification->save();
        
        $this->browse(function (Browser $browser) use ($notification) {
            $browser->loginAs($this->user)
                    ->visit(route('shop-action-details.edit', $notification->get_RCS_SFI()))
                    ->assertSeeIn('h1', 'Shop Action Details')
                    ->assertSelected('#SHL', 'R1');
        });
    }
    
    /**
     * Test that the default values are added where appropriate in the WPS_Segment.
     *
     * @return void
     */
    public function testWPS_SegmentDefaultInputValue()
    {
        $notification = Notification::with('pieceParts')
            ->where('plant_code', $this->user->location->plant_code)
            ->whereHas('pieceParts')
            ->inRandomOrder()
            ->first();
            
        // Make sure no value is set.
        $notification->PieceParts[0]->wpsPFC = NULL;
        $notification->PieceParts[0]->save();
        
        $this->browse(function (Browser $browser) use ($notification) {
            $browser->loginAs($this->user)
                    ->visit(route('worked-piece-part.edit', [$notification->PieceParts[0]->get_WPS_SFI(), $notification->PieceParts[0]->get_WPS_PPI()]))
                    ->assertSeeIn('h1', 'Worked Piece Part')
                    ->assertSelected('#PFC', 'D');
        });
        
        // Make sure default value can be overidden.
        $notification->PieceParts[0]->wpsPFC = 'Y';
        $notification->PieceParts[0]->save();
        
        $this->browse(function (Browser $browser) use ($notification) {
            $browser->loginAs($this->user)
                    ->visit(route('worked-piece-part.edit', [$notification->PieceParts[0]->get_WPS_SFI(), $notification->PieceParts[0]->get_WPS_PPI()]))
                    ->assertSeeIn('h1', 'Worked Piece Part')
                    ->assertSelected('#PFC', 'Y');
        });
    }
    
    /**
     * Test that the default values are added where appropriate in the MISC_Segment.
     *
     * @return void
     */
    public function testMISC_SegmentDefaultInputValue()
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
        
        $this->browse(function (Browser $browser) use ($notification) {
            $browser->loginAs($this->dataAdminUser)
                    ->visit(route('misc-segment.edit', $notification->get_RCS_SFI()))
                    ->assertSeeIn('h1', 'Collins Fields')
                    ->assertInputValue('#Plant', $notification->plant_code);
        });
    }
    
    /**
     * A Dusk test example.
     *
     * @return void
     */
    public function testSAS_SegmentRadioInputs()
    {
        $utasParts = UtasCode::getAllUtasCodes();
        
        // Get a random non-Collins notification
        $notification = Notification::where('plant_code', $this->dataAdminUser->location->plant_code)
            ->inRandomOrder()
            ->first();
            
        $notification->sasRFI = NULL;
        $notification->sasSDI = NULL;
        $notification->save();
        
        $this->browse(function (Browser $browser) use ($notification) {
            $browser->loginAs($this->dataAdminUser)
                    ->visit(route('shop-action-details.edit', $notification->id))
                    ->assertSeeIn('h1', 'Shop Action Details')
                    ->check('#show_all_fields')
                    ->assertRadioNotSelected('RFI', '1')
                    ->assertRadioNotSelected('RFI', '0')
                    ->assertRadioNotSelected('SDI', '1')
                    ->assertRadioNotSelected('SDI', '0');
        });
        
        $notification->sasRFI = 0;
        $notification->sasSDI = 1;
        $notification->save();
        
        $this->browse(function (Browser $browser) use ($notification) {
            $browser->loginAs($this->dataAdminUser)
                    ->visit(route('shop-action-details.edit', $notification->id))
                    ->assertSeeIn('h1', 'Shop Action Details')
                    ->check('#show_all_fields')
                    ->assertRadioSelected('RFI', '0')
                    ->assertRadioSelected('SDI', '1');
        });
    }
}
