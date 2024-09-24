<?php

namespace Tests\Feature;

use App\User;
use App\Activity;
use App\Location;
use App\PartList;
use Tests\TestCase;
use App\HDR_Segment;
use App\Notification;
use App\Events\PartListSaved;
use Illuminate\Support\Facades\Event;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ActivityTest extends TestCase
{
    /**
     * Create a header segment and update a user.
     * Test the activities index response is 200.
     * Test both activities are displayed.
     *
     * @return void
     */
    public function testAllActivitiesPage()
    {
        $user = User::with('location')
            ->where('role_id', 1)
            ->inRandomOrder()
            ->firstOrFail();
        
        $notification = Notification::where('plant_code', $user->location->plant_code)
            ->inRandomOrder()
            ->first();
        
        $header = factory(HDR_Segment::class)->make(['RON' => $user->location->name]);
        
        $attributes = [
            'rcsSFI' => $notification->get_RCS_SFI(),
            'plant_code' => $notification->plant_code,
            'CHG' => $header->get_HDR_CHG(),
            'ROC' => $header->get_HDR_ROC(),
            'RDT' => $header->get_HDR_RDT(),
            'RSD' => $header->get_HDR_RSD(),
            'OPR' => $header->get_HDR_OPR(),
            'RON' => $header->get_HDR_RON(),
            'WHO' => $header->get_HDR_WHO(),
        ];
        
        $response =  $this->actingAs($user)
            ->call('POST', route('header.update', $notification->get_RCS_SFI()), $attributes);
        
        $errors = session('errors');
    
        if (!empty($errors)) {
            mydd($errors);
            mydd($attributes);
            //die('Killed test.');
        }
        
        $this->get($response->headers->get('Location'))
            ->assertSee('Header saved successfully!');
            
        $dataAdmin = User::where('location_id', $user->location_id)
            ->where('role_id', 4)
            ->inRandomOrder()
            ->first();
            
        $attributes = $user->toArray();
        
        $attributes['first_name'] = $this->faker->firstName;
        $attributes['last_name'] = $this->faker->lastName;
        
        unset($attributes['fullname']);
        unset($attributes['remember_token']);
        unset($attributes['password']);
        unset($attributes['created_at']);
        unset($attributes['updated_at']);
        unset($attributes['acronym']);
        
        $response2 = $this->actingAs($dataAdmin)->call('PUT', route('user.update', $user->id), $attributes);
            
        $errors = session('errors');
    
        if (!empty($errors)) {
            mydd($errors);
            mydd($attributes);
            //die('Killed test.');
        }
        
        $this->get($response2->headers->get('Location'))
            ->assertSee('User updated successfully!');
        
        // Assert data admin user can see all activities.
        $this->actingAs($dataAdmin)->get(route('activity.index'))
            ->assertStatus(200)
            ->assertSee('Created HDR Segment')
            ->assertSee('Updated User');
            
        $siteAdmin = User::with('location')->where('location_id', $user->location_id)
            ->where('role_id', 3)
            ->inRandomOrder()
            ->firstOrFail();
            
        // Assert site admin user can see the activity.
        $this->actingAs($siteAdmin)->get(route('activity.index'))
            ->assertStatus(200)
            ->assertSee('Created HDR Segment');
    }
    
    /**
     * A data admin and site admin user can view another user's activities.
     *
     * @return void
     */
    public function testDataAdminAndSiteAdminCanViewAUsersActivities()
    {
        $user = User::with('location')
            ->where('role_id', 1)
            ->inRandomOrder()
            ->firstOrFail();
            
        $siteAdmin = User::with('location')
            ->where('location_id', $user->location_id)
            ->where('role_id', 3)
            ->inRandomOrder()
            ->firstOrFail();
        
        $notification = Notification::where('plant_code', $user->location->plant_code)
            ->inRandomOrder()
            ->first();
        
        $header = factory(HDR_Segment::class)->make();
        
        $attributes = [
            'rcsSFI' => $notification->get_RCS_SFI(),
            'plant_code' => $notification->plant_code,
            'CHG' => $header->get_HDR_CHG(),
            'ROC' => $header->get_HDR_ROC(),
            'RDT' => $header->get_HDR_RDT(),
            'RSD' => $header->get_HDR_RSD(),
            'OPR' => $header->get_HDR_OPR(),
            'RON' => $header->get_HDR_RON(),
            'WHO' => $header->get_HDR_WHO(),
        ];
        
        $response =  $this->actingAs($user)
            ->call('POST', route('header.update', $notification->get_RCS_SFI()), $attributes);
        
        $this->get($response->headers->get('Location'))
            ->assertSee('Header saved successfully!');
        
        $this->actingAs($siteAdmin)->get(route('activity.show', $user->id))
            ->assertStatus(200)
            ->assertSee('Created HDR Segment');
            
        $this->actingAs($this->dataAdminUser)->get(route('activity.show', $user->id))
            ->assertStatus(200)
            ->assertSee('Created HDR Segment');
            
        $otherSiteAdmin = User::with('location')
            ->where('location_id', '!=', $user->location_id)
            ->where('role_id', 3)
            ->inRandomOrder()
            ->firstOrFail();
            
        // Assert site admin from another site can't see the page.
        $this->actingAs($otherSiteAdmin)->get(route('activity.show', $user->id))
            ->assertStatus(403);
    }
    
    /**
     * An admin user can view another user's activities from the same location.
     *
     * @return void
     */
    public function testAdminCanViewAUsersActivitiesFromTheSameLocation()
    {
        $user = User::with('location')
            ->where('role_id', 1)
            ->inRandomOrder()
            ->firstOrFail();
            
        $admin = User::with('location')
            ->where('location_id', $user->location_id)
            ->where('role_id', 2)
            ->inRandomOrder()
            ->firstOrFail();
        
        $notification = Notification::where('plant_code', $user->location->plant_code)
            ->inRandomOrder()
            ->first();
        
        $header = factory(HDR_Segment::class)->make();
        
        $attributes = [
            'rcsSFI' => $notification->get_RCS_SFI(),
            'plant_code' => $notification->plant_code,
            'CHG' => $header->get_HDR_CHG(),
            'ROC' => $header->get_HDR_ROC(),
            'RDT' => $header->get_HDR_RDT(),
            'RSD' => $header->get_HDR_RSD(),
            'OPR' => $header->get_HDR_OPR(),
            'RON' => $header->get_HDR_RON(),
            'WHO' => $header->get_HDR_WHO(),
        ];
        
        $response =  $this->actingAs($user)
            ->call('POST', route('header.update', $notification->get_RCS_SFI()), $attributes);
        
        $this->get($response->headers->get('Location'))
            ->assertSee('Header saved successfully!');
        
        $this->actingAs($admin)->get(route('activity.show', $user->id))
            ->assertStatus(200)
            ->assertSee('Created HDR Segment');
            
        $otherAdmin = User::with('location')
            ->where('location_id', '!=', $user->location_id)
            ->where('role_id', 2)
            ->inRandomOrder()
            ->firstOrFail();
            
        // Assert site admin from another site can't see the page.
        $this->actingAs($otherAdmin)->get(route('activity.show', $user->id))
            ->assertStatus(403);
    }
    
    /**
     * An admin user can view another user's activities.
     *
     * @return void
     */
    public function testAUserCanViewTheirOwnActivities()
    {
        $user = User::with('location')
            ->where('role_id', 1)
            ->inRandomOrder()
            ->firstOrFail();
        
        $notification = Notification::where('plant_code', $user->location->plant_code)
            ->inRandomOrder()
            ->first();
        
        $header = factory(HDR_Segment::class)->make();
        
        $attributes = [
            'rcsSFI' => $notification->get_RCS_SFI(),
            'plant_code' => $notification->plant_code,
            'CHG' => $header->get_HDR_CHG(),
            'ROC' => $header->get_HDR_ROC(),
            'RDT' => $header->get_HDR_RDT(),
            'RSD' => $header->get_HDR_RSD(),
            'OPR' => $header->get_HDR_OPR(),
            'RON' => $header->get_HDR_RON(),
            'WHO' => $header->get_HDR_WHO(),
        ];
        
        $response =  $this->actingAs($user)
            ->call('POST', route('header.update', $notification->get_RCS_SFI()), $attributes);
        
        $this->get($response->headers->get('Location'))
            ->assertSee('Header saved successfully!');
        
        $this->actingAs($user)->get(route('activity.show-my-activity'))
            ->assertStatus(200)
            ->assertSee('Created HDR Segment');
    }
    
    /**
     * Test that a user who has activities can be deleted.
     *
     * @return void
     */
    public function testDeleteUserWithActivities()
    {
        $this->markTestSkipped('Deleting user is currently disabled.');
        
        $user = User::with('location')
            ->where('role_id', 1)
            ->inRandomOrder()
            ->firstOrFail();
            
        $siteAdmin = User::with('location')
            ->where('location_id', $user->location_id)
            ->where('role_id', 3)
            ->inRandomOrder()
            ->firstOrFail();
            
        $otherSiteAdmin = User::with('location')
            ->where('location_id', '!=', $user->location_id)
            ->where('role_id', 3)
            ->inRandomOrder()
            ->firstOrFail();
        
        $notification = Notification::where('plant_code', $user->location->plant_code)
            ->inRandomOrder()
            ->first();
        
        $header = factory(HDR_Segment::class)->make(['RON' => $user->location->name]);
        
        $attributes = [
            'rcsSFI' => $notification->get_RCS_SFI(),
            'plant_code' => $notification->plant_code,
            'CHG' => $header->get_HDR_CHG(),
            'ROC' => $header->get_HDR_ROC(),
            'RDT' => $header->get_HDR_RDT(),
            'RSD' => $header->get_HDR_RSD(),
            'OPR' => $header->get_HDR_OPR(),
            'RON' => $header->get_HDR_RON(),
            'WHO' => $header->get_HDR_WHO(),
        ];
        
        $response =  $this->actingAs($user)
            ->call('POST', route('header.update', $notification->get_RCS_SFI()), $attributes);
        
        $errors = session('errors');
    
        if (!empty($errors)) {
            mydd($errors);
            mydd($attributes);
            //die('Killed test.');
        }
        
        $this->get($response->headers->get('Location'))
            ->assertSee('Header saved successfully!');
        
        $response = $this->actingAs($this->dataAdminUser)
            ->call('DELETE', route('user.destroy', $user->id));
        
        $errors = session('errors');
    
        if (!empty($errors)) {
            mydd($errors);
            mydd($attributes);
            //die('Killed test.');
        }
        
        $this->get($response->headers->get('Location'))
            ->assertSee('User deleted successfully!')
            ->assertStatus(200);
            
        $this->actingAs($siteAdmin)->get(route('activity.index'))
            ->assertStatus(200)
            ->assertSee('Created HDR Segment');
            
        $this->actingAs($this->dataAdminUser)->get(route('activity.index'))
            ->assertStatus(200)
            ->assertSee('Deleted User')
            ->assertSee('Created HDR Segment');
            
        $this->actingAs($otherSiteAdmin)->get(route('activity.index'))
            ->assertStatus(200)
            ->assertDontSee('Created HDR Segment');
    }
    
    /**
     * Test that a site admin can create a part list and it is visible in their activities.
     *
     * @return void
     */
    public function testPartlistActivities()
    {
        // Clean up left over part lists
        $partLists = PartList::get();
        
        if ($partLists->count()) {
            foreach ($partLists as $partList) {
                $partList->delete();
            }
        }
        
        $location = Location::where('plant_code', $this->siteAdminUser->location->plant_code)->first();
        
        $this->actingAs($this->siteAdminUser)
            ->get(route('part-list.create', $location))
            ->assertSee('Create Excluded Part Number List')
            ->assertStatus(200);
            
        $attributes = factory(PartList::class)->raw(['location_id' => $location->id]);
        
        $response = $this->actingAs($this->siteAdminUser)
            ->call('post', route('part-list.store', $location), $attributes)
            ->assertSessionHas('alert.message', 'New excluded part numbers list created successfully!')
            ->assertStatus(302);
            
        $this->assertDatabaseHas('part_lists', $attributes);
        
        $this->actingAs($this->siteAdminUser)->get(route('activity.index'))
            ->assertStatus(200)
            ->assertSee('Created PartList');
            
        $this->actingAs($this->dataAdminUser)->get(route('activity.index'))
            ->assertStatus(200)
            ->assertSee('Created PartList');
    }
}
