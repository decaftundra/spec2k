<?php

namespace Tests\Feature;

use App\CageCode;
use App\Location;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CageCodeTest extends TestCase
{
    /**
     * Test the cage code index
     *
     * @return void
     */
    public function testCageCodeIndex()
    {
        $cageCode = CageCode::orderBy('cage_code', 'asc')->first();
        
        $this->actingAs($this->dataAdminUser)
            ->get(route('cage-code.index'))
            ->assertStatus(200)
            ->assertSee($cageCode->cage_code);
    }
    
    /**
     * Test the creation of a new cage code.
     *
     * @return void
     */
    public function testCreateCageCode()
    {
        $this->actingAs($this->dataAdminUser)
            ->get(route('cage-code.create'))
            ->assertStatus(200)
            ->assertSee('Create Cage Code');
            
        $attributes = factory(CageCode::class)->raw();
        
        $response = $this->actingAs($this->dataAdminUser)
            ->call('POST', route('cage-code.store'), $attributes)
            ->assertStatus(302);
            
        $this->get($response->headers->get('Location'))
            ->assertStatus(200)
            ->assertSee('New cage code created successfully!');
            
        $this->assertDatabaseHas('cage_codes', $attributes);
    }
    
    /**
     * Test updating a cage code.
     *
     * @return void
     */
    public function testUpdateCageCode()
    {
        $cageCode = CageCode::inRandomOrder()->first();
        
        $this->actingAs($this->dataAdminUser)
            ->get(route('cage-code.edit', $cageCode))
            ->assertStatus(200)
            ->assertSee('Edit Cage Code');
            
        $attributes = factory(CageCode::class)->raw();
        
        $response = $this->actingAs($this->dataAdminUser)
            ->call('PUT', route('cage-code.update', $cageCode), $attributes)
            ->assertStatus(302);
            
        $this->get($response->headers->get('Location'))
            ->assertStatus(200)
            ->assertSee('Cage code updated successfully!');
            
        $this->assertDatabaseHas('cage_codes', $attributes);
    }
    
    /**
     * Test delete cage code.
     *
     * @return void
     */
    public function testDeleteCageCode()
    {
        $cageCode = factory(CageCode::class)->create();
        
        $this->actingAs($this->dataAdminUser)
            ->get(route('cage-code.delete', $cageCode))
            ->assertStatus(200)
            ->assertSee('Delete Cage Code');
        
        $response = $this->actingAs($this->dataAdminUser)
            ->call('DELETE', route('cage-code.destroy', $cageCode))
            ->assertStatus(302);
            
        $this->get($response->headers->get('Location'))
            ->assertStatus(200)
            ->assertSee('Cage code deleted successfully!');
    }
    
    /**
     * Test a related cage code can't be deleted.
     *
     * @return void
     */
    public function testRelatedCageCodeCantBeDeleted()
    {
        $cageCode = CageCode::inRandomOrder()->first();
        $location = Location::inRandomOrder()->first();
        
        $location->cage_codes()->sync([$cageCode->id]);
        
        $response = $this->actingAs($this->dataAdminUser)
            ->call('DELETE', route('cage-code.destroy', $cageCode))
            ->assertStatus(302);
            
        $this->get($response->headers->get('Location'))
            ->assertStatus(200)
            ->assertSee('Error, this cage code is related to at least one location, so it cannot be deleted.');
    }
    
    /**
     * Test a non data admin can't access any of the cage code CRUD pages.
     *
     * @return void
     */
    public function testNonDataAdminUsersCantAccessCageCodePages()
    {
        $cageCode = CageCode::inRandomOrder()->first();
        $attributes = factory(CageCode::class)->raw();
        
        $this->actingAs($this->siteAdminUser)
            ->get(route('cage-code.index'))
            ->assertStatus(403);
            
        $this->actingAs($this->adminUser)
            ->get(route('cage-code.index'))
            ->assertStatus(403);
            
        $this->actingAs($this->user)
            ->get(route('cage-code.index'))
            ->assertStatus(403);
            
        $this->actingAs($this->siteAdminUser)
            ->get(route('cage-code.create'))
            ->assertStatus(403);
            
        $this->actingAs($this->adminUser)
            ->get(route('cage-code.create'))
            ->assertStatus(403);
            
        $this->actingAs($this->user)
            ->get(route('cage-code.create'))
            ->assertStatus(403);
            
        $this->actingAs($this->siteAdminUser)
             ->call('POST', route('cage-code.store'), $attributes)
            ->assertStatus(403);
            
        $this->actingAs($this->adminUser)
             ->call('POST', route('cage-code.store'), $attributes)
            ->assertStatus(403);
            
        $this->actingAs($this->user)
            ->call('POST', route('cage-code.store'), $attributes)
            ->assertStatus(403);
        
        $this->actingAs($this->siteAdminUser)
            ->get(route('cage-code.edit', $cageCode))
            ->assertStatus(403);
            
        $this->actingAs($this->adminUser)
            ->get(route('cage-code.edit', $cageCode))
            ->assertStatus(403);
            
        $this->actingAs($this->user)
            ->get(route('cage-code.edit', $cageCode))
            ->assertStatus(403);
            
        $this->actingAs($this->siteAdminUser)
             ->call('PUT', route('cage-code.update', $cageCode), $attributes)
            ->assertStatus(403);
            
        $this->actingAs($this->adminUser)
             ->call('PUT', route('cage-code.update', $cageCode), $attributes)
            ->assertStatus(403);
            
        $this->actingAs($this->user)
            ->call('PUT', route('cage-code.update', $cageCode), $attributes)
            ->assertStatus(403);
            
        $this->actingAs($this->siteAdminUser)
            ->get(route('cage-code.delete', $cageCode))
            ->assertStatus(403);
            
        $this->actingAs($this->adminUser)
            ->get(route('cage-code.delete', $cageCode))
            ->assertStatus(403);
            
        $this->actingAs($this->user)
            ->get(route('cage-code.delete', $cageCode))
            ->assertStatus(403);
            
        $this->actingAs($this->siteAdminUser)
             ->call('DELETE', route('cage-code.destroy', $cageCode))
            ->assertStatus(403);
            
        $this->actingAs($this->adminUser)
             ->call('DELETE', route('cage-code.destroy', $cageCode))
            ->assertStatus(403);
            
        $this->actingAs($this->user)
            ->call('DELETE', route('cage-code.destroy', $cageCode))
            ->assertStatus(403);
    }
}
