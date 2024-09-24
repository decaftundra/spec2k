<?php

namespace Tests\Browser;

use App\Notification;
use App\ShopFindings\SPT_Segment;
use Tests\DuskTestCase;
use Laravel\Dusk\Browser;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class SPT_SegmentTest extends DuskTestCase
{
    /**
     * A Dusk test example.
     *
     * @return void
     */
    public function testUpdateSPT_Segment()
    {
        $notification = Notification::where('plant_code', $this->dataAdminUser->location->plant_code)
            ->inRandomOrder()
            ->first();
        
        $SPT_Segment = factory(SPT_Segment::class)->make();
        
        $this->browse(function (Browser $browser) use ($notification, $SPT_Segment) {
            $browser->loginAs($this->dataAdminUser)
                    ->visit(route('shop-processing-time.edit', $notification->id))
                    ->assertSeeIn('h1', 'Shop Processing Time')
                    ->check('#show_all_segments')
                    ->type('MAH', $SPT_Segment->get_SPT_MAH())
                	->type('FLW', $SPT_Segment->get_SPT_FLW())
                	->type('MST', $SPT_Segment->get_SPT_MST())
                    ->press('Save')
                    ->waitForText('Success')
                    ->assertSee('Shop Processing Time saved successfully!')
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
