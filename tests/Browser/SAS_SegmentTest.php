<?php

namespace Tests\Browser;

use App\Notification;
use App\ShopFindings\SAS_Segment;
use App\UtasCode;
use Tests\DuskTestCase;
use Laravel\Dusk\Browser;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class SAS_SegmentTest extends DuskTestCase
{
    /**
     * A Dusk test example.
     *
     * @return void
     */
    public function testUpdateSAS_Segment()
    {
        $utasParts = UtasCode::getAllUtasCodes();
        
        // Get a random non-Collins notification
        $notification = Notification::where('plant_code', $this->dataAdminUser->location->plant_code)
            ->whereNotIn('rcsMPN', $utasParts)
            ->inRandomOrder()
            ->first();
        
        $SAS_Segment = factory(SAS_Segment::class)->make();
        
        $this->browse(function (Browser $browser) use ($notification, $SAS_Segment) {
            $browser->loginAs($this->dataAdminUser)
                    ->visit(route('shop-action-details.edit', $notification->id))
                    ->assertSeeIn('h1', 'Shop Action Details')
                    ->check('#show_all_fields')
                    ->type('INT', $SAS_Segment->get_SAS_INT())
                	->select('SHL', $SAS_Segment->get_SAS_SHL())
                	->radio('RFI', $SAS_Segment->get_SAS_RFI())
                	->type('MAT', $SAS_Segment->get_SAS_MAT())
                	->select('SAC', $SAS_Segment->get_SAS_SAC())
                	->select('PSC', $SAS_Segment->get_SAS_PSC())
                	->type('REM', $SAS_Segment->get_SAS_REM());
                    
            if (!is_null($SAS_Segment->get_SAS_SDI())) {
                $browser->radio('SDI', $SAS_Segment->get_SAS_SDI());
            }
            
            $browser->press('Save')
                    ->waitForText('Success')
                    ->assertSee('Shop Action Details saved successfully!')
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
     * A Dusk test example.
     *
     * @return void
     */
    public function testUpdateCollinsSAS_Segment()
    {
        $utasParts = UtasCode::getAllUtasCodes();
        
        // Get a random non-Collins notification
        $notification = Notification::where('plant_code', $this->dataAdminUser->location->plant_code)
            ->whereIn('rcsMPN', $utasParts)
            ->inRandomOrder()
            ->first();
        
        $SAS_Segment = factory(SAS_Segment::class)->make();
        
        $this->browse(function (Browser $browser) use ($notification, $SAS_Segment) {
            $browser->loginAs($this->dataAdminUser)
                    ->visit(route('shop-action-details.edit', $notification->id))
                    ->assertSeeIn('h1', 'Shop Action Details')
                    ->check('#show_all_fields')
                	->select('SHL', $SAS_Segment->get_SAS_SHL())
                	->radio('RFI', $SAS_Segment->get_SAS_RFI())
                	->type('MAT', $SAS_Segment->get_SAS_MAT())
                	->select('SAC', $SAS_Segment->get_SAS_SAC())
                	->select('PSC', $SAS_Segment->get_SAS_PSC())
                	->type('REM', $SAS_Segment->get_SAS_REM())
                	->assertMissing('#INT')
                	->assertSee('Collins Fields');
                    
            if (!is_null($SAS_Segment->get_SAS_SDI())) {
                $browser->radio('SDI', $SAS_Segment->get_SAS_SDI());
            }
            
            $browser->press('Save')
                    ->waitForText('Success')
                    ->assertSee('Shop Action Details saved successfully!')
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
