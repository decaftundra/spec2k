<?php

namespace Tests\Feature;

use App\CageCode;
use App\Location;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class LocationTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */
    public function testUserCantAccessLocationRoutes()
    {
        $this->actingAs($this->user)
            ->get(route('location.index'))
            ->assertStatus(403);
            
        $location = Location::inRandomOrder()->first();
        
        $attributes = factory(Location::class)->raw();
        
        $this->actingAs($this->user)
            ->get(route('location.create'))
            ->assertStatus(403);
            
        $this->actingAs($this->user)
            ->call('post', route('location.store'), $attributes)
            ->assertStatus(403);
            
        $this->actingAs($this->user)
            ->get(route('location.edit', $location))
            ->assertStatus(403);
            
        $this->actingAs($this->user)
            ->call('put', route('location.update', $location), $attributes)
            ->assertStatus(403);
            
        $this->actingAs($this->user)
            ->get(route('location.delete', $location))
            ->assertStatus(403);
            
        $this->actingAs($this->user)
            ->call('delete', route('location.destroy', $location))
            ->assertStatus(403);
    }
    
    /**
     * Test that a site adin can view the locations index page.
     *
     * @return void
     */
    public function testSiteAdminCanViewLocationIndex()
    {
        $otherLocation = Location::where('plant_code', '!=', $this->siteAdminUser->location->plant_code)
            ->inRandomOrder()
            ->first();
        
        $this->actingAs($this->siteAdminUser)
            ->get(route('location.index'))
            ->assertSee('Displaying 1 to 1 of 1 repair stations.')
            ->assertDontSee($otherLocation->name)
            ->assertSee($this->siteAdminUser->location->name)
            ->assertStatus(200);
    }
    
    /**
     * Test a site admin can update their location.
     *
     * @return void
     */
    public function testSiteAdminCanUpdateTheirOwnLocation()
    {
        $location = Location::where('plant_code', $this->siteAdminUser->location->plant_code)->first();
        
        $this->actingAs($this->siteAdminUser)
            ->get(route('location.edit', $location))
            ->assertSee('Edit Repair Station')
            ->assertStatus(200);
            
        $attributes = factory(Location::class)->raw();
        $attributes['cage_codes'] = CageCode::inRandomOrder()->take(mt_rand(0,3))->pluck('id')->toArray();
        
        $response = $this->actingAs($this->siteAdminUser)
            ->call('put', route('location.update', $location), $attributes)
            ->assertStatus(302);
            
        $this->get($response->headers->get('Location'))
            ->assertSee('Repair Station updated successfully!')
            ->assertStatus(200);
            
        $location = Location::where('plant_code', $attributes['plant_code'])->firstOrFail();
            
        if (count($attributes['cage_codes'])) {
            foreach ($attributes['cage_codes'] as $cageCode) {
                $this->assertDatabaseHas('cage_code_location', ['cage_code_id' => $cageCode, 'location_id' => $location->id]);
            }
        }
            
        unset($attributes['cage_codes']);
            
        $this->assertDatabaseHas('locations', $attributes);
    }
    
    /**
     * Test that a data admin can view the locations index.
     *
     * @return void
     */
    public function testDataAdminCanViewLocationIndex()
    {
        $location = Location::inRandomOrder()->first();
        
        $this->actingAs($this->dataAdminUser)
            ->get(route('location.index'))
            ->assertSee('Repair Stations')
            ->assertSee((string) $location->plant_code)
            ->assertSee($location->name)
            ->assertStatus(200);
    }
    
    /**
     * Test a data admin can create a new location.
     *
     * @return void
     */
    public function testDataAdminCanCreateNewLocation()
    {
        $this->actingAs($this->dataAdminUser)
            ->get(route('location.create'))
            ->assertSee('Create Repair Station')
            ->assertStatus(200);
            
        $attributes = factory(Location::class)->raw();
        $attributes['cage_codes'] = CageCode::inRandomOrder()->take(mt_rand(0,3))->pluck('id')->toArray();
        
        $response = $this->actingAs($this->dataAdminUser)
            ->call('post', route('location.store'), $attributes)
            ->assertStatus(302);
            
        $this->get($response->headers->get('Location'))
            ->assertSee('New repair station created successfully!')
            ->assertStatus(200);
            
        $location = Location::where('plant_code', $attributes['plant_code'])->firstOrFail();
            
        if (count($attributes['cage_codes'])) {
            foreach ($attributes['cage_codes'] as $cageCode) {
                $this->assertDatabaseHas('cage_code_location', ['cage_code_id' => $cageCode, 'location_id' => $location->id]);
            }
        }
            
        unset($attributes['cage_codes']);
            
        $this->assertDatabaseHas('locations', $attributes);
        
        
    }
    
    /**
     * Test a data admin can update an existing location.
     *
     * @return void
     */
    public function testDataAdminCanUpdateLocation()
    {
        $location = Location::inRandomOrder()->first();
        
        $this->actingAs($this->dataAdminUser)
            ->get(route('location.edit', $location))
            ->assertSee('Edit Repair Station')
            ->assertStatus(200);
            
        $attributes = factory(Location::class)->raw();
        $attributes['cage_codes'] = CageCode::inRandomOrder()->take(mt_rand(0,3))->pluck('id')->toArray();
        
        $response = $this->actingAs($this->dataAdminUser)
            ->call('put', route('location.update', $location), $attributes)
            ->assertStatus(302);
            
        $errors = session('errors');
        
        if (!empty($errors)) {
            mydd($errors);
            var_dump($attributes);
            //dd('Validation Errors, see above.');
        }
            
        $this->get($response->headers->get('Location'))
            ->assertSee('Repair Station updated successfully!')
            ->assertStatus(200);
            
        $location = Location::where('plant_code', $attributes['plant_code'])->firstOrFail();
            
        if (count($attributes['cage_codes'])) {
            foreach ($attributes['cage_codes'] as $cageCode) {
                $this->assertDatabaseHas('cage_code_location', ['cage_code_id' => $cageCode, 'location_id' => $location->id]);
            }
        }
            
        unset($attributes['cage_codes']);
            
        $this->assertDatabaseHas('locations', $attributes);
    }
    
    /**
     * Test that data admin can't delete a location that has related users.
     *
     * @return void
     */
    public function testDataAdminCantDeleteLocationWithRelatedUsers()
    {
        $location = Location::whereHas('users')->inRandomOrder()->first();
        
        $response = $this->actingAs($this->dataAdminUser)
            ->get(route('location.delete', $location))
            ->assertStatus(302);
            
        $this->get($response->headers->get('Location'))
            ->assertSee('Error, there are users related to this repair station so it cannot be deleted.')
            ->assertStatus(200);
            
        $response = $this->actingAs($this->dataAdminUser)
            ->call('delete', route('location.destroy', $location))
            ->assertStatus(302);
            
        $this->get($response->headers->get('Location'))
            ->assertSee('Error, there are users related to this repair station so it cannot be deleted.')
            ->assertStatus(200);
    }
    
    /**
     * Test that a data admin can delete a location if it has no users related to it.
     *
     * @return void
     */
    public function testDataAdminCanDeleteLocationThatHasNoUsersAttached()
    {
        $location = factory(Location::class)->create();
        
        $this->actingAs($this->dataAdminUser)
            ->get(route('location.delete', $location))
            ->assertSee('Delete Repair Station')
            ->assertSee($location->name)
            ->assertStatus(200);
            
        $response = $this->actingAs($this->dataAdminUser)
            ->call('delete', route('location.destroy', $location))
            ->assertStatus(302);
            
        $this->get($response->headers->get('Location'))
            ->assertSee('Repair Station deleted successfully!')
            ->assertStatus(200);
    }
}
