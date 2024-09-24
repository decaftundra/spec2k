<?php

namespace Tests\Feature;

use Carbon\Carbon;
use Tests\TestCase;
use App\ShopFindings\SUS_Segment;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class SUS_SegmentTest extends TestCase
{
    /**
     * Test the Received LRU form response is 200.
     *
     * @return void
     */
    public function testSUS_SegmentForm()
    {
        $notification = $this->getEditableNotification($this->user);
        
        $this->actingAs($this->user)->call('GET', route('shipped-lru.edit', $notification->get_RCS_SFI()))->assertStatus(200);
    }
    
    /**
     * Test user can't edit or update segment from another location.
     *
     * @return void
     */
    public function testUserCantEditOrUpdateSUS_SegmentForOtherLocation()
    {
        $notification = $this->getNotificationNotInUsersLocation($this->user);
        
        $this->actingAs($this->user)
            ->call('GET', route('shipped-lru.edit', $notification->get_RCS_SFI()))
            ->assertStatus(403);
            
        $SUS_Segment = factory(SUS_Segment::class)->make();
        
        $attributes = [
            'rcsSFI' => $notification->get_RCS_SFI(),
            'plant_code' => $notification->plant_code,
            'SHD' => $SUS_Segment->get_SUS_SHD(),
            'MFR' => $SUS_Segment->get_SUS_MFR(),
            'MPN' => $SUS_Segment->get_SUS_MPN(),
            'SER' => $SUS_Segment->get_SUS_SER(),
            'MFN' => $SUS_Segment->get_SUS_MFN(),
            'PDT' => $SUS_Segment->get_SUS_PDT(),
            'PNR' => $SUS_Segment->get_SUS_PNR(),
            'OPN' => $SUS_Segment->get_SUS_OPN(),
            'USN' => $SUS_Segment->get_SUS_USN(),
            'ASN' => $SUS_Segment->get_SUS_ASN(),
            'UCN' => $SUS_Segment->get_SUS_UCN(),
            'SPL' => $SUS_Segment->get_SUS_SPL(),
            'UST' => $SUS_Segment->get_SUS_UST(),
            'PML' => $SUS_Segment->get_SUS_PML(),
            'PSC' => $SUS_Segment->get_SUS_PSC(),
        ];
        
        $response = $this->actingAs($this->user)
            ->call('POST', route('shipped-lru.update', $notification->get_RCS_SFI()), $attributes);
            
        // Debug
        $errors = session('errors');
        
        if (!empty($errors)) {
            mydd($errors);
        }
            
        $response->assertStatus(403);
    }
    
    /**
     * Test admin can edit and update segment from another location.
     *
     * @return void
     */
    public function testAdminCanEditAndUpdateSUS_SegmentForOtherLocation()
    {
        $notification = $this->getNotificationNotInUsersLocation($this->adminUser);
        
        $this->actingAs($this->adminUser)
            ->call('GET', route('shipped-lru.edit', $notification->get_RCS_SFI()))
            ->assertStatus(200);
        
        $SUS_Segment = factory(SUS_Segment::class)->make();
        
        $attributes = [
            'rcsSFI' => $notification->get_RCS_SFI(),
            'plant_code' => $notification->plant_code,
            'SHD' => $SUS_Segment->get_SUS_SHD(),
            'MFR' => $SUS_Segment->get_SUS_MFR(),
            'MPN' => $SUS_Segment->get_SUS_MPN(),
            'SER' => $SUS_Segment->get_SUS_SER(),
            'MFN' => $SUS_Segment->get_SUS_MFN(),
            'PDT' => $SUS_Segment->get_SUS_PDT(),
            'PNR' => $SUS_Segment->get_SUS_PNR(),
            'OPN' => $SUS_Segment->get_SUS_OPN(),
            'USN' => $SUS_Segment->get_SUS_USN(),
            'ASN' => $SUS_Segment->get_SUS_ASN(),
            'UCN' => $SUS_Segment->get_SUS_UCN(),
            'SPL' => $SUS_Segment->get_SUS_SPL(),
            'UST' => $SUS_Segment->get_SUS_UST(),
            'PML' => $SUS_Segment->get_SUS_PML(),
            'PSC' => $SUS_Segment->get_SUS_PSC(),
        ];
        
        $response = $this->actingAs($this->adminUser)
            ->call('POST', route('shipped-lru.update', $notification->get_RCS_SFI()), $attributes)
            ->assertStatus(302);
            
        $this->get($response->headers->get('Location'))
            ->assertSee('Shipped LRU saved successfully!');
        
        unset($attributes['rcsSFI']); // Not recorded in DB.
        unset($attributes['SHD']);
        unset($attributes['plant_code']);
        
        $this->assertDatabaseHas('SUS_Segments', $attributes);
    }
    
    /**
     * Assert the session has errors if an empty form is submitted.
     *
     * @return void
     */
    public function testInvalidSUSFormSubmit()
    {
        $notification = $this->getEditableNotification($this->user);
        
        $attributes = [
            'rcsSFI' => $notification->get_RCS_SFI(),
            'plant_code' => $notification->plant_code,
        ];
        
        $response = $this->actingAs($this->user)
            ->call('POST', route('shipped-lru.update', $notification->get_RCS_SFI()), $attributes);
        
        $response->assertSessionHasErrors(['SHD', 'MFR', 'MPN', 'SER']);
    }
    
    /**
     * Test the Received LRU form request validates.
     *
     * @return void
     */
    public function testSUS_SegmentValidation()
    {
        $notification = $this->getEditableNotification($this->user);
        
        $SUS_Segment = factory(SUS_Segment::class)->make();
        
        $attributes = [
            'rcsSFI' => $notification->get_RCS_SFI(),
            'plant_code' => $notification->plant_code,
            'SHD' => $SUS_Segment->get_SUS_SHD(),
            'MFR' => $SUS_Segment->get_SUS_MFR(),
            'MPN' => $SUS_Segment->get_SUS_MPN(),
            'SER' => $SUS_Segment->get_SUS_SER(),
            'MFN' => $SUS_Segment->get_SUS_MFN(),
            'PDT' => $SUS_Segment->get_SUS_PDT(),
            'PNR' => $SUS_Segment->get_SUS_PNR(),
            'OPN' => $SUS_Segment->get_SUS_OPN(),
            'USN' => $SUS_Segment->get_SUS_USN(),
            'ASN' => $SUS_Segment->get_SUS_ASN(),
            'UCN' => $SUS_Segment->get_SUS_UCN(),
            'SPL' => $SUS_Segment->get_SUS_SPL(),
            'UST' => $SUS_Segment->get_SUS_UST(),
            'PML' => $SUS_Segment->get_SUS_PML(),
            'PSC' => $SUS_Segment->get_SUS_PSC(),
        ];
        
        $response = $this->actingAs($this->user)
            ->call('POST', route('shipped-lru.update', $notification->get_RCS_SFI()), $attributes)
            ->assertStatus(302);
            
        $errors = session('errors');
    
        if (!empty($errors)) {
            mydd($errors);
            mydd($attributes);
            //die('Killed test.');
        }
            
        $this->get($response->headers->get('Location'))->assertSee('Shipped LRU saved successfully!');
        
        unset($attributes['rcsSFI']); // Not recorded in DB.
        unset($attributes['SHD']);
        unset($attributes['plant_code']);
        
        $this->assertDatabaseHas('SUS_Segments', $attributes);
    }
    
    /**
     * Test that the segment can be deleted.
     *
     * @return void
     */
    public function testDeleteSUS_Segment()
    {
        $this->createSingleShopFindingAndPiecePartsWithAllSegments(1, $this->adminUser);
        
        $segment = SUS_Segment::inRandomOrder()->first();
        
        $segmentId = $segment->id;
        
        $this->actingAs($this->adminUser)
            ->ajaxPost(route('shipped-lru.destroy', $segmentId))
            ->assertStatus(200);
            
        $this->assertDatabaseMissing('SUS_Segments', ['id' => $segmentId]);
    }
}