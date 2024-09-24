<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\UtasCode as Utas;
use App\Codes\UtasReasonCode;
use App\ShopFindings\Misc_Segment;
use App\Notification;
use App\ValidationProfiles\UtasProfile;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class UtasMisc_SegmentTest extends TestCase
{
    /**
     * Test that the Utas Fields form is available.
     *
     * @return void
     */
    public function testUtasFieldsForm()
    {
        $notification = $this->getUtasNotification($this->user);
        
        $this->actingAs($this->user)->call('GET', route('misc-segment.edit', $notification->get_RCS_SFI()))
            ->assertStatus(200)
            ->assertSee(UtasProfile::MISC_SEGMENT_NAME);
    }
    
    /**
     * Test user can't edit or update segment from another location.
     *
     * @return void
     */
    public function testUserCantEditOrUpdateMisc_SegmentForOtherLocation()
    {
        $notification = $this->getUtasNotificationForOtherLocation($this->user);
        
        $this->actingAs($this->user)
            ->call('GET', route('misc-segment.edit', $notification->get_RCS_SFI()))
            ->assertStatus(403);
            
        $utasCodes = Utas::inRandomOrder()->first();
        $utasReasonCodes = UtasReasonCode::where('TYPE', $notification->get_RCS_RRC())
            ->inRandomOrder()
            ->first();
        
        $attributes = [
            'plant_code' => $notification->plant_code,
            'Type' => $utasReasonCodes->TYPE,
            'Plant' => $utasReasonCodes->PLANT,
            'PartNo' => $notification->get_RCS_MPN(),
            'Reason' => $utasReasonCodes->REASON,
            'rcsSFI' => $notification->get_RCS_SFI(),
            'Comments' => $this->faker->optional()->word,
            'Modifier' => $this->faker->optional()->word,
            'Component' => $utasCodes->COMP,
            'FeatureName' => $utasCodes->FEAT ?: NULL,
            'SubassemblyName' => $utasCodes->SUB,
            'FailureDescription' => $utasCodes->DESCR
        ];
        
        $response = $this->actingAs($this->user)
            ->call('POST', route('misc-segment.update', $notification->get_RCS_SFI()), $attributes)
            ->assertStatus(403);
    }
    
    /**
     * Test admin can edit and update segment for another location.
     *
     * @return void
     */
    public function testAdminCanEditAndUpdateMisc_SegmentForOtherLocation()
    {
        $notification = $this->getUtasNotificationForOtherLocation($this->adminUser);
        
        $this->actingAs($this->adminUser)
            ->call('GET', route('misc-segment.edit', $notification->get_RCS_SFI()))
            ->assertStatus(200)
            ->assertSee(UtasProfile::MISC_SEGMENT_NAME);
            
        $utasCodes = Utas::inRandomOrder()->first();
        $utasReasonCodes = UtasReasonCode::where('TYPE', $notification->get_RCS_RRC())
            ->inRandomOrder()
            ->first();
            
        //mydd($utasReasonCodes);
        
        $attributes = [
            'plant_code' => $notification->plant_code,
            'Type' => $utasReasonCodes->TYPE,
            'Plant' => $utasReasonCodes->PLANT,
            'PartNo' => $notification->get_RCS_MPN(),
            'Reason' => $utasReasonCodes->REASON,
            'rcsSFI' => $notification->get_RCS_SFI(),
            'Comments' => $this->faker->optional()->word,
            'Modifier' => $this->faker->optional()->word,
            'Component' => $utasCodes->COMP,
            'FeatureName' => $utasCodes->FEAT ?: NULL,
            'SubassemblyName' => $utasCodes->SUB,
            'FailureDescription' => $utasCodes->DESCR
        ];
        
        $response = $this->actingAs($this->adminUser)
            ->call('POST', route('misc-segment.update', $notification->get_RCS_SFI()), $attributes)
            ->assertStatus(302);
            
        $errors = session('errors');
        
        //mydd($errors,1);
            
        $this->get($response->headers->get('Location'))->assertSee('Misc segment saved successfully!');
        
        $id = $notification->get_RCS_SFI();
        
        $saved = Misc_Segment::where('values', 'LIKE', "%$id%")->first();
        
        $this->assertTrue(!is_null($saved));
    }
    
    /**
     * Test Utas Fields validate and store in DB.
     *
     * @return void
     */
    public function testUtasSegmentValidation()
    {
        $notification = $this->getUtasNotification($this->user);
        $utasCodes = Utas::inRandomOrder()->first();
        $utasReasonCodes = UtasReasonCode::where('TYPE', $notification->get_RCS_RRC())->inRandomOrder()->first();
        
        $attributes = [
            'plant_code' => $notification->plant_code,
            'Type' => $utasReasonCodes->TYPE,
            'Plant' => $utasReasonCodes->PLANT,
            'PartNo' => $notification->get_RCS_MPN(),
            'Reason' => $utasReasonCodes->REASON,
            'rcsSFI' => $notification->get_RCS_SFI(),
            'Comments' => $this->faker->optional()->word,
            'Modifier' => $this->faker->optional()->word,
            'Component' => $utasCodes->COMP,
            'FeatureName' => $utasCodes->FEAT ?: NULL,
            'SubassemblyName' => $utasCodes->SUB,
            'FailureDescription' => $utasCodes->DESCR
        ];
        
        $response = $this->actingAs($this->user)
            ->call('POST', route('misc-segment.update', $notification->get_RCS_SFI()), $attributes)
            ->assertStatus(302);
            
        $errors = session('errors');
        
        if (!empty($errors)) {
            var_dump($attributes);
            mydd($errors);
        }
            
        $this->get($response->headers->get('Location'))->assertSee('Misc segment saved successfully!');
        
        $id = $notification->get_RCS_SFI();
        
        $saved = Misc_Segment::where('values', 'LIKE', "%$id%")->first();
        
        $this->assertTrue(!is_null($saved));
    }
    
    /**
     * Get a notification that contains a Utas part.
     *
     * @return \App\Notification
     */
    public function getUtasNotification($user = NULL)
    {
        if (!$user) {
            $user = $this->user;
        }
        
        $utasParts = Utas::getAllUtasCodes();
        
        $notifications = Notification::whereIn('rcsRRC', ['U', 'S'])
            ->where('plant_code', $user->location->plant_code)
            ->whereIn('rcsMPN', $utasParts)
            ->orderBy('rcsSFI', 'asc')
            ->get();
        
        return collect($notifications)->random(); // Get a random Notification from collection.
    }
    
    /**
     * Get a notification that contains a Utas part.
     *
     * @return \App\Notification
     */
    public function getUtasNotificationForOtherLocation($user = NULL)
    {
        if (!$user) {
            $user = $this->user;
        }
        
        $utasParts = Utas::getAllUtasCodes();
        
        return Notification::whereIn('rcsRRC', ['U', 'S'])
            ->where('plant_code', '!=', $user->location->plant_code)
            ->whereIn('rcsMPN', $utasParts)
            ->orderBy('rcsSFI', 'asc')
            ->inRandomOrder()
            ->first();
    }
    
    /**
     * Test that the segment can be deleted.
     *
     * @return void
     */
    public function testDeleteMISC_Segment()
    {
        $this->createSingleShopFindingAndPiecePartsWithAllSegments(1, $this->adminUser);
        
        $segment = MISC_Segment::inRandomOrder()->first();
        
        $segmentId = $segment->id;
        
        $this->actingAs($this->adminUser)
            ->ajaxPost(route('misc-segment.destroy', $segmentId))
            ->assertStatus(200);
            
        $this->assertDatabaseMissing('Misc_Segments', ['id' => $segmentId]);
    }
}
