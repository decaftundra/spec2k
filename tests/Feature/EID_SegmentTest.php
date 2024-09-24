<?php

namespace Tests\Feature;

use App\AircraftDetail;
use App\Notification;
use Carbon\Carbon;
use Tests\TestCase;
use App\ShopFindings\AID_Segment;
use App\ShopFindings\EID_Segment;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class EID_SegmentTest extends TestCase
{
    /**
     * Test the header form response is 200.
     *
     * @return void
     */
    public function testEID_SegmentForm()
    {
        $notification = $this->getEditableNotification($this->user);
        
        $this->actingAs($this->user)->call('GET', route('engine-information.edit', $notification->get_RCS_SFI()))->assertStatus(200);
    }
    
    /**
     * Test a user can't edit or update a segment from another location.
     *
     * @return void
     */
    public function testUserCantEditOrUpdateEID_SegmentFromOtherLocation()
    {
        $notification = $this->getNotificationNotInUsersLocation($this->user);
        
        $this->actingAs($this->user)
            ->call('GET', route('engine-information.edit', $notification->get_RCS_SFI()))
            ->assertStatus(403);
            
        $EID_Segment = factory(EID_Segment::class)->make();
        
        $attributes = [
            'rcsSFI' => $notification->get_RCS_SFI(),
            'plant_code' => $notification->plant_code,
            'AET' => $EID_Segment->get_EID_AET(),
        	'EPC' => $EID_Segment->get_EID_EPC(),
        	'AEM' => $EID_Segment->get_EID_AEM(),
        	'EMS' => $EID_Segment->get_EID_EMS(),
        	'MFR' => $EID_Segment->get_EID_MFR(),
        	'ETH' => $EID_Segment->get_EID_ETH(),
        	'ETC' => $EID_Segment->get_EID_ETC(),
        ];
        
        $this->actingAs($this->user)
            ->call('POST', route('engine-information.update', $notification->get_RCS_SFI()), $attributes)
            ->assertStatus(403);
    }
    
    /**
     * Test admin can edit and update a segment from another location.
     *
     * @return void
     */
    public function testAdminCanEditAndUpdateEID_SegmentFromOtherLocation()
    {
        $notification = $this->getNotificationNotInUsersLocation($this->adminUser);
        
        $this->actingAs($this->adminUser)
            ->call('GET', route('engine-information.edit', $notification->get_RCS_SFI()))
            ->assertStatus(200);
            
        $EID_Segment = factory(EID_Segment::class)->make();
        
        $attributes = [
            'rcsSFI' => $notification->get_RCS_SFI(),
            'plant_code' => $notification->plant_code,
            'AET' => $EID_Segment->get_EID_AET(),
        	'EPC' => $EID_Segment->get_EID_EPC(),
        	'AEM' => $EID_Segment->get_EID_AEM(),
        	'EMS' => $EID_Segment->get_EID_EMS(),
        	'MFR' => $EID_Segment->get_EID_MFR(),
        	'ETH' => $EID_Segment->get_EID_ETH(),
        	'ETC' => $EID_Segment->get_EID_ETC(),
        ];
        
        $response = $this->actingAs($this->adminUser)
            ->call('POST', route('engine-information.update', $notification->get_RCS_SFI()), $attributes);
            
        $response->assertStatus(302);
        
        $this->get($response->headers->get('Location'))
            ->assertSee('Engine Information saved successfully!');
        
        unset($attributes['rcsSFI']); // Not recorded in DB.
        unset($attributes['plant_code']); // Not recorded in DB.
        
        $this->assertDatabaseHas('EID_Segments', $attributes);
    }
    
    /**
     * Assert the session has errors if an empty form is submitted.
     *
     * @return void
     */
    public function testInvalidEIDFormSubmit()
    {
        $notification = $this->getEditableNotification($this->user);
        
        $attributes = [
            'rcsSFI' => $notification->get_RCS_SFI(),
        ];
        
        $response = $this->actingAs($this->user)
            ->call('POST', route('engine-information.update', $notification->get_RCS_SFI()), $attributes);
        
        $response->assertSessionHasErrors(['AET', 'EPC', 'AEM']);
    }
    
    /**
     * Test the header form request validates.
     *
     * @return void
     */
    public function testEditEID_Segment()
    {
        $notification = $this->getEditableNotification($this->user);
        
        $EID_Segment = factory(EID_Segment::class)->make();
        
        $attributes = [
            'rcsSFI' => $notification->get_RCS_SFI(),
            'plant_code' => $notification->plant_code,
            'AET' => $EID_Segment->get_EID_AET(),
        	'EPC' => $EID_Segment->get_EID_EPC(),
        	'AEM' => $EID_Segment->get_EID_AEM(),
        	'EMS' => $EID_Segment->get_EID_EMS(),
        	'MFR' => $EID_Segment->get_EID_MFR(),
        	'ETH' => $EID_Segment->get_EID_ETH(),
        	'ETC' => $EID_Segment->get_EID_ETC(),
        ];
        
        $response = $this->actingAs($this->user)
            ->call('POST', route('engine-information.update', $notification->get_RCS_SFI()), $attributes);
            
        $errors = session('errors');
        
        if (!empty($errors)) {
            mydd($attributes);
            mydd($errors);
        }
            
        $response->assertStatus(302);
            
        $this->get($response->headers->get('Location'))
            ->assertSee('Engine Information saved successfully!');
        
        unset($attributes['rcsSFI']); // Not recorded in DB.
        unset($attributes['plant_code']); // Not recorded in DB.
        
        $this->assertDatabaseHas('EID_Segments', $attributes);
    }
    
    /**
     * Test that the segment can be deleted.
     *
     * @return void
     */
    public function testDeleteEID_Segment()
    {
        $this->createSingleShopFindingAndPiecePartsWithAllSegments(1, $this->adminUser);
        
        $segment = EID_Segment::inRandomOrder()->first();
        
        $segmentId = $segment->id;
        
        $this->actingAs($this->adminUser)
            ->ajaxPost(route('engine-information.destroy', $segmentId))
            ->assertStatus(200);
            
        $this->assertDatabaseMissing('EID_Segments', ['id' => $segmentId]);
    }
    
    /**
     * Test that form fields are pre-filled correctly when airframe information has data.
     *
     * @return void
     */
    public function testEID_SegmentEngineDetailsPrefill()
    {
        $notificationId = $this->faker->unique()->numberBetween(100000, 9999999999);
        
        // Create a valid notification with all segments.
        $notification = factory(Notification::class)
            ->states('all_segments_real_arcraft_data')
            ->create([
            'id' => $notificationId,
            'rcsSFI' => $notificationId,
            'status' => 'complete_shipped',
            'shipped_at' => Carbon::now()
        ]);
        
        $aircraft = AircraftDetail::where('aircraft_fully_qualified_registration_no', $notification->get_AID_REG())->first();
        
        $this->actingAs($this->adminUser)
            ->call('GET', route('engine-information.edit', $notificationId))
            ->assertSee($aircraft->engine_type)
            ->assertSee($aircraft->engines_series)
            ->assertSee($aircraft->engine_position_identifier)
            ->assertStatus(200);
            
        // Save AID segment with different aircraft data and reload EID segment form expecting to see different data.
        
        $AID_Segment = factory(AID_Segment::class)->states('with_real_aircraft_data')->make();
        
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
            
        $aircraft = AircraftDetail::where('aircraft_fully_qualified_registration_no', $AID_Segment->get_AID_REG())->first();
            
        $this->actingAs($this->adminUser)
            ->call('GET', route('engine-information.edit', $notificationId))
            ->assertSee($aircraft->engine_type)
            ->assertSee($aircraft->engines_series)
            ->assertSee($aircraft->engine_position_identifier)
            ->assertStatus(200);
    }
}
