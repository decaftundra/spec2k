<?php

namespace Tests\Browser;

use App\HDR_Segment;
use App\Notification;
use Tests\DuskTestCase;
use Laravel\Dusk\Browser;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class HDR_SegmentTest extends DuskTestCase
{
    /**
     * A Dusk test example.
     *
     * @return void
     */
    public function testUpdateHeader()
    {
        $notification = Notification::where('plant_code', $this->dataAdminUser->location->plant_code)
            ->inRandomOrder()
            ->first();
        
        $HDR_Segment = factory(HDR_Segment::class)->make();
        
        $this->browse(function (Browser $browser) use ($notification, $HDR_Segment) {
            $browser->loginAs($this->dataAdminUser)
                    ->visit(route('header.edit', $notification->get_RCS_SFI()))
                    ->assertSeeIn('h1', 'Header')
                    ->select('CHG', $HDR_Segment->get_HDR_CHG())
                    ->type('ROC', $HDR_Segment->get_HDR_ROC())
                    ->type('OPR', $HDR_Segment->get_HDR_OPR())
                    ->type('RON', $HDR_Segment->get_HDR_RON())
                    ->type('WHO', $HDR_Segment->get_HDR_WHO())
                	->press('Save')
                	->waitForText('Success')
                    ->assertSee('Header saved successfully!')
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
