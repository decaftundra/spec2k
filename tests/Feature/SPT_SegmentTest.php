<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\ShopFindings\SPT_Segment;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class SPT_SegmentTest extends TestCase
{
    /**
     * Test the Shop Processing Time form response is 200.
     *
     * @return void
     */
    public function testSPT_SegmentForm()
    {
        $notification = $this->getEditableNotification($this->user);
        
        $this->actingAs($this->user)->call('GET', route('shop-processing-time.edit', $notification->get_RCS_SFI()))->assertStatus(200);
    }
    
    /**
     * Test user can't edit or update segment from another location.
     *
     * @return void
     */
    public function testUserCantEditOrUpdateSPT_SegmentForOtherLocation()
    {
        $notification = $this->getNotificationNotInUsersLocation($this->user);
        
        $this->actingAs($this->user)
            ->call('GET', route('shop-processing-time.edit', $notification->get_RCS_SFI()))
            ->assertStatus(403);
            
        $SPT_Segment = factory(SPT_Segment::class)->make();
        
        $attributes = [
            'rcsSFI' => $notification->get_RCS_SFI(),
            'plant_code' => $notification->plant_code,
            'MAH' => $SPT_Segment->get_SPT_MAH(),
        	'FLW' => $SPT_Segment->get_SPT_FLW(),
        	'MST' => $SPT_Segment->get_SPT_MST(),
        ];
        
        $response = $this->actingAs($this->user)
            ->call('POST', route('shop-processing-time.update', $notification->get_RCS_SFI()), $attributes)
            ->assertStatus(403);
    }
    
    /**
     * Test admin can edit and update segment from another location.
     *
     * @return void
     */
    public function testAdminCanEditAndUpdateSPT_SegmentForOtherLocation()
    {
        $notification = $this->getNotificationNotInUsersLocation($this->adminUser);
        
        $this->actingAs($this->adminUser)
            ->call('GET', route('shop-processing-time.edit', $notification->get_RCS_SFI()))
            ->assertStatus(200);
            
        $SPT_Segment = factory(SPT_Segment::class)->make();
        
        $attributes = [
            'rcsSFI' => $notification->get_RCS_SFI(),
            'plant_code' => $notification->plant_code,
            'MAH' => $SPT_Segment->get_SPT_MAH(),
        	'FLW' => $SPT_Segment->get_SPT_FLW(),
        	'MST' => $SPT_Segment->get_SPT_MST(),
        ];
        
        $response = $this->actingAs($this->adminUser)
            ->call('POST', route('shop-processing-time.update', $notification->get_RCS_SFI()), $attributes)
            ->assertStatus(302);
            
        $this->get($response->headers->get('Location'))
            ->assertSee('Shop Processing Time saved successfully!');
        
        unset($attributes['rcsSFI']); // Not recorded in DB.
        unset($attributes['plant_code']); // Not recorded in DB.
        
        $this->assertDatabaseHas('SPT_Segments', $attributes);
    }
    
    /**
     * Assert the session has errors if an empty form is submitted.
     *
     * @return void
     */
    public function testInvalidSPTFormSubmit()
    {
        $notifications = $this->actingAs($this->user)->getNotifications();
        
        $notification = collect($notifications)->random(); // Get a random Notification from collection.
        
        $attributes = [
            'rcsSFI' => $notification->get_RCS_SFI(),
            'plant_code' => $notification->plant_code,
        ];
        
        $response = $this->actingAs($this->user)
            ->call('POST', route('shop-processing-time.update', $notification->get_RCS_SFI()), $attributes);
            
        // There are no required fields in this segment.
        $response->assertSessionHasErrors(['MAH', 'FLW', 'MST']);
    }
    
    /**
     * Test the Shop Processing Time form request validates.
     *
     * @return void
     */
    public function testEditSPT_Segment()
    {
        $notification = $this->getEditableNotification($this->user);
        
        $SPT_Segment = factory(SPT_Segment::class)->make();
        
        $attributes = [
            'rcsSFI' => $notification->get_RCS_SFI(),
            'plant_code' => $notification->plant_code,
            'MAH' => $SPT_Segment->get_SPT_MAH(),
        	'FLW' => $SPT_Segment->get_SPT_FLW(),
        	'MST' => $SPT_Segment->get_SPT_MST(),
        ];
        
        $response = $this->actingAs($this->user)
            ->call('POST', route('shop-processing-time.update', $notification->get_RCS_SFI()), $attributes)
            ->assertStatus(302);
        
        $this->get($response->headers->get('Location'))->assertSee('Shop Processing Time saved successfully!');
        
        unset($attributes['rcsSFI']); // Not recorded in DB.
        unset($attributes['plant_code']); // Not recorded in DB.
        
        $this->assertDatabaseHas('SPT_Segments', $attributes);
    }
    
    /**
     * Test that the segment can be deleted.
     *
     * @return void
     */
    public function testDeleteSPT_Segment()
    {
        $this->createSingleShopFindingAndPiecePartsWithAllSegments(1, $this->adminUser);
        
        $segment = SPT_Segment::inRandomOrder()->first();
        
        $segmentId = $segment->id;
        
        $this->actingAs($this->adminUser)
            ->ajaxPost(route('shop-processing-time.destroy', $segmentId))
            ->assertStatus(200);
            
        $this->assertDatabaseMissing('SPT_Segments', ['id' => $segmentId]);
    }
}
