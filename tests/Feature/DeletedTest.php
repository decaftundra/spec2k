<?php

namespace Tests\Feature;

use App\ShopFindings\ShopFinding;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DeletedTest extends TestCase
{
    /**
     * Test for a 200 response from the deleted index.
     *
     * @return void
     */
    public function testDeletedIndex()
    {
        $this->actingAs($this->user)->call('GET', route('deleted.index'))->assertStatus(200);
    }
    
    /**
     * Test a notification can be deleted and then appears in the deleted list.
     *
     * @return void
     */
    public function testNotificationDeletion()
    {
        $notification = $this->getEditableNotification($this->dataAdminUser);
        
        $response = $this->actingAs($this->dataAdminUser)->ajaxPost(route('status.delete'), ['id' => $notification->id]);
        
        $response->assertStatus(200)
            ->assertJson(['success' => true]);
            
        $this->actingAs($this->dataAdminUser)
            ->call('GET', route('deleted.index'))
            ->assertSee($notification->id)
            ->assertStatus(200);
    }
    
    /**
     * Test an in progress notification can be deleted and then appears in the deleted list.
     *
     * @return void
     */
    public function testInProgressDeletion()
    {
        $this->createShopFindingsWithPieceParts(10);
        
        $shopFinding = ShopFinding::inRandomOrder()->first();
        
        $response = $this->actingAs($this->dataAdminUser)->ajaxPost(route('status.delete'), ['id' => $shopFinding->id]);
        
        $response->assertStatus(200)
            ->assertJson(['success' => true]);
            
        $this->actingAs($this->dataAdminUser)
            ->call('GET', route('deleted.index'))
            ->assertSee($shopFinding->id)
            ->assertStatus(200);
    }
    
    // Test piece part count.
    
    // Test if it is a utas part.
    
    // Test the notifications search filter with a full notification id.
    
    // Test partial notification id search.
    
    // Test partial part number search.
    
    // Test partial serial number search.
    
    // Test location search.
    
    // Test date range search.
}
