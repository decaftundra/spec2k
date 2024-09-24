<?php

namespace Tests\Feature;

use App\Customer;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CustomerTest extends TestCase
{
    /**
     * Test that the customer pages return a 403 status if a user tries to access them.
     *
     * @return void
     */
    public function testUserCantAccessCustomerPages()
    {
        $this->actingAs($this->user)
            ->get(route('customer.index'))
            ->assertStatus(403);
            
        $customer = factory(Customer::class)->raw();
        
        $this->actingAs($this->user)
            ->get(route('customer.create'))
            ->assertStatus(403);
        
        $this->actingAs($this->user)
            ->call('POST', route('customer.store'), $customer)
            ->assertStatus(403);
            
        $customer = Customer::inRandomOrder()->first();
            
        $newCustomer = factory(Customer::class)->raw();
        
        $this->actingAs($this->user)
            ->get(route('customer.edit', $customer->id))
            ->assertStatus(403);
        
        $this->actingAs($this->user)
            ->call('PUT', route('customer.update', $customer->id), $newCustomer)
            ->assertStatus(403);
        
        $this->actingAs($this->user)
            ->get(route('customer.delete', $customer->id))
            ->assertStatus(403);
        
        $this->actingAs($this->user)
            ->call('DELETE', route('customer.destroy', $customer->id))
            ->assertStatus(403);
    }
    
    /**
     * Test that the customer index returns a 200 response.
     *
     * @return void
     */
    public function testCustomersIndex()
    {
        $response = $this->actingAs($this->dataAdminUser)
            ->get(route('customer.index'))
            ->assertStatus(200);
    }
    
    /**
     * Test that the create form returns 200 and a new customer is created successfully.
     *
     * @return void
     */
    public function testCreateCustomer()
    {
        $customer = factory(Customer::class)->raw();
        
        $this->actingAs($this->dataAdminUser)
            ->get(route('customer.create'))
            ->assertStatus(200)
            ->assertSee('Create Customer');
        
        $response = $this->actingAs($this->dataAdminUser)
            ->call('POST', route('customer.store'), $customer);
        
        // Replace empty icao if required.
        if (empty($customer['icao'])) $customer['icao'] = 'ZZZZZ';
        
        $this->get($response->headers->get('Location'))
            ->assertSee('New customer created successfully!');
        
        $this->assertDatabaseHas('customers', $customer);
    }
    
    /**
     * Test the edit customer form returns 200 and a customer can be edited.
     *
     * @return void
     */
    public function testEditCustomer()
    {
        $customer = Customer::inRandomOrder()->first();
        
        $newCustomer = factory(Customer::class)->raw();
        
        $this->actingAs($this->dataAdminUser)
            ->get(route('customer.edit', $customer->id))
            ->assertStatus(200)
            ->assertSee('Edit Customer');
        
        $response = $this->actingAs($this->dataAdminUser)
            ->call('PUT', route('customer.update', $customer->id), $newCustomer);
        
        // Replace empty icao if required.
        if (empty($newCustomer['icao'])) $newCustomer['icao'] = 'ZZZZZ';
        
        $newCustomer['id'] = $customer->id;
        
        $this->get($response->headers->get('Location'))
            ->assertSee('Customer updated successfully!');
        
        $this->assertDatabaseHas('customers', $newCustomer);
    }
    
    /**
     * Test the delete customer form returns 200 and a customer can be deleted.
     *
     * @return void
     */
    public function testDeleteCustomer()
    {
        $customer = Customer::inRandomOrder()->first();
        
        $this->actingAs($this->dataAdminUser)
            ->get(route('customer.delete', $customer->id))
            ->assertStatus(200)
            ->assertSee('Delete Customer');
        
        $response = $this->actingAs($this->dataAdminUser)
            ->call('DELETE', route('customer.destroy', $customer->id));
        
        $this->get($response->headers->get('Location'))
            ->assertSee('Customer deleted successfully!');
        
        $oldCustomer = [];
        $oldCustomer['company_name'] = $customer->company_name;
        $oldCustomer['icao'] = $customer->icao;
        
        $this->assertDatabaseMissing('customers', $oldCustomer);
    }
}
