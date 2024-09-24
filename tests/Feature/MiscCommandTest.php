<?php

namespace Tests\Feature;

use App\Location;
use App\Notification;
use App\PartList;
use App\ShopFindings\ShopFinding;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;

class MiscCommandTest extends TestCase
{
    /**
     * Test that the sync shop findings command runs without any errors.
     *
     * @return void
     */
    public function testSyncShopFindingsCommand()
    {
        $ids = Notification::where('plant_code', $this->user->location->plant_code)
            ->pluck('id')
            ->toArray();
        
        $shopFindings = ShopFinding::whereHas('HDR_Segment')
            ->where('plant_code', $this->user->location->plant_code)
            ->orWhereIn('id', $ids)
            ->get();
        
        if (!count($shopFindings)) {
            $number = mt_rand(1, 25);
            $this->createShopFindingsWithPieceParts($number, $this->user);
        } else {
            $number = count($shopFindings);
        }
        
        Artisan::call('spec2kapp:sync_shopfindings');
        
        $this->assertEquals('', Artisan::output());
    }
    
    /**
     * Test that the update validation command runs without any errors.
     *
     * @return void
     */
    public function testUpdateValidationCommand()
    {
        $ids = Notification::where('plant_code', $this->user->location->plant_code)
            ->pluck('id')
            ->toArray();
        
        $shopFindings = ShopFinding::whereHas('HDR_Segment')
            ->where('plant_code', $this->user->location->plant_code)
            ->orWhereIn('id', $ids)
            ->get();
        
        if (!count($shopFindings)) {
            $number = mt_rand(1, 25);
            $this->createShopFindingsWithPieceParts($number, $this->user);
        } else {
            $number = count($shopFindings);
        }
        
        Artisan::call('spec2kapp:update_validation');
        
        $this->assertStringContainsString('Validation updates complete.', Artisan::output());
    }
    
    /**
     * Test that the remove unwanted parts command runs without any errors.
     *
     * @return void
     */
    public function testPartListCommand()
    {
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
        
        Artisan::call('spec2kapp:remove_unwanted_parts');
        
        $this->assertEquals('', Artisan::output());
    }
}
