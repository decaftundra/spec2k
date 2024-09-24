<?php

namespace Tests\Feature;

use Carbon\Carbon;
use Tests\TestCase;
use App\ShopFindings\RLS_Segment;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class RLS_SegmentTest extends TestCase
{
    /**
     * Test the Received LRU form response is 200.
     *
     * @return void
     */
    public function testRLS_SegmentForm()
    {
        $notification = $this->getEditableNotification($this->user);
        
        $this->actingAs($this->user)->call('GET', route('removed-lru.edit', $notification->get_RCS_SFI()))->assertStatus(200);
    }
    
    /**
     * Test user can't edit or update a segment from another location.
     *
     * @return void
     */
    public function testUserCantEditOrUpdateRLS_SegmentForOtherLocation()
    {
        $notification = $this->getNotificationNotInUsersLocation($this->user);
        
        $this->actingAs($this->user)
            ->call('GET', route('removed-lru.edit', $notification->get_RCS_SFI()))
            ->assertStatus(403);
            
        $RLS_Segment = factory(RLS_Segment::class)->make();
        
        $attributes = [
            'rcsSFI' => $notification->get_RCS_SFI(),
            'plant_code' => $notification->plant_code,
            'MFR' => $RLS_Segment->get_RLS_MFR(),
        	'MPN' => $RLS_Segment->get_RLS_MPN(),
        	'SER' => $RLS_Segment->get_RLS_SER(),
        	'RED' => $RLS_Segment->get_RLS_RED(),
        	'TTY' => $RLS_Segment->get_RLS_TTY(),
        	'RET' => $RLS_Segment->get_RLS_RET(),
        	'DOI' => $RLS_Segment->get_RLS_DOI(),
        	'MFN' => $RLS_Segment->get_RLS_MFN(),
        	'PNR' => $RLS_Segment->get_RLS_PNR(),
        	'OPN' => $RLS_Segment->get_RLS_OPN(),
        	'USN' => $RLS_Segment->get_RLS_USN(),
        	'RMT' => $RLS_Segment->get_RLS_RMT(),
        	'APT' => $RLS_Segment->get_RLS_APT(),
        	'CPI' => $RLS_Segment->get_RLS_CPI(),
        	'CPT' => $RLS_Segment->get_RLS_CPT(),
        	'PDT' => $RLS_Segment->get_RLS_PDT(),
        	'PML' => $RLS_Segment->get_RLS_PML(),
        	'ASN' => $RLS_Segment->get_RLS_ASN(),
        	'UCN' => $RLS_Segment->get_RLS_UCN(),
        	'SPL' => $RLS_Segment->get_RLS_SPL(),
        	'UST' => $RLS_Segment->get_RLS_UST(),
        	'RFR' => $RLS_Segment->get_RLS_RFR(),
        ];
        
        $response = $this->actingAs($this->user)
            ->call('POST', route('removed-lru.update', $notification->get_RCS_SFI()), $attributes)
            ->assertStatus(403);
    }
    
    /**
     * Test that admin can edit and update segment from another location.
     *
     * @return void
     */
    public function testAdminCanEditAndUpdateRLS_SegmentForOtherLocation()
    {
        $notification = $this->getNotificationNotInUsersLocation($this->adminUser);
        
        $this->actingAs($this->adminUser)
            ->call('GET', route('removed-lru.edit', $notification->get_RCS_SFI()))
            ->assertStatus(200);
        
        $RLS_Segment = factory(RLS_Segment::class)->make();
        
        $attributes = [
            'rcsSFI' => $notification->get_RCS_SFI(),
            'plant_code' => $notification->plant_code,
            'MFR' => $RLS_Segment->get_RLS_MFR(),
        	'MPN' => $RLS_Segment->get_RLS_MPN(),
        	'SER' => $RLS_Segment->get_RLS_SER(),
        	'RED' => $RLS_Segment->get_RLS_RED(),
        	'TTY' => $RLS_Segment->get_RLS_TTY(),
        	'RET' => $RLS_Segment->get_RLS_RET(),
        	'DOI' => $RLS_Segment->get_RLS_DOI(),
        	'MFN' => $RLS_Segment->get_RLS_MFN(),
        	'PNR' => $RLS_Segment->get_RLS_PNR(),
        	'OPN' => $RLS_Segment->get_RLS_OPN(),
        	'USN' => $RLS_Segment->get_RLS_USN(),
        	'RMT' => $RLS_Segment->get_RLS_RMT(),
        	'APT' => $RLS_Segment->get_RLS_APT(),
        	'CPI' => $RLS_Segment->get_RLS_CPI(),
        	'CPT' => $RLS_Segment->get_RLS_CPT(),
        	'PDT' => $RLS_Segment->get_RLS_PDT(),
        	'PML' => $RLS_Segment->get_RLS_PML(),
        	'ASN' => $RLS_Segment->get_RLS_ASN(),
        	'UCN' => $RLS_Segment->get_RLS_UCN(),
        	'SPL' => $RLS_Segment->get_RLS_SPL(),
        	'UST' => $RLS_Segment->get_RLS_UST(),
        	'RFR' => $RLS_Segment->get_RLS_RFR(),
        ];
        
        $response = $this->actingAs($this->adminUser)
            ->call('POST', route('removed-lru.update', $notification->get_RCS_SFI()), $attributes)
            ->assertStatus(302);
        
        $this->get($response->headers->get('Location'))
            ->assertSee('Removed LRU saved successfully!');
        
        unset($attributes['rcsSFI']); // Not recorded in DB.
        unset($attributes['RED']);
        unset($attributes['DOI']);
        unset($attributes['plant_code']);
        
        $this->assertDatabaseHas('RLS_Segments', $attributes);
    }
    
    /**
     * Assert the session has errors if an empty form is submitted.
     *
     * @return void
     */
    public function testInvalidRLSFormSubmit()
    {
        $notification = $this->getEditableNotification($this->user);
        
        $attributes = [
            'rcsSFI' => $notification->get_RCS_SFI(),
            'plant_code' => $notification->plant_code,
        ];
        
        $response = $this->actingAs($this->user)
            ->call('POST', route('removed-lru.update', $notification->get_RCS_SFI()), $attributes);
        
        $response->assertSessionHasErrors(['MFR','MPN','SER']);
    }
    
    /**
     * Test the Received LRU form request validates.
     *
     * @return void
     */
    public function testEditRLS_Segment()
    {
        $notification = $this->getEditableNotification($this->user);
        
        $RLS_Segment = factory(RLS_Segment::class)->make();
        
        $attributes = [
            'rcsSFI' => $notification->get_RCS_SFI(),
            'plant_code' => $notification->plant_code,
            'MFR' => $RLS_Segment->get_RLS_MFR(),
        	'MPN' => $RLS_Segment->get_RLS_MPN(),
        	'SER' => $RLS_Segment->get_RLS_SER(),
        	'RED' => $RLS_Segment->get_RLS_RED(),
        	'TTY' => $RLS_Segment->get_RLS_TTY(),
        	'RET' => $RLS_Segment->get_RLS_RET(),
        	'DOI' => $RLS_Segment->get_RLS_DOI(),
        	'MFN' => $RLS_Segment->get_RLS_MFN(),
        	'PNR' => $RLS_Segment->get_RLS_PNR(),
        	'OPN' => $RLS_Segment->get_RLS_OPN(),
        	'USN' => $RLS_Segment->get_RLS_USN(),
        	'RMT' => $RLS_Segment->get_RLS_RMT(),
        	'APT' => $RLS_Segment->get_RLS_APT(),
        	'CPI' => $RLS_Segment->get_RLS_CPI(),
        	'CPT' => $RLS_Segment->get_RLS_CPT(),
        	'PDT' => $RLS_Segment->get_RLS_PDT(),
        	'PML' => $RLS_Segment->get_RLS_PML(),
        	'ASN' => $RLS_Segment->get_RLS_ASN(),
        	'UCN' => $RLS_Segment->get_RLS_UCN(),
        	'SPL' => $RLS_Segment->get_RLS_SPL(),
        	'UST' => $RLS_Segment->get_RLS_UST(),
        	'RFR' => $RLS_Segment->get_RLS_RFR(),
        ];
        
        $response = $this->actingAs($this->user)
            ->call('POST', route('removed-lru.update', $notification->get_RCS_SFI()), $attributes)
            ->assertStatus(302);
        
        $this->get($response->headers->get('Location'))
            ->assertSee('Removed LRU saved successfully!');
        
        unset($attributes['rcsSFI']); // Not recorded in DB.
        unset($attributes['RED']);
        unset($attributes['DOI']);
        unset($attributes['plant_code']);
        
        $this->assertDatabaseHas('RLS_Segments', $attributes);
    }
    
    /**
     * Test that the segment can be deleted.
     *
     * @return void
     */
    public function testDeleteRLS_Segment()
    {
        $this->createSingleShopFindingAndPiecePartsWithAllSegments(1, $this->adminUser);
        
        $segment = RLS_Segment::inRandomOrder()->first();
        
        $segmentId = $segment->id;
        
        $this->actingAs($this->adminUser)
            ->ajaxPost(route('removed-lru.destroy', $segmentId))
            ->assertStatus(200);
            
        $this->assertDatabaseMissing('RLS_Segments', ['id' => $segmentId]);
    }
}
