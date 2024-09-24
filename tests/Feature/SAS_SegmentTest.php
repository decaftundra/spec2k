<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\UtasCode as Utas;
use App\ShopFindings\SAS_Segment;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class SAS_SegmentTest extends TestCase
{
    /**
     * Test the Received LRU form response is 200.
     *
     * @return void
     */
    public function testSAS_SegmentForm()
    {
        $notification = $this->getEditableNotification($this->user);
        
        $this->actingAs($this->user)->call('GET', route('shop-action-details.edit', $notification->get_RCS_SFI()))->assertStatus(200);
    }
    
    /**
     * Test user can't edit or update segment for another location.
     *
     * @return void
     */
    public function testUserCantEditOrUpdateSAS_SegmentForOtherLocation()
    {
        $notification = $this->getNotificationNotInUsersLocation($this->user);
        
        $this->actingAs($this->user)
            ->call('GET', route('shop-action-details.edit', $notification->get_RCS_SFI()))
            ->assertStatus(403);
            
        $SAS_Segment = factory(SAS_Segment::class)->make();
        
        $attributes = [
            'rcsSFI' => $notification->get_RCS_SFI(),
            'plant_code' => $notification->plant_code,
            'INT' => $SAS_Segment->get_SAS_INT(),
        	'SHL' => $SAS_Segment->get_SAS_SHL(),
        	'RFI' => $SAS_Segment->get_SAS_RFI(),
        	'MAT' => $SAS_Segment->get_SAS_MAT(),
        	'SAC' => $SAS_Segment->get_SAS_SAC(),
        	'SDI' => $SAS_Segment->get_SAS_SDI(),
        	'PSC' => $SAS_Segment->get_SAS_PSC(),
        	'REM' => $SAS_Segment->get_SAS_REM(),
        ];
        
        if (is_null($attributes['SDI'])) {
            unset($attributes['SDI']);
        }
        
        $response = $this->actingAs($this->user)
            ->call('POST', route('shop-action-details.update', $notification->get_RCS_SFI()), $attributes)
            ->assertStatus(403);
    }
    
    /**
     * Test admin can edit and update segment for another location.
     *
     * @return void
     */
    public function testAdminCanEditAndUpdateSAS_SegmentForOtherLocation()
    {
        $notification = $this->getNotificationNotInUsersLocation($this->adminUser);
        
        $this->actingAs($this->adminUser)
            ->call('GET', route('shop-action-details.edit', $notification->get_RCS_SFI()))
            ->assertStatus(200);
            
        $SAS_Segment = factory(SAS_Segment::class)->make();
        
        $attributes = [
            'rcsSFI' => $notification->get_RCS_SFI(),
            'plant_code' => $notification->plant_code,
            'INT' => $SAS_Segment->get_SAS_INT(),
        	'SHL' => $SAS_Segment->get_SAS_SHL(),
        	'RFI' => $SAS_Segment->get_SAS_RFI(),
        	'MAT' => $SAS_Segment->get_SAS_MAT(),
        	'SAC' => $SAS_Segment->get_SAS_SAC(),
        	'SDI' => $SAS_Segment->get_SAS_SDI(),
        	'PSC' => $SAS_Segment->get_SAS_PSC(),
        	'REM' => $SAS_Segment->get_SAS_REM(),
        ];
        
        if (is_null($attributes['SDI'])) {
            unset($attributes['SDI']);
        }
        
        $response = $this->actingAs($this->adminUser)
            ->call('POST', route('shop-action-details.update', $notification->get_RCS_SFI()), $attributes)
            ->assertStatus(302);
            
        $this->get($response->headers->get('Location'))
            ->assertSee('Shop Action Details saved successfully!');
        
        unset($attributes['rcsSFI']); // Not recorded in DB.
        unset($attributes['plant_code']); // Not recorded in DB.
        
        $this->assertDatabaseHas('SAS_Segments', $attributes);
    }
    
    /**
     * Assert the session has errors if an empty form is submitted.
     *
     * @return void
     */
    public function testInvalidSASFormSubmit()
    {
        $notification = $this->getEditableNotification($this->user);
        
        $attributes = [
            'rcsSFI' => $notification->get_RCS_SFI(),
            'plant_code' => $notification->plant_code
        ];
        
        $response = $this->actingAs($this->user)
            ->call('POST', route('shop-action-details.update', $notification->get_RCS_SFI()), $attributes);
        
        // There are different required fields for UTAS parts.
        if (in_array($notification->get_RCS_MPN(), Utas::getAllUtasCodes())) {
            $response->assertSessionHasErrors(['SHL', 'RFI', 'SAC', 'PSC']);
        } else {
            $response->assertSessionHasErrors(['INT', 'SHL', 'RFI', 'SAC', 'PSC']);
        }
    }
    
    /**
     * Test the Received LRU form request validates and saves to database.
     *
     * @return void
     */
    public function testEditSAS_Segment()
    {
        $notification = $this->getEditableNotification($this->user);
        
        $SAS_Segment = factory(SAS_Segment::class)->make();
        
        $attributes = [
            'rcsSFI' => $notification->get_RCS_SFI(),
            'plant_code' => $notification->plant_code,
            'INT' => $SAS_Segment->get_SAS_INT(),
        	'SHL' => $SAS_Segment->get_SAS_SHL(),
        	'RFI' => $SAS_Segment->get_SAS_RFI(),
        	'MAT' => $SAS_Segment->get_SAS_MAT(),
        	'SAC' => $SAS_Segment->get_SAS_SAC(),
        	'SDI' => $SAS_Segment->get_SAS_SDI(),
        	'PSC' => $SAS_Segment->get_SAS_PSC(),
        	'REM' => $SAS_Segment->get_SAS_REM(),
        ];
        
        if (is_null($attributes['SDI'])) {
            unset($attributes['SDI']);
        }
        
        $response = $this->actingAs($this->user)
            ->call('POST', route('shop-action-details.update', $notification->get_RCS_SFI()), $attributes)
            ->assertStatus(302);
        
        $this->get($response->headers->get('Location'))
            ->assertSee('Shop Action Details saved successfully!');
        
        unset($attributes['rcsSFI']); // Not recorded in DB.
        unset($attributes['plant_code']); // Not recorded in DB.
        
        $this->assertDatabaseHas('SAS_Segments', $attributes);
    }
    
    /**
     * Test the Received LRU form request validates and saves to database.
     *
     * @return void
     */
    public function testEditCollinsSAS_Segment()
    {
        $notification = $this->getEditableCollinsNotification($this->user);
        
        $SAS_Segment = factory(SAS_Segment::class)->make();
        
        $attributes = [
            'rcsSFI' => $notification->get_RCS_SFI(),
            'plant_code' => $notification->plant_code,
            //'INT' => $SAS_Segment->get_SAS_INT(), There is no INT field in the Collins form.
        	'SHL' => $SAS_Segment->get_SAS_SHL(),
        	'RFI' => $SAS_Segment->get_SAS_RFI(),
        	'MAT' => $SAS_Segment->get_SAS_MAT(),
        	'SAC' => $SAS_Segment->get_SAS_SAC(),
        	'SDI' => $SAS_Segment->get_SAS_SDI(),
        	'PSC' => $SAS_Segment->get_SAS_PSC(),
        	'REM' => $SAS_Segment->get_SAS_REM(),
        ];
        
        if (is_null($attributes['SDI'])) {
            unset($attributes['SDI']);
        }
        
        $response = $this->actingAs($this->user)
            ->call('POST', route('shop-action-details.update', $notification->get_RCS_SFI()), $attributes)
            ->assertStatus(302);
        
        $this->get($response->headers->get('Location'))
            ->assertSee('Shop Action Details saved successfully!');
        
        unset($attributes['rcsSFI']); // Not recorded in DB.
        unset($attributes['plant_code']); // Not recorded in DB.
        
        $this->assertDatabaseHas('SAS_Segments', $attributes);
    }
    
    /**
     * Test that the segment can be deleted.
     *
     * @return void
     */
    public function testDeleteSAS_Segment()
    {
        $this->createSingleShopFindingAndPiecePartsWithAllSegments(1, $this->adminUser);
        
        $segment = SAS_Segment::inRandomOrder()->first();
        
        $segmentId = $segment->id;
        
        $this->actingAs($this->adminUser)
            ->ajaxPost(route('shop-action-details.destroy', $segmentId))
            ->assertStatus(200);
            
        $this->assertDatabaseMissing('SAS_Segments', ['id' => $segmentId]);
    }
}
