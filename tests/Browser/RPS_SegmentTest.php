<?php

namespace Tests\Browser;

use App\Notification;
use App\PieceParts\RPS_Segment;
use Tests\DuskTestCase;
use Laravel\Dusk\Browser;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class RPS_SegmentTest extends DuskTestCase
{
    /**
     * A Dusk test example.
     *
     * @return void
     */
    public function testUpdateRPS_Segment()
    {
        $notification = Notification::with('pieceParts')
            ->where('plant_code', $this->dataAdminUser->location->plant_code)
            ->whereHas('pieceParts')
            ->inRandomOrder()
            ->first();
            
        $RPS_Segment = factory(RPS_Segment::class)->make();
        
        $this->browse(function (Browser $browser) use ($notification, $RPS_Segment) {
            $browser->loginAs($this->dataAdminUser)
                    ->visit(route('replaced-piece-part.edit', [$notification->PieceParts[0]->get_WPS_SFI(), $notification->PieceParts[0]->get_WPS_PPI()]))
                    ->assertSeeIn('h1', 'Replaced Piece Part')
                    ->check('#show_all_fields')
                    ->type('MPN', $RPS_Segment->get_RPS_MPN())
                    ->type('MFR', $RPS_Segment->get_RPS_MFR())
                    ->type('MFN', $RPS_Segment->get_RPS_MFN())
                    ->type('SER', $RPS_Segment->get_RPS_SER())
                    ->type('PNR', $RPS_Segment->get_RPS_PNR())
                    ->type('OPN', $RPS_Segment->get_RPS_OPN())
                    ->type('USN', $RPS_Segment->get_RPS_USN())
                    ->type('ASN', $RPS_Segment->get_RPS_ASN())
                    ->type('UCN', $RPS_Segment->get_RPS_UCN())
                    ->type('SPL', $RPS_Segment->get_RPS_SPL())
                    ->type('UST', $RPS_Segment->get_RPS_UST())
                    ->type('PDT', $RPS_Segment->get_RPS_PDT())
                	->press('Save')
                	->waitForText('Success')
                    ->assertSee('Replaced Piece Part saved successfully!')
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
