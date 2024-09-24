<?php

namespace Tests\Browser;

use App\Codes\ActionCode;
use App\Notification;
use Tests\DuskTestCase;
use Laravel\Dusk\Browser;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class AutoCompleteTest extends DuskTestCase
{
    /**
     * Test the autocomplete functionality in the HDR_Segment.
     *
     * @return void
     */
    public function testHDR_SegmentAutocomplete()
    {
        $notification = Notification::where('plant_code', $this->dataAdminUser->location->plant_code)
            ->inRandomOrder()
            ->first();
        
        $this->browse(function (Browser $browser) use ($notification) {
            $browser->loginAs($this->dataAdminUser)
                    ->visit(route('header.edit', $notification->get_RCS_SFI()))
                    ->pause(1000)
                    ->press('#cust-reset')
                    ->type('WHO', 'roll')
                    ->pause(2000)
                    ->assertSeeIn('.ui-menu-item-wrapper', 'Rolls Royce')
                    ->click('.ui-menu-item-wrapper')
                    ->pause(1000)
                    ->assertInputValue('OPR', 'ZZZZZ')
                    ->visit(route('notifications.index'))->waitForDialog(5)->acceptDialog(); // Navigate away from page and close unsaved warning alert.
        });
    }
    
    /**
     * Test the autocomplete functionality in the RCS_Segment.
     *
     * @return void
     */
    public function testRCS_SegmentAutocomplete()
    {
        $notification = Notification::where('plant_code', $this->dataAdminUser->location->plant_code)
            ->inRandomOrder()
            ->first();
        
        $this->browse(function (Browser $browser) use ($notification) {
            $browser->loginAs($this->dataAdminUser)
                    ->visit(route('received-lru.edit', $notification->id))
                    ->pause(1000)
                    ->press('#reset')
                    ->pause(1000)
                    ->assertNotSelected('RRC','U')
                    ->assertNotSelected('RRC','S')
                    ->assertNotSelected('RRC','M')
                    ->assertNotSelected('RRC','P')
                    ->assertNotSelected('RRC','O')
                    
                	->assertNotSelected('FFC', 'NT')
                	->assertNotSelected('FFC', 'FT')
                	->assertNotSelected('FFC', 'NA')
                	
                    ->assertNotSelected('FFI', 'NA')
                    ->assertNotSelected('FFI', 'NI')
                    ->assertNotSelected('FFI', 'IN')
                    
                	->assertNotSelected('FCR', 'NA')
                	->assertNotSelected('FCR', 'CR')
                	->assertNotSelected('FCR', 'NC')
                	
                	->assertNotSelected('FAC', 'NA')
                	->assertNotSelected('FAC', 'CM')
                	->assertNotSelected('FAC', 'NM')
                	
                	->assertNotSelected('FBC', 'NA')
                	->assertNotSelected('FBC', 'CB')
                	->assertNotSelected('FBC', 'NB')
                	
                	->assertNotSelected('FHS', 'NA')
                	->assertNotSelected('FHS', 'HW')
                	->assertNotSelected('FHS', 'SW')
                	
                	->select('RRC', 'U')
                	->pause(1000)
                	->select('FFC', 'NT')
                	->pause(1000)
                	->assertSelected('FFI', 'NA')
                	->assertSelected('FCR', 'NA')
                	->assertSelected('FAC', 'NA')
                	->assertSelected('FBC', 'NA')
                	->assertSelected('FHS', 'NA')
                	->visit(route('notifications.index'))->waitForDialog(5)->acceptDialog(); // Navigate away from page and close unsaved warning alert.
        });
    }
    
    /**
     * A Dusk test example.
     *
     * @return void
     */
    public function testAID_SegmentAutocomplete()
    {
        $notification = Notification::where('plant_code', $this->dataAdminUser->location->plant_code)
            ->inRandomOrder()
            ->first();
        
        $this->browse(function (Browser $browser) use ($notification) {
            $browser->loginAs($this->dataAdminUser)
                    ->visit(route('airframe-information.edit', $notification->get_RCS_SFI()))
                    ->check('#show_all_fields')
                    ->pause(3000)
                    ->press('#reset')
                    ->type('REG', 1003)
                    //->pause(1000)
                    ->waitFor('.ui-menu-item .ui-menu-item-wrapper')
                    ->waitForText('10035')
                    ->assertSeeIn('.ui-menu-item:nth-child(2) .ui-menu-item-wrapper', '10035')
                    ->click('.ui-menu-item:nth-child(2) .ui-menu-item-wrapper')
                    ->pause(1000)
                    ->assertInputValue('AIN', '421B-0882')
                    ->assertInputValue('AMC', 'Cessna 421')
                    ->assertInputValue('ASE', '421 (P)')
                    ->assertInputValue('MFN', 'Textron Aviation (Cessna)')
                    ->assertInputValue('MFR', '7EK50')
                    ->visit(route('notifications.index'))->waitForDialog(5)->acceptDialog(); // Navigate away from page and close unsaved warning alert.
        });
    }
    
    /**
     * A Dusk test example.
     *
     * @return void
     */
    public function testSAS_SegmentAutocomplete()
    {
        $notification = Notification::where('plant_code', $this->dataAdminUser->location->plant_code)
            ->inRandomOrder()
            ->first();
            
        $yesOptions = ActionCode::where('RFI', 1)->orWhereNULL('RFI')->pluck('SAC')->toArray();
        $noOptions = ActionCode::where('RFI', 0)->orWhereNULL('RFI')->pluck('SAC')->toArray();
        
        $this->browse(function (Browser $browser) use ($notification, $yesOptions, $noOptions) {
            
            $allSACOptions = ['IRTR', 'SCRP', 'XCHG', 'RPLC', 'RTAS', 'RCRT', 'REPR', 'BERP', 'CLBN', 'MODN', 'OVHL', 'REFN', 'RLSW', 'ROMP', 'RPCK', 'RWRK', 'SADJ', 'SLRN', 'SPAG', 'TEST', 'UNRP'];
            
            $browser->loginAs($this->dataAdminUser)
                    ->visit(route('shop-action-details.edit', $notification->id))
                    ->assertSeeIn('h1', 'Shop Action Details')
                    ->pause(1000)
                    ->press('#action-reset')
                    ->assertSelectMissingOptions('SAC', $allSACOptions)
                    ->radio('RFI', '1')
                    ->pause(1000)
                    ->assertSelectHasOptions('SAC', $yesOptions)
                    ->radio('RFI', '0')
                    ->pause(1000)
                    ->assertSelectHasOptions('SAC', $noOptions)
                    ->visit(route('notifications.index'))->waitForDialog(5)->acceptDialog(); // Navigate away from page and close unsaved warning alert.
        });
    }
}
