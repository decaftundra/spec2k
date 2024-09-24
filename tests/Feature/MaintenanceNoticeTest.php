<?php

namespace Tests\Feature;

use App\MaintenanceNotice;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class MaintenanceNoticeTest extends TestCase
{
    /**
     * Test maintenance notice index.
     *
     * @return void
     */
    public function testMaintenanceNoticeIndex()
    {
        $notices = factory(MaintenanceNotice::class, 5)->create();
        
        $notice = MaintenanceNotice::inRandomOrder()->first();
        
        $this->actingAs($this->dataAdminUser)
            ->get(route('maintenance-notice.index'))
            ->assertSeeText($notice->title)
            ->assertStatus(200);
    }
    
    /**
     * Test maintenance notice permissions.
     *
     * @return void
     */
    public function testMaintenanceNoticePermissions()
    {
        $notices = factory(MaintenanceNotice::class, 5)->create();
        
        $notice = MaintenanceNotice::inRandomOrder()->first();
        
        $this->actingAs($this->adminUser)
            ->get(route('maintenance-notice.index'))
            ->assertStatus(403);
            
        $this->actingAs($this->adminUser)
            ->get(route('maintenance-notice.edit', $notice->id))
            ->assertStatus(403);
            
        $this->actingAs($this->adminUser)
            ->get(route('maintenance-notice.delete', $notice->id))
            ->assertStatus(403);
            
        $this->actingAs($this->adminUser)
            ->get(route('maintenance-notice.create'))
            ->assertStatus(403);
    }
    
    /**
     * Test the creation of a new maintenance notice.
     *
     * @return void
     */
    public function testCreateMaintenanceNotice()
    {
        $notice = factory(MaintenanceNotice::class)->raw();
        
        $attributes = [
            'title' => $notice['title'],
            'contents' => $notice['contents'],
            'display' => 1
        ];
        
        $this->actingAs($this->dataAdminUser)
            ->get(route('maintenance-notice.create'))
            ->assertSeeText('Create Maintenance Notice')
            ->assertStatus(200);
            
        $response = $this->actingAs($this->dataAdminUser)
            ->call('POST', route('maintenance-notice.store'), $attributes)
            ->assertStatus(302);
            
        $this->followRedirects($response)
            ->assertSeeText('New Maintenance Notice created successfully!')
            ->assertStatus(200);
            
        $this->assertDatabaseHas('maintenance_notices', $attributes);
        
        $this->actingAs($this->user)
            ->get(route('notifications.index'))
            ->assertSeeText($notice['title'])
            ->assertSeeText($notice['contents'])
            ->assertStatus(200);
    }
    
    /**
     * Test the editing of a maintenance notice.
     *
     * @return void
     */
    public function testEditMaintenanceNotice()
    {
        $notice = factory(MaintenanceNotice::class)->create();
        
        $this->actingAs($this->dataAdminUser)
            ->get(route('maintenance-notice.edit', $notice->id))
            ->assertSeeText('Edit Maintenance Notice')
            ->assertSee($notice->title)
            ->assertSee($notice->contents)
            ->assertStatus(200);
            
        $noticeUpdated = factory(MaintenanceNotice::class)->raw();
        
        $attributes = [
            'title' => $noticeUpdated['title'],
            'contents' => $noticeUpdated['contents'],
            'display' => 1
        ];
            
        $response = $this->actingAs($this->dataAdminUser)
            ->call('PUT', route('maintenance-notice.update', $notice->id), $attributes)
            ->assertStatus(302);
            
        $this->followRedirects($response)
            ->assertSeeText('Maintenance Notice updated successfully!')
            ->assertStatus(200);
            
        $this->assertDatabaseHas('maintenance_notices', $attributes);
        
        $this->actingAs($this->user)
            ->get(route('notifications.index'))
            ->assertSeeText($noticeUpdated['title'])
            ->assertSeeText($noticeUpdated['contents'])
            ->assertStatus(200);
    }
    
    /**
     * Test the deletion of a maintenance notice.
     *
     * @return void
     */
    public function testDeleteMaintenanceNotice()
    {
        $notice = factory(MaintenanceNotice::class)->create();
        
        $this->actingAs($this->dataAdminUser)
            ->get(route('maintenance-notice.delete', $notice->id))
            ->assertSeeText('Delete Maintenance Notice')
            ->assertSeeText($notice->title)
            ->assertSeeText($notice->contents)
            ->assertStatus(200);
            
        $response = $this->actingAs($this->dataAdminUser)
            ->call('DELETE', route('maintenance-notice.destroy', $notice->id))
            ->assertStatus(302);
            
        $this->followRedirects($response)
            ->assertSeeText('Maintenance Notice deleted successfully!')
            ->assertStatus(200);
            
        $this->assertDatabaseMissing('maintenance_notices', ['title' => $notice->title]);
    }
    
    /**
     * Test the display of a hidden maintenance notice.
     *
     * @params
     */
    public function testHiddenMaintenanceNotice()
    {
        $notice = factory(MaintenanceNotice::class)->raw();
        
        $attributes = [
            'title' => $notice['title'],
            'contents' => $notice['contents'],
            'display' => 0
        ];
        
        $this->actingAs($this->dataAdminUser)
            ->get(route('maintenance-notice.create'))
            ->assertSeeText('Create Maintenance Notice')
            ->assertStatus(200);
            
        $response = $this->actingAs($this->dataAdminUser)
            ->call('POST', route('maintenance-notice.store'), $attributes)
            ->assertStatus(302);
            
        $this->followRedirects($response)
            ->assertSeeText('New Maintenance Notice created successfully!')
            ->assertStatus(200);
            
        $this->assertDatabaseHas('maintenance_notices', $attributes);
        
        $this->actingAs($this->user)
            ->get(route('notifications.index'))
            ->assertDontSeeText($notice['title'])
            ->assertDontSeeText($notice['contents'])
            ->assertStatus(200);
    }
}
