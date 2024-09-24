<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\ShopFindings\API_Segment;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class API_SegmentTest extends TestCase
{
    /**
     * Test the header form response is 200.
     *
     * @return void
     */
    public function testAPI_SegmentForm()
    {
        $notification = $this->getEditableNotification($this->user);
        
        $this->actingAs($this->user)->call('GET', route('apu-information.edit', $notification->get_RCS_SFI()))->assertStatus(200);
    }
    
    /**
     * Test that a user can't edit or update a segment from another location.
     *
     * @return void
     */
    public function testUserCantEditAPI_SegmentFromOtherLocation()
    {
        $notification = $this->getNotificationNotInUsersLocation($this->user);
        
        $this->actingAs($this->user)
            ->call('GET', route('apu-information.edit', $notification->get_RCS_SFI()))
            ->assertStatus(403);
            
        $API_Segment = factory(API_Segment::class)->make();
        
        $attributes = [
            'rcsSFI' => $notification->get_RCS_SFI(),
            'plant_code' => $notification->plant_code,
            'AET' => $API_Segment->get_API_AET(),
        	'EMS' => $API_Segment->get_API_EMS(),
        	'AEM' => $API_Segment->get_API_AEM(),
        	'MFR' => $API_Segment->get_API_MFR(),
        	'ATH' => $API_Segment->get_API_ATH(),
        	'ATC' => $API_Segment->get_API_ATC(),
        ];
        
        $response = $this->actingAs($this->user)
            ->call('POST', route('apu-information.update', $notification->get_RCS_SFI()), $attributes);
            
        $response->assertStatus(403);
    }
    
    /**
     * Test that a user can't edit or update a segment from another location.
     *
     * @return void
     */
    public function testAdminCanEditAPI_SegmentFromOtherLocation()
    {
        $notification = $this->getNotificationNotInUsersLocation($this->adminUser);
        
        $this->actingAs($this->adminUser)
            ->call('GET', route('apu-information.edit', $notification->get_RCS_SFI()))
            ->assertStatus(200);
            
        $API_Segment = factory(API_Segment::class)->make();
        
        //mydd($notification->get_API_AET());
        //mydd($API_Segment->AET);
        //mydd($API_Segment->get_API_AET());
        
        $attributes = [
            'rcsSFI' => $notification->get_RCS_SFI(),
            'plant_code' => $notification->plant_code,
            'AET' => $API_Segment->get_API_AET(),
        	'EMS' => $API_Segment->get_API_EMS(),
        	'AEM' => $API_Segment->get_API_AEM(),
        	'MFR' => $API_Segment->get_API_MFR(),
        	'ATH' => $API_Segment->get_API_ATH(),
        	'ATC' => $API_Segment->get_API_ATC(),
        ];
        
        $response = $this->actingAs($this->adminUser)
            ->call('POST', route('apu-information.update', $notification->get_RCS_SFI()), $attributes);
            
        $response->assertStatus(302);
        
        $this->get($response->headers->get('Location'))
            ->assertSee('APU Information saved successfully!');
        
        unset($attributes['rcsSFI']); // Not recorded in DB.
        unset($attributes['plant_code']); // Not recorded in DB.
        
        $this->assertDatabaseHas('API_Segments', $attributes);
    }
    
    /**
     * Assert the session has errors if an empty form is submitted.
     *
     * @return void
     */
    public function testInvalidAPIFormSubmit()
    {
        $notification = $this->getEditableNotification($this->user);
        
        $attributes = [
            'rcsSFI' => $notification->get_RCS_SFI(),
        ];
        
        $response = $this->actingAs($this->user)
            ->call('POST', route('apu-information.update', $notification->get_RCS_SFI()), $attributes);
        
        $response->assertSessionHasErrors(['AET', 'EMS']);
    }
    
    /**
     * Test the header form request validates.
     *
     * @return void
     */
    public function testEditAPI_Segment()
    {
        $notification = $this->getEditableNotification($this->user);
        
        $API_Segment = factory(API_Segment::class)->make();
        
        $attributes = [
            'rcsSFI' => $notification->get_RCS_SFI(),
            'plant_code' => $notification->plant_code,
            'AET' => $API_Segment->get_API_AET(),
        	'EMS' => $API_Segment->get_API_EMS(),
        	'AEM' => $API_Segment->get_API_AEM(),
        	'MFR' => $API_Segment->get_API_MFR(),
        	'ATH' => $API_Segment->get_API_ATH(),
        	'ATC' => $API_Segment->get_API_ATC(),
        ];
        
        $response = $this->actingAs($this->user)
            ->call('POST', route('apu-information.update', $notification->get_RCS_SFI()), $attributes);
            
        $response->assertStatus(302);
        
        $this->get($response->headers->get('Location'))
            ->assertSee('APU Information saved successfully!');
        
        unset($attributes['rcsSFI']); // Not recorded in DB.
        unset($attributes['plant_code']); // Not recorded in DB.
        
        $this->assertDatabaseHas('API_Segments', $attributes);
    }
    
    /**
     * Test that the segment can be deleted.
     *
     * @return void
     */
    public function testDeleteAPI_Segment()
    {
        $this->createSingleShopFindingAndPiecePartsWithAllSegments(1, $this->adminUser);
        
        $segment = API_Segment::inRandomOrder()->first();
        
        $segmentId = $segment->id;
        
        $this->actingAs($this->adminUser)
            ->ajaxPost(route('apu-information.destroy', $segmentId))
            ->assertStatus(200);
            
        $this->assertDatabaseMissing('API_Segments', ['id' => $segmentId]);
    }
}
