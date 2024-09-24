<?php

namespace Tests\Browser;

use App\Notification;
use App\ShopFindings\API_Segment;
use Tests\DuskTestCase;
use Laravel\Dusk\Browser;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class API_SegmentTest extends DuskTestCase
{
    /**
     * A Dusk test example.
     *
     * @return void
     */
    public function testUpdateAPI_Segment()
    {
        $notification = Notification::where('plant_code', $this->dataAdminUser->location->plant_code)
            ->inRandomOrder()
            ->first();
        
        $API_Segment = factory(API_Segment::class)->make();
        
        $this->browse(function (Browser $browser) use ($notification, $API_Segment) {
            $browser->loginAs($this->dataAdminUser)
                    ->visit(route('apu-information.edit', $notification->get_RCS_SFI()))
                    ->assertSeeIn('h1', 'APU Information')
                    ->check('#show_all_fields')
                    ->check('#show_all_segments')
                	->type('AET', $API_Segment->get_API_AET())
                	->type('EMS', $API_Segment->get_API_EMS())
                	->type('AEM', $API_Segment->get_API_AEM())
                	->type('MFR', $API_Segment->get_API_MFR())
                	->type('ATH', $API_Segment->get_API_ATH())
                	->type('ATC', $API_Segment->get_API_ATC())
                    ->press('Save')
                    ->waitForText('Success')
                    ->assertSee('APU Information saved successfully!')
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
