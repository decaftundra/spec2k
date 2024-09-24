<?php

namespace Tests\Feature;

use App\User;
use Carbon\Carbon;
use Tests\TestCase;
use App\HDR_Segment;
use App\ShopFindings\ShopFinding;
use Illuminate\Support\Facades\Event;
use App\Events\NotificationStatusUpdating;
use Illuminate\Foundation\Testing\WithFaker;
use App\Events\NotificationPlannerGroupUpdating;
use Illuminate\Foundation\Testing\RefreshDatabase;

class HDR_SegmentTest extends TestCase
{
    /**
     * Test the header form response is 200.
     *
     * @return void
     */
    public function testHDR_SegmentForm()
    {
        $notification = $this->getEditableNotification($this->user);
        
        $this->actingAs($this->user)->call('GET', route('header.edit', $notification->get_RCS_SFI()))->assertStatus(200);
    }
    
    /**
     * Test a user cant edit or update a segment from another location.
     *
     * @return void
     */
    public function testUserCantEditOrUpdateHDR_SegmentFromOtherLocation()
    {
        $notification = $this->getNotificationNotInUsersLocation($this->user);
        
        $this->actingAs($this->user)
            ->call('GET', route('header.edit', $notification->get_RCS_SFI()))
            ->assertStatus(403);
            
        $header = factory(HDR_Segment::class)->make();
            
        $attributes = [
            'rcsSFI' => $notification->get_RCS_SFI(),
            'plant_code' => $notification->plant_code,
            'CHG' => $header->get_HDR_CHG(),
            'ROC' => $header->get_HDR_ROC(),
            'OPR' => $header->get_HDR_OPR(),
            'RON' => $header->get_HDR_RON(),
            'WHO' => $header->get_HDR_WHO(),
        ];
        
        $response = $this->actingAs($this->user)
            ->call('POST', route('header.update', $notification->get_RCS_SFI()), $attributes);
        
        $response->assertStatus(403);
    }
    
    /**
     * Test a user cant edit or update a segment from another location.
     *
     * @return void
     */
    public function testAdminCanEditOrUpdateHDR_SegmentFromOtherLocation()
    {
        $notification = $this->getNotificationNotInUsersLocation($this->adminUser);
        
        $this->actingAs($this->adminUser)
            ->call('GET', route('header.edit', $notification->get_RCS_SFI()))
            ->assertStatus(200);
            
        $header = factory(HDR_Segment::class)->make();
            
        $attributes = [
            'rcsSFI' => $notification->get_RCS_SFI(),
            'plant_code' => $notification->plant_code,
            'CHG' => $header->get_HDR_CHG(),
            'ROC' => $header->get_HDR_ROC(),
            'OPR' => $header->get_HDR_OPR(),
            'RON' => $header->get_HDR_RON(),
            'WHO' => $header->get_HDR_WHO(),
        ];
        
        $response = $this->actingAs($this->adminUser)
            ->call('POST', route('header.update', $notification->get_RCS_SFI()), $attributes);
        
        $response->assertStatus(302);
        
        $this->get($response->headers->get('Location'))
            ->assertSee('Header saved successfully!');
        
        unset($attributes['rcsSFI']); // Not recorded in DB.
        unset($attributes['plant_code']); // Not recorded in DB.
        
        $this->assertDatabaseHas('HDR_Segments', $attributes);
    }
    
    /**
     * Assert the session has errors if an empty form is submitted.
     *
     * @return void
     */
    public function testInvalidHDRFormSubmit()
    {
        $notification = $this->getEditableNotification($this->user);
        
        $attributes = [
            'rcsSFI' => $notification->get_RCS_SFI(),
            'plant_code' => $notification->plant_code,
        ];
        
        $response = $this->actingAs($this->user)
            ->call('POST', route('header.update', $notification->get_RCS_SFI()), $attributes);
        
        $response->assertSessionHasErrors(['CHG', 'ROC', 'OPR']);
    }
    
    /**
     * Test the header form request validates, and a header is created.
     *
     * @return void
     */
    public function testEditHDR_Segment()
    {
        $notification = $this->getEditableNotification($this->user);
        
        // Save a planner group and status to the notification.
        $notification->planner_group = $this->user->planner_group;
        $notification->setStatus('subcontracted');
        $notification->save();
        
        $header = factory(HDR_Segment::class)->make();
        
        $attributes = [
            'rcsSFI' => $notification->get_RCS_SFI(),
            'plant_code' => $notification->plant_code,
            'CHG' => $header->get_HDR_CHG(),
            'ROC' => $header->get_HDR_ROC(),
            'OPR' => $header->get_HDR_OPR(),
            'RON' => $header->get_HDR_RON(),
            'WHO' => $header->get_HDR_WHO(),
        ];
        
        $response = $this->actingAs($this->user)
            ->call('POST', route('header.update', $notification->get_RCS_SFI()), $attributes);
        
        $response->assertStatus(302);
        
        $errors = session('errors');
        
        if (!empty($errors)) {
            mydd($errors);
            var_dump($attributes);
            //dd('Validation Errors, see above.');
        }
            
        $this->get($response->headers->get('Location'))
            ->assertSee('Header saved successfully!');
        
        unset($attributes['rcsSFI']); // Not recorded in DB.
        unset($attributes['plant_code']); // Not recorded in DB.
        unset($attributes['RDT']);
        unset($attributes['RSD']);
        
        $shopFindingAttributes = [
            'id' => $notification->id,
            'planner_group' => $this->user->planner_group,
            'status' => 'subcontracted'
        ];
        
        // This basically asserts that the model events have all been fired, as we can't seemingly test those.
        $this->assertDatabaseHas('shop_findings', $shopFindingAttributes);
        $this->assertDatabaseHas('HDR_Segments', $attributes);
        
        // Get a user with a different planner group
        $otherUser = User::where('planner_group', '!=', $this->user->planner_group)->first();
        
        // Update the planner group and status in the notification.
        $notification->planner_group = $otherUser->planner_group;
        $notification->setStatus('complete_shipped');
        $notification->save();
        
        $updatedAttributes = [
            'id' => $notification->id,
            'planner_group' => $otherUser->planner_group,
            'status' => 'complete_shipped'
        ];
        
        // This basically asserts that the model events have all been fired, as we can't seemingly test those.
        $this->assertDatabaseHas('shop_findings', $updatedAttributes);
    }
    
    /**
     * Test that the segment can be deleted.
     *
     * @return void
     */
    public function testDeleteHDR_Segment()
    {
        $this->createSingleShopFindingAndPiecePartsWithAllSegments(1, $this->adminUser);
        
        $segment = HDR_Segment::inRandomOrder()->first();
        
        $segmentId = $segment->id;
        
        $this->actingAs($this->adminUser)
            ->ajaxPost(route('header.destroy', $segmentId))
            ->assertStatus(200);
            
        $this->assertDatabaseMissing('HDR_Segments', ['id' => $segmentId]);
    }
}
