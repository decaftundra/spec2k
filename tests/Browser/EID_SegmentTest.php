<?php

namespace Tests\Browser;

use App\Notification;
use App\ShopFindings\EID_Segment;
use Tests\DuskTestCase;
use Laravel\Dusk\Browser;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class EID_SegmentTest extends DuskTestCase
{
    /**
     * A Dusk test example.
     *
     * @return void
     */
    public function testUpdateEID_Segment()
    {
        $notification = Notification::where('plant_code', $this->dataAdminUser->location->plant_code)
            ->inRandomOrder()
            ->first();
        
        $EID_Segment = factory(EID_Segment::class)->make();
        
        $this->browse(function (Browser $browser) use ($notification, $EID_Segment) {
            $browser->loginAs($this->dataAdminUser)
                    ->visit(route('engine-information.edit', $notification->get_RCS_SFI()))
                    ->assertSeeIn('h1', 'Engine Information')
                    ->check('#show_all_fields')
                	->type('AET', $EID_Segment->get_EID_AET())
                	->select('EPC', $EID_Segment->get_EID_EPC())
                	->type('AEM', $EID_Segment->get_EID_AEM())
                	->type('EMS', $EID_Segment->get_EID_EMS())
                	->type('MFR', $EID_Segment->get_EID_MFR())
                	->type('ETH', $EID_Segment->get_EID_ETH())
                	->type('ETC', $EID_Segment->get_EID_ETC())
                    ->press('Save')
                    ->waitForText('Success')
                    ->assertSee('Engine Information saved successfully!')
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
