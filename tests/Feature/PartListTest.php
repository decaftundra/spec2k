<?php

namespace Tests\Feature;

use App\PartList;
use App\Location;
use Tests\TestCase;
use App\Events\PartListSaved;
use App\Events\PartListDeleted;
use App\ShopFindings\EID_Segment;
use App\ShopFindings\RCS_Segment;
use App\ShopFindings\ShopFinding;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Event;
use App\ScopesShopFindingPartListScope;
use App\Notification;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class PartListTest extends TestCase
{
    /**
     * Test a normal user can access any of the part list routes.
     *
     * @return void
     */
    public function testUserCantAccessPartListRoutes()
    {
        $this->actingAs($this->user)
            ->get(route('part-list.index'))
            ->assertStatus(403);
        
        $location = Location::where('plant_code', $this->user->location->plant_code)->first();
        
        $attributes = factory(PartList::class)->raw();
        
        $this->actingAs($this->user)
            ->get(route('part-list.create', $location))
            ->assertStatus(403);
            
        $this->actingAs($this->user)
            ->call('post', route('part-list.store', $location), $attributes)
            ->assertStatus(403);
            
        $partList = factory(PartList::class)->create();
            
        $this->actingAs($this->user)
            ->get(route('part-list.edit', $partList))
            ->assertStatus(403);
            
        $this->actingAs($this->user)
            ->call('put', route('part-list.update', $partList), $attributes)
            ->assertStatus(403);
            
        $this->actingAs($this->user)
            ->get(route('part-list.delete', $partList))
            ->assertStatus(403);
            
        $this->actingAs($this->user)
            ->call('delete', route('part-list.destroy', $partList))
            ->assertStatus(403);
    }
    
    /**
     * Test that a site adin can view the part list index page.
     *
     * @return void
     */
    public function testSiteAdminCanViewPartListIndex()
    {
        $otherLocation = Location::where('plant_code', '!=', $this->siteAdminUser->location->plant_code)
            ->inRandomOrder()
            ->first();
        
        $this->actingAs($this->siteAdminUser)
            ->get(route('part-list.index'))
            ->assertSee('Displaying 1 to 1 of 1 excluded part number lists.')
            ->assertDontSee($otherLocation->name)
            ->assertSee($this->siteAdminUser->location->name)
            ->assertStatus(200);
    }
    
    /**
     * Test that a site admin can create a parts list for their site.
     *
     * @return void
     */
    public function testSiteAdminCanCreateTheirOwnPartList()
    {
        // Clean up left over part lists
        $partLists = PartList::get();
        
        if ($partLists->count()) {
            foreach ($partLists as $partList) {
                $partList->delete();
            }
        }
        
        Event::fake();
        
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
            
        Event::assertDispatched(PartListSaved::class);
            
        $this->assertDatabaseHas('part_lists', $attributes);
    }
    
    /**
     * Test a site admin can update their part list.
     *
     * @return void
     */
    public function testSiteAdminCanUpdateTheirOwnPartList()
    {
        Event::fake();
        
        $location = Location::where('plant_code', $this->siteAdminUser->location->plant_code)->first();
        
        $partList = factory(PartList::class)->create(['location_id' => $location->id]);
        
        $this->actingAs($this->siteAdminUser)
            ->get(route('part-list.edit', $partList))
            ->assertSee('Edit Excluded Part Number List')
            ->assertStatus(200);
            
        $attributes = factory(PartList::class)->raw();
        
        $attributes['location_id'] = $location->id;
        
        $response = $this->actingAs($this->siteAdminUser)
            ->call('put', route('part-list.update', $partList), $attributes)
            ->assertSessionHas('alert.message', 'Excluded part numbers list updated successfully!')
            ->assertStatus(302);
            
        Event::assertDispatched(PartListSaved::class);
            
        $this->assertDatabaseHas('part_lists', $attributes);
    }
    
    /**
     * Test that a data admin can view the part list index.
     *
     * @return void
     */
    public function testDataAdminCanViewPartListIndex()
    {
        $location = Location::inRandomOrder()->first();
        
        $this->actingAs($this->dataAdminUser)
            ->get(route('part-list.index'))
            ->assertSee('Excluded Part Numbers')
            ->assertSee($location->name)
            ->assertStatus(200);
    }
    
    /**
     * Test a data admin can create a new part list.
     *
     * @return void
     */
    public function testDataAdminCanCreateNewPartList()
    {
        // Clean up left over part lists
        $partLists = PartList::get();
        
        if ($partLists->count()) {
            foreach ($partLists as $partList) {
                $partList->delete();
            }
        }
        
        Event::fake();
        
        $location = Location::whereDoesntHave('part_list')
            ->inRandomOrder()
            ->first();
        
        $this->actingAs($this->dataAdminUser)
            ->get(route('part-list.create', $location))
            ->assertSee('Create Excluded Part Number List')
            ->assertStatus(200);
            
        $attributes = factory(PartList::class)->raw(['location_id' => $location->id]);
        
        $response = $this->actingAs($this->dataAdminUser)
            ->call('post', route('part-list.store', $location), $attributes)
            ->assertSessionHas('alert.message', 'New excluded part numbers list created successfully!')
            ->assertStatus(302);
            
        $errors = session('errors');
    
        if (!empty($errors)) {
            mydd($errors);
            mydd($attributes);
            //die('Killed test.');
        }
            
        Event::assertDispatched(PartListSaved::class);
            
        $this->assertDatabaseHas('part_lists', $attributes);
    }
    
    /**
     * Test a data admin can update an existing part list.
     *
     * @return void
     */
    public function testDataAdminCanUpdatePartList()
    {
        Event::fake();
        
        $partList = factory(PartList::class)->create();
        
        $this->actingAs($this->dataAdminUser)
            ->get(route('part-list.edit', $partList))
            ->assertSee('Edit Excluded Part Number List')
            ->assertStatus(200);
            
        $attributes = factory(PartList::class)->raw(['location_id' => $partList->location_id]);
        
        $response = $this->actingAs($this->dataAdminUser)
            ->call('put', route('part-list.update', $partList), $attributes)
            ->assertSessionHas('alert.message', 'Excluded part numbers list updated successfully!')
            ->assertStatus(302);
            
        $errors = session('errors');
    
        if (!empty($errors)) {
            mydd($errors);
            mydd($attributes);
            //die('Killed test.');
        }
            
        Event::assertDispatched(PartListSaved::class);
            
        $this->assertDatabaseHas('part_lists', $attributes);
    }
    
    /**
     * Test that data admin can't delete a location that has related users.
     *
     * @return void
     */
    public function testDataAdminCanDeletePartList()
    {
        Event::fake();
        
        $partList = factory(PartList::class)->create();
        
        $this->actingAs($this->dataAdminUser)
            ->get(route('part-list.delete', $partList))
            ->assertSee('Delete Excluded Part Number List')
            ->assertStatus(200);
            
        $response = $this->actingAs($this->dataAdminUser)
            ->call('delete', route('part-list.destroy', $partList))
            ->assertSessionHas('alert.message', 'Excluded part numbers list deleted successfully!')
            ->assertStatus(302);
            
        Event::assertDispatched(PartListDeleted::class);
    }
    
    /**
     * Test site admin can delete their own part list.
     *
     * @return void
     */
    public function testSiteAdminCanDeleteTheirOwnPartList()
    {
        Event::fake();
        
        $partList = factory(PartList::class)->create(['location_id' => $this->siteAdminUser->location->id]);
        
        $this->actingAs($this->siteAdminUser)
            ->get(route('part-list.delete', $partList))
            ->assertSee('Delete Excluded Part Number List')
            ->assertStatus(200);
            
        $response = $this->actingAs($this->siteAdminUser)
            ->call('delete', route('part-list.destroy', $partList))
            ->assertSessionHas('alert.message', 'Excluded part numbers list deleted successfully!')
            ->assertStatus(302);
            
        Event::assertDispatched(PartListDeleted::class);
    }
     
    /**
     * Test site admin can't delete a part list for another location.
     *
     * @return void
     */
    public function testSiteAdminCantDeleteAPartListFromOtherLocation()
    {
        Event::fake();
        
        $otherLocation = Location::where('id', '!=', $this->siteAdminUser->location->id)
            ->inRandomOrder()
            ->first();
        
        $partList = factory(PartList::class)->create(['location_id' => $otherLocation->id]);
        
        $this->actingAs($this->siteAdminUser)
            ->get(route('part-list.delete', $partList))
            ->assertStatus(403);
            
        $response = $this->actingAs($this->siteAdminUser)
            ->call('delete', route('part-list.destroy', $partList))
            ->assertStatus(403);
            
        Event::assertNotDispatched(PartListDeleted::class);
    }
    
    /**
     * Test that a part number added to a part list doesn't display in notifications listing page.
     *
     * @return void
     */
    public function testExcludedPartIsNotListedInNotifications()
    {
        // Clean up left over part lists
        $partLists = PartList::get();
        
        if ($partLists->count()) {
            foreach ($partLists as $partList) {
                $partList->delete();
            }
        }
        
        // Check there are no notifications that are excluded.
        $excluded = DB::table('notifications')->select('*')->whereNotNull('deleted_at')->get();
        
        $this->assertFalse((bool) $excluded->count());
        
        $notification = Notification::whereNotNull('hdrROC')
            ->whereNotNull('rcsMPN')
            ->inRandomOrder()
            ->first();
            
        $location = Location::where('plant_code', $notification->plant_code)->first();
            
        $this->actingAs($this->dataAdminUser)
            ->get(route('notifications.index') . '?search=' . $notification->rcsMPN . '&pc=' . $location->plant_code)
            ->assertSee($notification->rcsSFI);
            
        $attributes = factory(PartList::class)->raw([
            'location_id' => $location->id,
            'context' => 'exclude',
            'parts' => $notification->rcsMPN
        ]);
        
        $response = $this->actingAs($this->dataAdminUser)
            ->call('post', route('part-list.store', $location), $attributes)
            ->assertSessionHas('alert.message', 'New excluded part numbers list created successfully!')
            ->assertStatus(302);
            
        $errors = session('errors');
        
        if (!empty($errors)) {
            mydd($errors);
        }
            
        // Check there are some notifications that are excluded.
        $excluded = DB::table('notifications')->select('*')->whereNotNull('deleted_at')->get();
        
        $this->assertTrue((bool) $excluded->count());
            
        $this->actingAs($this->dataAdminUser)
            ->get(route('notifications.index') . '?search=' . $notification->rcsMPN . '&pc=' . $location->plant_code)
            ->assertSee('No notifications found.');
    }
    
    /**
     * Test that a part number added to a part list doesn't display in shop findings listing page.
     *
     * @param (type) $name
     * @return
     */
    public function testExcludedPartIsNotListedInShopFindings()
    {
        // Clean up left over part lists
        $partLists = PartList::get();
        
        if ($partLists->count()) {
            foreach ($partLists as $partList) {
                $partList->delete();
            }
        }
        
        $this->createShopFindingsWithPieceParts(10, $this->user);
        
        // Check there are some notifications that are excluded.
        $excluded = DB::table('shop_findings')->select('*')->whereNotNull('deleted_at')->get();
        
        $this->assertFalse((bool) $excluded->count());
        
        // Get a random shop finding
        $shopFinding = ShopFinding::with(['HDR_Segment', 'ShopFindingsDetail.RCS_Segment'])
            ->whereHas('HDR_Segment')
            ->where('plant_code', $this->user->location->plant_code)
            ->inRandomOrder()
            ->first();
        
        // Assert that the shopfinding is listed. 
        $this->actingAs($this->dataAdminUser)
            ->get(route('datasets.index') . '?search=' . $shopFinding->ShopFindingsDetail->RCS_Segment->get_RCS_MPN() . '&pc=' . $this->user->location->plant_code)
            ->assertSee($shopFinding->id);
            
        // Create new part list and exclude parts.
        $attributes = factory(PartList::class)->raw([
            'location_id' => $this->user->location->id,
            'context' => 'exclude',
            'parts' => $shopFinding->ShopFindingsDetail->RCS_Segment->get_RCS_MPN()
        ]);
        
        $response = $this->actingAs($this->dataAdminUser)
            ->call('post', route('part-list.store', $this->user->location), $attributes)
            ->assertSessionHas('alert.message', 'New excluded part numbers list created successfully!')
            ->assertStatus(302);
            
        $errors = session('errors');
        
        if (!empty($errors)) {
            mydd($errors);
        }
            
        // Check there are some notifications that are excluded.
        $excluded = DB::table('shop_findings')->select('*')->whereNotNull('deleted_at')->get();
        
        $this->assertTrue((bool) $excluded->count());
        
        // Assert that the same shop finding is now not listed.
        $this->actingAs($this->dataAdminUser)
            ->get(route('datasets.index') . '?search=' . $shopFinding->ShopFindingsDetail->RCS_Segment->get_RCS_MPN() . '&pc=' . $this->user->location->plant_code)
            ->assertSee('No datasets found.');
    }
    
    /**
     * Test that a wildcard in the parts list moved the shopfinding part to the deleted list.
     *
     * @return void
     */
    public function testWildcardShopFindingPartsAreExcluded()
    {
        // Clean up left over part lists
        $partLists = PartList::get();
        
        if ($partLists->count()) {
            foreach ($partLists as $partList) {
                $partList->delete();
            }
        }
        
        $this->createShopFindingsWithPieceParts(10, $this->user);
        
        // Check there are no shop_findings that are excluded.
        $excluded = DB::table('shop_findings')->select('*')->whereNotNull('deleted_at')->get();
        
        $this->assertFalse((bool) $excluded->count());
        
        $partNumbers = [
            '900-100-100',
            '900-100-101',
            '900-100-102',
            '900-100-103',
            '900-100-104',
            '900-100-105',
            '900-100-106',
            '900-100-107',
            '900-100-108',
            '900-100-109'
        ];
        
        $rcsSegments = RCS_Segment::get();
        
        foreach ($rcsSegments as $key => $rcsSegment) {
            $rcsSegment->MPN = $partNumbers[$key];
            $rcsSegment->save();
        }
        
        // Get a random shop finding
        $shopFinding = ShopFinding::with(['HDR_Segment', 'ShopFindingsDetail.RCS_Segment'])
            ->whereHas('HDR_Segment')
            ->whereHas('ShopFindingsDetail.RCS_Segment', function($q) use ($partNumbers) {
                $q->whereIn('MPN', $partNumbers);
            })
            ->where('plant_code', $this->user->location->plant_code)
            ->inRandomOrder()
            ->firstOrFail();
            
        // Assert that the shopfinding is listed. 
        $this->actingAs($this->dataAdminUser)
            ->get(route('datasets.index') . '?search=' . $shopFinding->ShopFindingsDetail->RCS_Segment->get_RCS_MPN() . '&pc=' . $this->user->location->plant_code)
            ->assertSee($shopFinding->id);
            
        // Create new part list and exclude parts with wildcard.
        $attributes = factory(PartList::class)->raw([
            'location_id' => $this->user->location->id,
            'context' => 'exclude',
            'parts' => '900-100-*'
        ]);
        
        $response = $this->actingAs($this->dataAdminUser)
            ->call('post', route('part-list.store', $this->user->location), $attributes)
            ->assertSessionHas('alert.message', 'New excluded part numbers list created successfully!')
            ->assertStatus(302);
            
        $errors = session('errors');
        
        if (!empty($errors)) {
            mydd($errors);
        }
            
        // Check there are some notifications that are excluded.
        $excluded = DB::table('shop_findings')->select('*')->whereNotNull('deleted_at')->get();
        
        $this->assertTrue((bool) $excluded->count());
        
        // Assert that the shopfinding is not listed. 
        $this->actingAs($this->dataAdminUser)
            ->get(route('datasets.index') . '?search=' . $shopFinding->ShopFindingsDetail->RCS_Segment->get_RCS_MPN() . '&pc=' . $this->user->location->plant_code)
            ->assertDontSee($shopFinding->id);
            
        // Assert that the shopfinding is listed in the deleted list. 
        $this->actingAs($this->dataAdminUser)
            ->get(route('deleted.index'))
            ->assertSee($shopFinding->id);
    }
    
    /**
     * Test that a wildcard in the parts list moved the notification part to the deleted list.
     *
     * @return void
     */
    public function testWildcardNotificationPartsAreExcluded()
    {
        // Clean up left over part lists
        $partLists = PartList::get();
        
        if ($partLists->count()) {
            foreach ($partLists as $partList) {
                $partList->delete();
            }
        }
        
        // Check there are no notifications that are excluded.
        $excluded = DB::table('notifications')->select('*')->whereNotNull('deleted_at')->get();
        
        $this->assertFalse((bool) $excluded->count());
        
        $partNumbers = [
            '900-100-100',
            '900-100-101',
            '900-100-102',
            '900-100-103',
            '900-100-104',
            '900-100-105',
            '900-100-106',
            '900-100-107',
            '900-100-108',
            '900-100-109'
        ];
        
        $notifications = Notification::where('plant_code', $this->user->location->plant_code)
            ->whereNull('deleted_at')->take(10)->get();
            
        foreach ($notifications as $key => $notification) {
            $notification->rcsMPN = $partNumbers[$key];
            $notification->save();
        }
        
        $notification = Notification::where('plant_code', $this->user->location->plant_code)
            ->where('rcsMPN', '900-100-107')->firstOrFail();
        
        // Assert that the shopfinding is listed. 
        $this->actingAs($this->dataAdminUser)
            ->get(route('notifications.index') . '?search=900-100-107' . '&pc=' . $this->user->location->plant_code)
            ->assertSee($notification->id);
        
        // Create new part list and exclude parts with wildcard.
        $attributes = factory(PartList::class)->raw([
            'location_id' => $this->user->location->id,
            'context' => 'exclude',
            'parts' => '900-100-*'
        ]);
        
        $response = $this->actingAs($this->dataAdminUser)
            ->call('post', route('part-list.store', $this->user->location), $attributes)
            ->assertSessionHas('alert.message', 'New excluded part numbers list created successfully!')
            ->assertStatus(302);
            
        $errors = session('errors');
        
        if (!empty($errors)) {
            mydd($errors);
        }
        
        // Check there are some notifications that are excluded.
        $excluded = DB::table('notifications')->select('*')->whereNotNull('deleted_at')->get();
        
        $this->assertTrue((bool) $excluded->count());
        
        // Assert that the notification is not listed. 
        $this->actingAs($this->dataAdminUser)
            ->get(route('notifications.index') . '?search=900-100-107' . '&pc=' . $this->user->location->plant_code)
            ->assertDontSee($notification->id);
            
        // Assert that the shopfinding is listed in the deleted list. 
        $this->actingAs($this->dataAdminUser)
            ->get(route('deleted.index'))
            ->assertSee($notification->id);
    }
    
    /**
     * Test that a shopfinding based off a notification that is excluded but without a header or RCS Segment saved is excluded.
     *
     * @return void
     */
    public function testShopFindingWithoutHeaderOrRCSSegmentIsExcluded()
    {
        // Clean up left over part lists
        $partLists = PartList::get();
        
        if ($partLists->count()) {
            foreach ($partLists as $partList) {
                $partList->delete();
            }
        }
        
        // Check there are no notifications that are excluded.
        $excluded = DB::table('notifications')->select('*')->whereNotNull('deleted_at')->get();
        
        $this->assertFalse((bool) $excluded->count());
        
        $excludedPartNumber = '900-100-100'; // The part we will exclude.
        
        $notification = Notification::where('plant_code', $this->user->location->plant_code)
            ->whereNull('deleted_at')->inRandomOrder()->firstOrFail();
            
        $notification->rcsMPN = $excludedPartNumber;
        $notification->save();
        
        // Create a segment
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
            ->call('POST', route('engine-information.update', $notification->get_RCS_SFI()), $attributes)
            ->assertStatus(302);
            
        $this->get($response->headers->get('Location'))
            ->assertSee('Engine Information saved successfully!');
        
        // Check the shopfinding record is visible if searching by part number.
        $this->get(route('datasets.index') . '?search='.$excludedPartNumber)
            ->assertSee($notification->get_RCS_SFI())
            ->assertStatus(200);
            
        // Create part list with excluded part number.
        $attributes = factory(PartList::class)->raw([
            'location_id' => $this->user->location->id,
            'context' => 'exclude',
            'parts' => $excludedPartNumber
        ]);
        
        $response = $this->actingAs($this->dataAdminUser)
            ->call('post', route('part-list.store', $this->user->location), $attributes)
            ->assertSessionHas('alert.message', 'New excluded part numbers list created successfully!')
            ->assertStatus(302);
            
        $errors = session('errors');
        
        if (!empty($errors)) {
            mydd($errors);
        }
        
        // Check there are some notifications that are excluded.
        $excluded = DB::table('shop_findings')->select('*')->whereNotNull('deleted_at')->get();
        
        $this->assertTrue((bool) $excluded->count());
        
        // Check the shopfinding record is not visible if searching by part number.
        $this->get(route('datasets.index') . '?search='.$excludedPartNumber)
            ->assertDontSee($notification->get_RCS_SFI())
            ->assertStatus(200);
            
        // Assert that the shopfinding is listed in the deleted list. 
        $this->actingAs($this->user)
            ->get(route('deleted.index'))
            ->assertSee($notification->get_RCS_SFI());
    }
    
}
