<?php

namespace Tests\Browser;

use App\Notification;
use App\ShopFindings\AID_Segment;
use Tests\DuskTestCase;
use Laravel\Dusk\Browser;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class AID_SegmentTest extends DuskTestCase
{
    /**
     * A Dusk test example.
     *
     * @return void
     */
    public function testUpdateAID_Segment()
    {
        $notification = Notification::where('plant_code', $this->dataAdminUser->location->plant_code)
            ->inRandomOrder()
            ->first();
        
        $AID_Segment = factory(AID_Segment::class)->make();
        
        $this->browse(function (Browser $browser) use ($notification, $AID_Segment) {
            $browser->loginAs($this->dataAdminUser)
                    ->visit(route('airframe-information.edit', $notification->get_RCS_SFI()))
                    ->assertSeeIn('h1', 'Airframe Information')
                    ->check('#show_all_fields')
                    ->type('MFR', $AID_Segment->get_AID_MFR())
                	->type('AMC', $AID_Segment->get_AID_AMC())
                	->type('MFN', $AID_Segment->get_AID_MFN())
                	->type('ASE', $AID_Segment->get_AID_ASE())
                	->type('AIN', $AID_Segment->get_AID_AIN())
                	->type('REG', $AID_Segment->get_AID_REG())
                	->type('OIN', $AID_Segment->get_AID_OIN())
                	->type('CTH', $AID_Segment->get_AID_CTH())
                	->type('CTY', $AID_Segment->get_AID_CTY())
                    ->press('Save')
                    ->waitForText('Success')
                    ->assertSee('Airframe Information saved successfully!')
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
