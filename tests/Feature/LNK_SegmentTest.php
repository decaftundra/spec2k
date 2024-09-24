<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\ShopFindings\LNK_Segment;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class LNK_SegmentTest extends TestCase
{
    /**
     * Test the Linking Field form response is 200.
     *
     * @return void
     */
    public function testLNK_SegmentForm()
    {
        $notification = $this->getEditableNotification($this->user);
        
        $this->actingAs($this->user)->call('GET', route('linking-field.edit', $notification->get_RCS_SFI()))->assertStatus(200);
    }
    
    /**
     * Test a user can't edit or update a segment from another location.
     *
     * @return void
     */
    public function testUserCantEditOrUpdateLNK_SegmentFromOtherLocation()
    {
        $notification = $this->getNotificationNotInUsersLocation($this->user);
        
        $this->actingAs($this->user)
            ->call('GET', route('linking-field.edit', $notification->get_RCS_SFI()))
            ->assertStatus(403);
            
        $LNK_Segment = factory(LNK_Segment::class)->make();
        
        $attributes = [
            'rcsSFI' => $notification->get_RCS_SFI(),
            'plant_code' => $notification->plant_code,
            'RTI' => $LNK_Segment->get_LNK_RTI()
        ];
        
        $response = $this->actingAs($this->user)
            ->call('POST', route('linking-field.update', $notification->get_RCS_SFI()), $attributes);
            
        $response->assertStatus(403);
    }
    
    /**
     * Test admin can edit or update a segment from another location.
     *
     * @return void
     */
    public function testAdminCanEditOrUpdateLNK_SegmentFromOtherLocation()
    {
        $notification = $this->getNotificationNotInUsersLocation($this->adminUser);
        
        $this->actingAs($this->adminUser)
            ->call('GET', route('linking-field.edit', $notification->get_RCS_SFI()))
            ->assertStatus(200);
            
        $LNK_Segment = factory(LNK_Segment::class)->make();
        
        $attributes = [
            'rcsSFI' => $notification->get_RCS_SFI(),
            'plant_code' => $notification->plant_code,
            'RTI' => $LNK_Segment->get_LNK_RTI()
        ];
        
        $response = $this->actingAs($this->adminUser)
            ->call('POST', route('linking-field.update', $notification->get_RCS_SFI()), $attributes);
            
        $response->assertStatus(302);
        
        $this->get($response->headers->get('Location'))
            ->assertSee('Linking Fields saved successfully!');
        
        unset($attributes['rcsSFI']); // Not recorded in DB.
        unset($attributes['plant_code']); // Not recorded in DB.
        
        $this->assertDatabaseHas('LNK_Segments', $attributes);
    }
    
    /**
     * Assert the session has errors if an empty form is submitted.
     *
     * @return void
     */
    public function testInvalidLNKFormSubmit()
    {
        $notification = $this->getEditableNotification($this->user);
        
        $attributes = [
            'rcsSFI' => $notification->get_RCS_SFI(),
            'plant_code' => $notification->plant_code,
        ];
        
        $response = $this->actingAs($this->user)
            ->call('POST', route('linking-field.update', $notification->get_RCS_SFI()), $attributes);
        
        $response->assertSessionHasErrors(['RTI']);
    }
    
    /**
     * Test the Linking Field form request validates.
     *
     * @return void
     */
    public function testEditNK_Segment()
    {
        $notification = $this->getEditableNotification($this->user);
        
        $LNK_Segment = factory(LNK_Segment::class)->make();
        
        $attributes = [
            'rcsSFI' => $notification->get_RCS_SFI(),
            'plant_code' => $notification->plant_code,
            'RTI' => $LNK_Segment->get_LNK_RTI()
        ];
        
        $response = $this->actingAs($this->user)
            ->call('POST', route('linking-field.update', $notification->get_RCS_SFI()), $attributes);
            
        $response->assertStatus(302);
        
        $this->get($response->headers->get('Location'))
            ->assertSee('Linking Fields saved successfully!');
        
        unset($attributes['rcsSFI']); // Not recorded in DB.
        unset($attributes['plant_code']); // Not recorded in DB.
        
        $this->assertDatabaseHas('LNK_Segments', $attributes);
    }
    
    /**
     * Test that the segment can be deleted.
     *
     * @return void
     */
    public function testDeleteLNK_Segment()
    {
        $this->createSingleShopFindingAndPiecePartsWithAllSegments(1, $this->adminUser);
        
        $segment = LNK_Segment::inRandomOrder()->first();
        
        $segmentId = $segment->id;
        
        $this->actingAs($this->adminUser)
            ->ajaxPost(route('linking-field.destroy', $segmentId))
            ->assertStatus(200);
            
        $this->assertDatabaseMissing('LNK_Segments', ['id' => $segmentId]);
    }
}
