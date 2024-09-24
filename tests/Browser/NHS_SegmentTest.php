<?php

namespace Tests\Browser;

use App\Notification;
use App\PieceParts\NHS_Segment;
use Tests\DuskTestCase;
use Laravel\Dusk\Browser;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class NHS_SegmentTest extends DuskTestCase
{
    /**
     * A Dusk test example.
     *
     * @return void
     */
    public function testUpdateNHS_Segment()
    {
        $notification = Notification::with('pieceParts')
            ->where('plant_code', $this->dataAdminUser->location->plant_code)
            ->whereHas('pieceParts')
            ->inRandomOrder()
            ->first();
            
        $NHS_Segment = factory(NHS_Segment::class)->make();
        
        $this->browse(function (Browser $browser) use ($notification, $NHS_Segment) {
            $browser->loginAs($this->dataAdminUser)
                    ->visit(route('next-higher-assembly.edit', [$notification->PieceParts[0]->get_WPS_SFI(), $notification->PieceParts[0]->get_WPS_PPI()]))
                    ->assertSeeIn('h1', 'Next Higher Assembly')
                    ->check('#show_all_fields')
                    ->type('MFR', $NHS_Segment->get_NHS_MFR())
                    ->type('MPN', $NHS_Segment->get_NHS_MPN())
                    ->type('SER', $NHS_Segment->get_NHS_SER())
                    ->type('MFN', $NHS_Segment->get_NHS_MFN())
                    ->type('PNR', $NHS_Segment->get_NHS_PNR())
                    ->type('OPN', $NHS_Segment->get_NHS_OPN())
                    ->type('USN', $NHS_Segment->get_NHS_USN())
                    ->type('PDT', $NHS_Segment->get_NHS_PDT())
                    ->type('ASN', $NHS_Segment->get_NHS_ASN())
                    ->type('UCN', $NHS_Segment->get_NHS_UCN())
                    ->type('SPL', $NHS_Segment->get_NHS_SPL())
                    ->type('UST', $NHS_Segment->get_NHS_UST())
                    ->type('NPN', $NHS_Segment->get_NHS_NPN())
                	->press('Save')
                	->waitForText('Success')
                    ->assertSee('Next Higher Assembly saved successfully!')
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
