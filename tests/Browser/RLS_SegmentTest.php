<?php

namespace Tests\Browser;

use App\Notification;
use App\ShopFindings\RLS_Segment;
use Tests\DuskTestCase;
use Laravel\Dusk\Browser;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class RLS_SegmentTest extends DuskTestCase
{
    /**
     * A Dusk test example.
     *
     * @return void
     */
    public function testUpdateRLS_Segment()
    {
        $notification = Notification::where('plant_code', $this->dataAdminUser->location->plant_code)
            ->inRandomOrder()
            ->first();
        
        $RLS_Segment = factory(RLS_Segment::class)->make();
        
        $this->browse(function (Browser $browser) use ($notification, $RLS_Segment) {
            $browser->loginAs($this->dataAdminUser)
                    ->visit(route('removed-lru.edit', $notification->id))
                    ->assertSeeIn('h1', 'Removed LRU')
                    ->check('#show_all_fields')
                    ->check('#show_all_segments')
                    ->type('MFR', $RLS_Segment->get_RLS_MFR())
                	->type('MPN', $RLS_Segment->get_RLS_MPN())
                	->type('SER', $RLS_Segment->get_RLS_SER())
                	->type('RED', $RLS_Segment->get_RLS_RED())
                	->select('TTY', $RLS_Segment->get_RLS_TTY())
                	->type('RET', $RLS_Segment->get_RLS_RET())
                	->type('DOI', $RLS_Segment->get_RLS_DOI())
                	->type('MFN', $RLS_Segment->get_RLS_MFN())
                	->type('PNR', $RLS_Segment->get_RLS_PNR())
                	->type('OPN', $RLS_Segment->get_RLS_OPN())
                	->type('USN', $RLS_Segment->get_RLS_USN())
                	->type('RMT', $RLS_Segment->get_RLS_RMT())
                	->type('APT', $RLS_Segment->get_RLS_APT())
                	->type('CPI', $RLS_Segment->get_RLS_CPI())
                	->type('CPT', $RLS_Segment->get_RLS_CPT())
                	->type('PDT', $RLS_Segment->get_RLS_PDT())
                	->type('PML', $RLS_Segment->get_RLS_PML())
                	->type('ASN', $RLS_Segment->get_RLS_ASN())
                	->type('UCN', $RLS_Segment->get_RLS_UCN())
                	->type('SPL', $RLS_Segment->get_RLS_SPL())
                	->type('UST', $RLS_Segment->get_RLS_UST())
                	->select('RFR', $RLS_Segment->get_RLS_RFR())
                    ->press('Save')
                    ->waitForText('Success')
                    ->assertSee('Removed LRU saved successfully!')
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
