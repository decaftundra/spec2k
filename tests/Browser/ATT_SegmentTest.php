<?php

namespace Tests\Browser;

use App\Notification;
use App\ShopFindings\ATT_Segment;
use Tests\DuskTestCase;
use Laravel\Dusk\Browser;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class ATT_SegmentTest extends DuskTestCase
{
    /**
     * A Dusk test example.
     *
     * @return void
     */
    public function testUpdateATT_Segment()
    {
        $notification = Notification::where('plant_code', $this->dataAdminUser->location->plant_code)
            ->inRandomOrder()
            ->first();
        
        $ATT_Segment = factory(ATT_Segment::class)->make();
        
        $this->browse(function (Browser $browser) use ($notification, $ATT_Segment) {
            $browser->loginAs($this->dataAdminUser)
                    ->visit(route('accumulated-time-text.edit', $notification->id))
                    ->assertSeeIn('h1', 'Accumulated Time Text')
                    ->check('#show_all_segments')
                	->select('TRF', $ATT_Segment->get_ATT_TRF())
                    ->type('OTT', $ATT_Segment->get_ATT_OTT())
                    ->type('OPC', $ATT_Segment->get_ATT_OPC())
                    ->type('ODT', $ATT_Segment->get_ATT_ODT())
                    ->press('Save')
                    ->waitForText('Success')
                    ->assertSee('Accumulated Time Text saved successfully!')
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
