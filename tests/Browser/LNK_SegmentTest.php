<?php

namespace Tests\Browser;

use App\Notification;
use App\ShopFindings\LNK_Segment;
use Tests\DuskTestCase;
use Laravel\Dusk\Browser;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class LNK_SegmentTest extends DuskTestCase
{
    /**
     * A Dusk test example.
     *
     * @return void
     */
    public function testUpdateLNK_Segment()
    {
        $notification = Notification::where('plant_code', $this->dataAdminUser->location->plant_code)
            ->inRandomOrder()
            ->first();
        
        $LNK_Segment = factory(LNK_Segment::class)->make();
        
        $this->browse(function (Browser $browser) use ($notification, $LNK_Segment) {
            $browser->loginAs($this->dataAdminUser)
                    ->visit(route('linking-field.edit', $notification->id))
                    ->assertSeeIn('h1', 'Linking Field')
                    ->check('#show_all_segments')
                	->type('RTI', $LNK_Segment->get_LNK_RTI())
                    ->press('Save')
                    ->waitForText('Success')
                    ->assertSee('Linking Fields saved successfully!')
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
