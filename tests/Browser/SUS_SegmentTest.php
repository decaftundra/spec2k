<?php

namespace Tests\Browser;

use App\Notification;
use App\ShopFindings\SUS_Segment;
use Tests\DuskTestCase;
use Laravel\Dusk\Browser;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class SUS_SegmentTest extends DuskTestCase
{
    /**
     * A Dusk test example.
     *
     * @return void
     */
    public function testUpdateSUS_Segment()
    {
        $notification = Notification::where('plant_code', $this->dataAdminUser->location->plant_code)
            ->inRandomOrder()
            ->first();
        
        $SUS_Segment = factory(SUS_Segment::class)->make();
        
        $this->browse(function (Browser $browser) use ($notification, $SUS_Segment) {
            $browser->loginAs($this->dataAdminUser)
                    ->visit(route('shipped-lru.edit', $notification->id))
                    ->assertSeeIn('h1', 'Shipped LRU')
                    ->check('#show_all_fields')
                    ->check('#show_all_segments')
                	->type('SHD', $SUS_Segment->get_SUS_SHD())
                    ->type('MFR', $SUS_Segment->get_SUS_MFR())
                    ->type('MPN', $SUS_Segment->get_SUS_MPN())
                    ->type('SER', $SUS_Segment->get_SUS_SER())
                    ->type('MFN', $SUS_Segment->get_SUS_MFN())
                    ->type('PDT', $SUS_Segment->get_SUS_PDT())
                    ->type('PNR', $SUS_Segment->get_SUS_PNR())
                    ->type('OPN', $SUS_Segment->get_SUS_OPN())
                    ->type('USN', $SUS_Segment->get_SUS_USN())
                    ->type('ASN', $SUS_Segment->get_SUS_ASN())
                    ->type('UCN', $SUS_Segment->get_SUS_UCN())
                    ->type('SPL', $SUS_Segment->get_SUS_SPL())
                    ->type('UST', $SUS_Segment->get_SUS_UST())
                    ->type('PML', $SUS_Segment->get_SUS_PML())
                    ->type('PSC', $SUS_Segment->get_SUS_PSC())
                    ->press('Save')
                    ->waitForText('Success')
                    ->assertSee('Shipped LRU saved successfully!')
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
