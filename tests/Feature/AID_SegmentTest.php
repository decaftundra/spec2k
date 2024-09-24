<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\ShopFindings\AID_Segment;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;

class AID_SegmentTest extends TestCase
{
    /**
     * Test the header form response is 200.
     *
     * @return void
     */
    public function testAID_SegmentForm()
    {
        $notification = $this->getEditableNotification($this->user);
        
        $this->actingAs($this->user)
            ->call('GET', route('airframe-information.edit', $notification->get_RCS_SFI()))
            ->assertStatus(200);
    }
    
    /**
     * Test for a 403 error if the user tries to edit or update a segment from another location.
     *
     * @return void
     */
    public function testUserCantEditAID_SegmentFromOtherLocation()
    {
        $notification = $this->getNotificationNotInUsersLocation($this->user);
        
        $this->actingAs($this->user)
            ->call('GET', route('airframe-information.edit', $notification->get_RCS_SFI()))
            ->assertStatus(403);
            
        $AID_Segment = factory(AID_Segment::class)->make();
        
        $attributes = [
            'rcsSFI' => $notification->get_RCS_SFI(),
            'plant_code' => $notification->plant_code,
            'MFR' => $AID_Segment->get_AID_MFR(),
        	'AMC' => $AID_Segment->get_AID_AMC(),
        	'MFN' => $AID_Segment->get_AID_MFN(),
        	'ASE' => $AID_Segment->get_AID_ASE(),
        	'AIN' => $AID_Segment->get_AID_AIN(),
        	'REG' => $AID_Segment->get_AID_REG(),
        	'OIN' => $AID_Segment->get_AID_OIN(),
        	'CTH' => $AID_Segment->get_AID_CTH(),
        	'CTY' => $AID_Segment->get_AID_CTY(),
        ];
        
        $response = $this->actingAs($this->user)
            ->call('POST', route('airframe-information.update', $notification), $attributes);
            
        $errors = session('errors');
        
        if (!empty($errors)) {
            mydd($errors);
            var_dump($attributes);
        }
        
        $response->assertStatus(403);
    }
    
    /**
     * Test that an admin user can still edit and update a segment from another location.
     *
     * @return void
     */
    public function testAdminCanEditAID_SegmentFromOtherLocation()
    {
        $notification = $this->getNotificationNotInUsersLocation($this->adminUser);
        
        $this->actingAs($this->adminUser)
            ->call('GET', route('airframe-information.edit', $notification->get_RCS_SFI()))
            ->assertStatus(200);
            
        $AID_Segment = factory(AID_Segment::class)->make();
        
        $attributes = [
            'rcsSFI' => $notification->get_RCS_SFI(),
            'plant_code' => $notification->plant_code,
            'MFR' => $AID_Segment->get_AID_MFR(),
        	'AMC' => $AID_Segment->get_AID_AMC(),
        	'MFN' => $AID_Segment->get_AID_MFN(),
        	'ASE' => $AID_Segment->get_AID_ASE(),
        	'AIN' => $AID_Segment->get_AID_AIN(),
        	'REG' => $AID_Segment->get_AID_REG(),
        	'OIN' => $AID_Segment->get_AID_OIN(),
        	'CTH' => $AID_Segment->get_AID_CTH(),
        	'CTY' => $AID_Segment->get_AID_CTY(),
        ];
        
        $response = $this->actingAs($this->adminUser)
            ->call('POST', route('airframe-information.update', $notification), $attributes);
            
        $errors = session('errors');
        
        if (!empty($errors)) {
            mydd($errors);
            var_dump($attributes);
        }
        
        $response->assertStatus(302);
        
        $this->get($response->headers->get('Location'))
            ->assertSee('Airframe Information saved successfully!');
        
        unset($attributes['rcsSFI']); // Not recorded in DB.
        unset($attributes['plant_code']); // Not recorded in DB.
        
        $this->assertDatabaseHas('AID_Segments', $attributes);
    }
    
    /**
     * Assert the session has errors if an empty form is submitted.
     *
     * @return void
     */
    public function testInvalidAIDFormSubmit()
    {
        $notification = $this->getEditableNotification($this->user);
        
        $attributes = [
            'rcsSFI' => $notification->get_RCS_SFI(),
            'plant_code' => $notification->plant_code,
        ];
        
        $response = $this->actingAs($this->user)
            ->call('POST', route('airframe-information.update', $notification), $attributes);
        
        $response->assertSessionHasErrors(['MFR', 'AMC', 'AIN', 'REG', 'OIN']);
    }
    
    /**
     * Test the header form request validates.
     *
     * @return void
     */
    public function testEditAID_Segment()
    {
        $notification = $this->getEditableNotification($this->user);
        
        $AID_Segment = factory(AID_Segment::class)->make();
        
        $attributes = [
            'rcsSFI' => $notification->get_RCS_SFI(),
            'plant_code' => $notification->plant_code,
            'MFR' => $AID_Segment->get_AID_MFR(),
        	'AMC' => $AID_Segment->get_AID_AMC(),
        	'MFN' => $AID_Segment->get_AID_MFN(),
        	'ASE' => $AID_Segment->get_AID_ASE(),
        	'AIN' => $AID_Segment->get_AID_AIN(),
        	'REG' => $AID_Segment->get_AID_REG(),
        	'OIN' => $AID_Segment->get_AID_OIN(),
        	'CTH' => $AID_Segment->get_AID_CTH(),
        	'CTY' => $AID_Segment->get_AID_CTY(),
        ];
        
        $response = $this->actingAs($this->user)
            ->call('POST', route('airframe-information.update', $notification), $attributes);
            
        $errors = session('errors');
        
        if (!empty($errors)) {
            mydd($errors);
            var_dump($attributes);
        }
            
        $response->assertStatus(302);
        
        $this->get($response->headers->get('Location'))
            ->assertSee('Airframe Information saved successfully!');
        
        unset($attributes['rcsSFI']); // Not recorded in DB.
        unset($attributes['plant_code']); // Not recorded in DB.
        
        $this->assertDatabaseHas('AID_Segments', $attributes);
    }
    
    /**
     * Test that the segment can be deleted.
     *
     * @return void
     */
    public function testDeleteAID_Segment()
    {
        $this->createSingleShopFindingAndPiecePartsWithAllSegments(1, $this->adminUser);
        
        $segment = AID_Segment::inRandomOrder()->first();
        
        $segmentId = $segment->id;
        
        $this->actingAs($this->adminUser)
            ->ajaxPost(route('airframe-information.destroy', $segmentId))
            ->assertStatus(200);
            
        $this->assertDatabaseMissing('AID_Segments', ['id' => $segmentId]);
    }
}
