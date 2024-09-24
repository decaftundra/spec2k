<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\ShopFindings\ATT_Segment;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ATT_SegmentTest extends TestCase
{
    /**
     * Test the Accumulated Time Text form response is 200.
     *
     * @return void
     */
    public function testATT_SegmentForm()
    {
        $notification = $this->getEditableNotification($this->user);
        
        $this->actingAs($this->user)->call('GET', route('accumulated-time-text.edit', $notification->get_RCS_SFI()))->assertStatus(200);
    }
    
    /**
     * Test that a user can't edit or update a segment from a different location.
     *
     * @return void
     */
    public function testUserCantEditOrUpdateATT_SegmentFromOtherLocation()
    {
        $notification = $this->getNotificationNotInUsersLocation($this->adminUser);
        
        $this->actingAs($this->user)
            ->call('GET', route('accumulated-time-text.edit', $notification->get_RCS_SFI()))
            ->assertStatus(403);
            
        $ATT_Segment = factory(ATT_Segment::class)->make();
        
        $attributes = [
            'rcsSFI' => $notification->get_RCS_SFI(),
            'plant_code' => $notification->plant_code,
            'TRF' => $ATT_Segment->get_ATT_TRF(),
            'OTT' => $ATT_Segment->get_ATT_OTT(),
            'OPC' => $ATT_Segment->get_ATT_OPC(),
            'ODT' => $ATT_Segment->get_ATT_ODT(),
        ];
        
        $response = $this->actingAs($this->user)
            ->call('POST', route('accumulated-time-text.update', $notification->get_RCS_SFI()), $attributes);
            
        $response->assertStatus(403);
    }
    
    /**
     * Test an admin can edit and update a segment from a different location...
     *
     * @param (type) $name
     * @return
     */
    public function testAdminCanEditAndUpdateATT_SegmentFromOtherLocation()
    {
        $notification = $this->getNotificationNotInUsersLocation($this->adminUser);
        
        $this->actingAs($this->adminUser)
            ->call('GET', route('accumulated-time-text.edit', $notification->get_RCS_SFI()))
            ->assertStatus(200);
            
        $ATT_Segment = factory(ATT_Segment::class)->make();
        
        $attributes = [
            'rcsSFI' => $notification->get_RCS_SFI(),
            'plant_code' => $notification->plant_code,
            'TRF' => $ATT_Segment->get_ATT_TRF(),
            'OTT' => $ATT_Segment->get_ATT_OTT(),
            'OPC' => $ATT_Segment->get_ATT_OPC(),
            'ODT' => $ATT_Segment->get_ATT_ODT(),
        ];
        
        $response = $this->actingAs($this->adminUser)
            ->call('POST', route('accumulated-time-text.update', $notification->get_RCS_SFI()), $attributes);
            
        $response->assertStatus(302);
        
        $this->get($response->headers->get('Location'))
            ->assertSee('Accumulated Time Text saved successfully!');
        
        unset($attributes['rcsSFI']); // Not recorded in DB.
        unset($attributes['plant_code']); // Not recorded in DB.
        
        $this->assertDatabaseHas('ATT_Segments', $attributes);
    }
    
    /**
     * Assert the session has errors if an empty form is submitted.
     *
     * @return void
     */
    public function testInvalidATTFormSubmit()
    {
        $notification = $this->getEditableNotification($this->user);
        
        $attributes = [
            'rcsSFI' => $notification->get_RCS_SFI(),
        ];
        
        $response = $this->actingAs($this->user)
            ->call('POST', route('accumulated-time-text.update', $notification->get_RCS_SFI()), $attributes);
        
        $response->assertSessionHasErrors(['TRF', 'OTT', 'OPC', 'ODT']);
    }
    
    /**
     * Test the Accumulated Time Text form request validates.
     *
     * @return void
     */
    public function testEditATT_Segment()
    {
        $notification = $this->getEditableNotification($this->user);
        
        $ATT_Segment = factory(ATT_Segment::class)->make();
        
        $attributes = [
            'rcsSFI' => $notification->get_RCS_SFI(),
            'plant_code' => $notification->plant_code,
            'TRF' => $ATT_Segment->get_ATT_TRF(),
            'OTT' => $ATT_Segment->get_ATT_OTT(),
            'OPC' => $ATT_Segment->get_ATT_OPC(),
            'ODT' => $ATT_Segment->get_ATT_ODT(),
        ];
        
        $response = $this->actingAs($this->user)
            ->call('POST', route('accumulated-time-text.update', $notification->get_RCS_SFI()), $attributes);
            
        $response->assertStatus(302);
            
        $this->get($response->headers->get('Location'))
            ->assertSee('Accumulated Time Text saved successfully!');
        
        unset($attributes['rcsSFI']); // Not recorded in DB.
        unset($attributes['plant_code']); // Not recorded in DB.
        
        $this->assertDatabaseHas('ATT_Segments', $attributes);
    }
    
    /**
     * Test that the segment can be deleted.
     *
     * @return void
     */
    public function testDeleteATT_Segment()
    {
        $this->createSingleShopFindingAndPiecePartsWithAllSegments(1, $this->adminUser);
        
        $segment = ATT_Segment::inRandomOrder()->first();
        
        $segmentId = $segment->id;
        
        $this->actingAs($this->adminUser)
            ->ajaxPost(route('accumulated-time-text.destroy', $segmentId))
            ->assertStatus(200);
            
        $this->assertDatabaseMissing('ATT_Segments', ['id' => $segmentId]);
    }
}