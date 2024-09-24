<?php

namespace Tests\Browser;

use App\Notification;
use App\PieceParts\WPS_Segment;
use Tests\DuskTestCase;
use Laravel\Dusk\Browser;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class WPS_SegmentTest extends DuskTestCase
{
    /**
     * A Dusk test example.
     *
     * @return void
     */
    public function testUpdateWPS_Segment()
    {
        $notification = Notification::with('pieceParts')
            ->where('plant_code', $this->dataAdminUser->location->plant_code)
            ->whereHas('pieceParts')
            ->inRandomOrder()
            ->first();
            
        $WPS_Segment = factory(WPS_Segment::class)->make();
        
        $this->browse(function (Browser $browser) use ($notification, $WPS_Segment) {
            $browser->loginAs($this->dataAdminUser)
                    ->visit(route('worked-piece-part.edit', [$notification->PieceParts[0]->get_WPS_SFI(), $notification->PieceParts[0]->get_WPS_PPI()]))
                    ->assertSeeIn('h1', 'Worked Piece Part')
                    ->check('#show_all_fields')
                    ->select('PFC', $WPS_Segment->get_WPS_PFC())
                    ->type('MFR', $WPS_Segment->get_WPS_MFR())
                    ->type('MFN', $WPS_Segment->get_WPS_MFN())
                    ->type('MPN', $WPS_Segment->get_WPS_MPN())
                    ->type('SER', $WPS_Segment->get_WPS_SER())
                    ->type('FDE', $WPS_Segment->get_WPS_FDE())
                    ->type('PNR', $WPS_Segment->get_WPS_PNR())
                    ->type('OPN', $WPS_Segment->get_WPS_OPN())
                    ->type('USN', $WPS_Segment->get_WPS_USN())
                    ->type('PDT', $WPS_Segment->get_WPS_PDT())
                    ->type('GEL', $WPS_Segment->get_WPS_GEL())
                    ->type('MRD', $WPS_Segment->get_WPS_MRD())
                    ->type('ASN', $WPS_Segment->get_WPS_ASN())
                    ->type('UCN', $WPS_Segment->get_WPS_UCN())
                    ->type('SPL', $WPS_Segment->get_WPS_SPL())
                    ->type('UST', $WPS_Segment->get_WPS_UST())
                	->press('Save')
                	->waitForText('Success')
                    ->assertSee('Worked Piece Part saved successfully!')
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
