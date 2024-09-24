<?php

namespace Tests\Browser;

use App\Notification;
use App\ShopFindings\RCS_Segment;
use App\UtasCode;
use Tests\DuskTestCase;
use Laravel\Dusk\Browser;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class RCS_SegmentTest extends DuskTestCase
{
    /**
     * Test the RCS Segment saves OK.
     *
     * @return void
     */
    public function testUpdateRCS_Segment()
    {
        $utasParts = UtasCode::getAllUtasCodes();
        
        // Get a random non-Collins notification
        $notification = Notification::where('plant_code', $this->dataAdminUser->location->plant_code)
            ->whereNotIn('rcsMPN', $utasParts)
            ->inRandomOrder()
            ->first();
        
        $RCS_Segment = factory(RCS_Segment::class)->make();
        
        $this->browse(function (Browser $browser) use ($notification, $RCS_Segment) {
            $browser->loginAs($this->dataAdminUser)
                    ->visit(route('received-lru.edit', $notification->id))
                    ->assertSeeIn('h1', 'Received LRU')
                    ->check('#show_all_fields')
                    ->type('MRD', $RCS_Segment->get_RCS_MRD())
                	->type('MFR', $RCS_Segment->get_RCS_MFR())
                	->type('MPN', $RCS_Segment->get_RCS_MPN())
                	->type('SER', $RCS_Segment->get_RCS_SER())
                	->select('RRC', $RCS_Segment->get_RCS_RRC())
                	->select('FFC', $RCS_Segment->get_RCS_FFC())
                	->select('FFI', $RCS_Segment->get_RCS_FFI())
                	->select('FCR', $RCS_Segment->get_RCS_FCR())
                	->select('FAC', $RCS_Segment->get_RCS_FAC())
                	->select('FBC', $RCS_Segment->get_RCS_FBC())
                	->select('FHS', $RCS_Segment->get_RCS_FHS())
                	->type('MFN', $RCS_Segment->get_RCS_MFN())
                	->type('PNR', $RCS_Segment->get_RCS_PNR())
                	->type('OPN', $RCS_Segment->get_RCS_OPN())
                	->type('USN', $RCS_Segment->get_RCS_USN())
                	->type('RET', $RCS_Segment->get_RCS_RET())
                	->type('CIC', $RCS_Segment->get_RCS_CIC())
                	->type('CPO', $RCS_Segment->get_RCS_CPO())
                	->type('PSN', $RCS_Segment->get_RCS_PSN())
                	->type('WON', $RCS_Segment->get_RCS_WON())
                	->type('MRN', $RCS_Segment->get_RCS_MRN())
                	->type('CTN', $RCS_Segment->get_RCS_CTN())
                	->type('BOX', $RCS_Segment->get_RCS_BOX())
                	->type('ASN', $RCS_Segment->get_RCS_ASN())
                	->type('UCN', $RCS_Segment->get_RCS_UCN())
                	->type('SPL', $RCS_Segment->get_RCS_SPL())
                	->type('UST', $RCS_Segment->get_RCS_UST())
                	->type('PDT', $RCS_Segment->get_RCS_PDT())
                	->type('PML', $RCS_Segment->get_RCS_PML())
                	->type('SFC', $RCS_Segment->get_RCS_SFC())
                	->type('RSI', $RCS_Segment->get_RCS_RSI())
                	->type('RLN', $RCS_Segment->get_RCS_RLN())
                	->type('INT', $RCS_Segment->get_RCS_INT())
                	->type('REM', $RCS_Segment->get_RCS_REM())
                	->press('Save')
                    ->assertSee('Received LRU saved successfully!')
                    ->pause(2000)
                    ->press('Delete')
                    ->assertSee('Are you sure?')
                    ->pause(2000)
                    ->press('.confirm')
                    ->waitForText('Success')
                    ->assertSee('Segment deleted successfully!');
        });
    }
    
    /**
     * Test the Collins RCS Segment saves OK.
     *
     * @return void
     */
    public function testUpdateCollinsRCS_Segment()
    {
        $utasParts = UtasCode::getAllUtasCodes();
        
        $notification = Notification::whereIn('rcsMPN', $utasParts)
            ->inRandomOrder()
            ->first();
        
        $RCS_Segment = factory(RCS_Segment::class)->make();
        
        $this->browse(function (Browser $browser) use ($notification, $RCS_Segment) {
            $browser->loginAs($this->dataAdminUser)
                    ->visit(route('received-lru.edit', $notification->id))
                    ->assertSeeIn('h1', 'Received LRU')
                    ->check('#show_all_fields')
                    ->type('MRD', $RCS_Segment->get_RCS_MRD())
                	->type('MFR', $RCS_Segment->get_RCS_MFR())
                	->type('MPN', $RCS_Segment->get_RCS_MPN())
                	->type('SER', $RCS_Segment->get_RCS_SER())
                	->select('RRC', $RCS_Segment->get_RCS_RRC())
                	->select('FFC', $RCS_Segment->get_RCS_FFC())
                	->select('FFI', $RCS_Segment->get_RCS_FFI())
                	->select('FCR', $RCS_Segment->get_RCS_FCR())
                	->select('FAC', $RCS_Segment->get_RCS_FAC())
                	->select('FBC', $RCS_Segment->get_RCS_FBC())
                	->select('FHS', $RCS_Segment->get_RCS_FHS())
                	->type('MFN', $RCS_Segment->get_RCS_MFN())
                	->type('PNR', $RCS_Segment->get_RCS_PNR())
                	->type('OPN', $RCS_Segment->get_RCS_OPN())
                	->type('USN', $RCS_Segment->get_RCS_USN())
                	->type('RET', $RCS_Segment->get_RCS_RET())
                	->type('CIC', $RCS_Segment->get_RCS_CIC())
                	->type('CPO', $RCS_Segment->get_RCS_CPO())
                	->type('PSN', $RCS_Segment->get_RCS_PSN())
                	->type('WON', $RCS_Segment->get_RCS_WON())
                	->type('MRN', $RCS_Segment->get_RCS_MRN())
                	->type('CTN', $RCS_Segment->get_RCS_CTN())
                	->type('BOX', $RCS_Segment->get_RCS_BOX())
                	->type('ASN', $RCS_Segment->get_RCS_ASN())
                	->type('UCN', $RCS_Segment->get_RCS_UCN())
                	->type('SPL', $RCS_Segment->get_RCS_SPL())
                	->type('UST', $RCS_Segment->get_RCS_UST())
                	->type('PDT', $RCS_Segment->get_RCS_PDT())
                	->type('PML', $RCS_Segment->get_RCS_PML())
                	->type('SFC', $RCS_Segment->get_RCS_SFC())
                	->type('RSI', $RCS_Segment->get_RCS_RSI())
                	->type('RLN', $RCS_Segment->get_RCS_RLN())
                	->type('INT', $RCS_Segment->get_RCS_INT())
                	->assertMissing('#REM')
                	->assertSee('Collins Fields')
                	->press('Save')
                	->waitForText('Success')
                    ->assertSee('Received LRU saved successfully!')
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
