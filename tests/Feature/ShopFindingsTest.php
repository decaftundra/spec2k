<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\ShopFindings\ShopFinding;
use App\Notification;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ShopFindingsTest extends TestCase
{
    /**
     * Test for a 200 response from the notifications index and it is displaying the correct number of records.
     *
     * @return void
     */
    public function testShopFindingsIndex()
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
        
        $perPage = 20;
        
        if ($number < 20) {
            $perPage = $number;
        }
        
        $this->actingAs($this->user)
            ->call('GET', route('datasets.index') . '?pc=all')
            ->assertStatus(200)
            ->assertSee("Displaying 1 to $perPage of $number datasets.");
            
        // Get random id.
        $shopFinding = ShopFinding::whereHas('HDR_Segment')
            ->where('plant_code', $this->user->location->plant_code)
            ->inRandomOrder()
            ->first();
        
        $this->actingAs($this->user)
            ->call('GET', route('datasets.index') . '?search=' . $shopFinding->id . '&pc=All')
            ->assertStatus(200)
            ->assertSee("Displaying 1 to 1 of 1 datasets.");
    }
    
    // Test partial notification id search.
    
    // Test partial part number search.
    
    // Test partial serial number search.
    
    // Test location search (admins only).
    
    // Test date range search.
}
