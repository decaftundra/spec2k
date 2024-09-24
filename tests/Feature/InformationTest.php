<?php

namespace Tests\Feature;

use App\PartList;
use App\Customer;
use App\Location;
use Tests\TestCase;
use App\AircraftDetail;
use App\Codes\ActionCode;
use App\Codes\RcsFailureCode;
use App\PieceParts\PiecePart;
use App\Notification;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class InformationTest extends TestCase
{
    /**
     * Test standard users can access information pages.
     *
     * @return void
     */
    public function testUserCanAccessInformationPages()
    {
        $this->actingAs($this->user)
            ->call('GET', route('info.customers'))
            ->assertStatus(200);
            
        $this->actingAs($this->user)
            ->call('GET', route('info.locations'))
            ->assertStatus(200);
            
        $this->actingAs($this->user)
            ->call('GET', route('info.aircraft'))
            ->assertStatus(200);
            
        $this->actingAs($this->user)
            ->call('GET', route('info.rcs-failure-codes'))
            ->assertStatus(200);
            
        $this->actingAs($this->user)
            ->call('GET', route('info.shop-action-codes'))
            ->assertStatus(200);
            
        $this->actingAs($this->user)
            ->call('GET', route('info.cage-codes'))
            ->assertStatus(200);
            
        $this->actingAs($this->user)
            ->call('GET', route('info.engine-details'))
            ->assertStatus(200);
    }
    
    /**
     * Test the customer information page.
     *
     * @return void
     */
    public function testCustomerInformation()
    {
        $customers = Customer::orderBy('company_name', 'asc')->get();
        
        // Order by name ascending.
        $this->actingAs($this->user)
            ->call('GET', route('info.customers') . '?orderby=company&order=asc')
            ->assertStatus(200)
            ->assertSee('Customers')
            ->assertSee("Displaying 1 to 20 of {$customers->count()} customers.")
            ->assertSeeText($customers->first()->company_name);
            
        // Order by name descending.
        $this->actingAs($this->user)
            ->call('GET', route('info.customers') . '?orderby=company&order=desc')
            ->assertStatus(200)
            ->assertSee('Customers')
            ->assertSee("Displaying 1 to 20 of {$customers->count()} customers.")
            ->assertSeeText($customers->last()->company_name);
            
        // Do a partial search where there are between 1 and 20 results.
        do {
            // Get random customer.
            $randomCustomer = Customer::where('icao', '!=', 'ZZZZZ')->inRandomOrder()->first();
            $partialName = substr($randomCustomer->company_name, 0, 6);
            $results = Customer::where('company_name', 'LIKE', "%$partialName%")->orWhere('icao', 'LIKE', "%$partialName%")->count();
        } while ((($results > 20) && (strlen($partialName) > 4)) || (($results <= 1)  && (strlen($partialName) > 4)));
        
        // Search by partial name.
        $this->actingAs($this->user)
            ->call('GET', route('info.customers') . "?search=$partialName&orderby=company&order=asc")
            ->assertStatus(200)
            ->assertSeeText('Customers')
            ->assertSeeText($randomCustomer->company_name);
            
        //mydd($results);
            
        do {
            // Get random customer.
            $randomCustomer = Customer::whereNotNull('icao')->where('icao', '!=', 'ZZZZZ')->inRandomOrder()->first();
            $icao = $randomCustomer->icao;
            $results = Customer::where('company_name', 'LIKE', "%$icao%")->orWhere('icao', $icao)->count();
        } while (($results > 20) || ($results == 0));
        
        //mydd($results);
        
        // Search by ICAO code.
        $this->actingAs($this->user)
            ->call('GET', route('info.customers') . "?search={$randomCustomer->icao}")
            ->assertStatus(200)
            ->assertSee('Customers')
            ->assertSeeText($randomCustomer->company_name);
            
        $customers = Customer::orderBy('icao', 'asc')->get();
        
        // Order by ICAO code ascending.
        $this->actingAs($this->user)
            ->call('GET', route('info.customers') . '?orderby=code&order=asc')
            ->assertStatus(200)
            ->assertSee('Customers')
            ->assertSee("Displaying 1 to 20 of {$customers->count()} customers.")
            ->assertSee($customers->first()->icao);
            
        // Order by ICAO code descending.
        $this->actingAs($this->user)
            ->call('GET', route('info.customers') . '?orderby=code&order=desc')
            ->assertStatus(200)
            ->assertSee('Customers')
            ->assertSee("Displaying 1 to 20 of {$customers->count()} customers.")
            ->assertSee($customers->last()->icao);
    }
    
    /**
     * Test the customer information page.
     *
     * @return void
     */
    public function testLocationInformation()
    {
        $route = route('info.locations');
        $locations = Location::orderBy('name', 'asc')->get();
        
        $total = $locations->count();
        $perpage = 20;
        
        $maxShown = $total > $perpage ? $perpage : $total;
        
        // Order by name ascending.
        $this->actingAs($this->user)
            ->call('GET', $route . '?orderby=name&order=asc')
            ->assertStatus(200)
            ->assertSee('Repair Stations')
            ->assertSee("Displaying 1 to $maxShown of $total locations.")
            ->assertSee($locations->first()->name);
            
        // Order by name descending.
        $this->actingAs($this->user)
            ->call('GET', $route . '?orderby=name&order=desc')
            ->assertStatus(200)
            ->assertSee('Repair Stations')
            ->assertSee("Displaying 1 to $maxShown of $total locations.")
            ->assertSee($locations->last()->name);
            
        // Get random location.
        $randomLocation = Location::inRandomOrder()->first();
        $partialName = substr($randomLocation->name, 0, 5);
        
        // Search by partial name.
        $this->actingAs($this->user)
            ->call('GET', $route . "?search=$partialName")
            ->assertStatus(200)
            ->assertSee('Repair Stations')
            ->assertSee($randomLocation->name);
        
        // Search by plant code.
        $this->actingAs($this->user)
            ->call('GET', $route . "?search={$randomLocation->plant_code}")
            ->assertStatus(200)
            ->assertSee('Repair Stations')
            ->assertSee($randomLocation->name);
            
        $locations = Location::orderBy('plant_code', 'asc')->get();
        
        // Order by plant code ascending.
        $this->actingAs($this->user)
            ->call('GET', $route . '?orderby=code&order=asc')
            ->assertStatus(200)
            ->assertSee('Repair Stations')
            ->assertSee("Displaying 1 to $maxShown of $total locations.")
            ->assertSee((string) $locations->first()->plant_code);
            
        // Order by plant code descending.
        $this->actingAs($this->user)
            ->call('GET', $route . '?orderby=code&order=desc')
            ->assertStatus(200)
            ->assertSee('Repair Stations')
            ->assertSee("Displaying 1 to $maxShown of $total locations.")
            ->assertSee((string) $locations->last()->plant_code);
    }
    
    /**
     * Test the location parts page.
     *
     * @return void
     */
    public function testLocationPartsInformation()
    {
        // Clean up left over part lists
        $partLists = PartList::get();
        
        if ($partLists->count()) {
            foreach ($partLists as $partList) {
                $partList->delete();
            }
        }
        
        // Create new part list and include parts.
        $attributes = factory(PartList::class)->raw([
            'location_id' => $this->user->location->id
        ]);
        
        $response = $this->actingAs($this->dataAdminUser)
            ->call('post', route('part-list.store', $this->user->location), $attributes)
            ->assertSessionHas('alert.message', 'New excluded part numbers list created successfully!')
            ->assertStatus(302);
            
        $errors = session('errors');
        
        if (!empty($errors)) {
            mydd($errors);
        }
        
        $partsCount = count(explode(',', $attributes['parts']));
        $total = $partsCount < 20 ? $partsCount : 20;
        
        $this->actingAs($this->user)
            ->get(route('info.location-parts'))
            ->assertSee($attributes['context'] . 'd')
            ->assertSee("Displaying 1 to $total of $partsCount parts.")
            ->assertStatus(200);
    }
    
    /**
     * Test the aircraft information page.
     *
     * @return void
     */
    public function testAircraftInformation()
    {
        $perpage = 20;
        
        $route = route('info.aircraft');
        $title = 'Aircraft List';
        
        $aircraft = AircraftDetail::search(NULL)
            ->manufacturerCode(NULL)
            ->orderBy('aircraft_fully_qualified_registration_no', 'asc')
            ->paginate($perpage);
        
        $total = $aircraft->total();
        $maxShown = $total > $perpage ? $perpage : $total;
        
        // Order by reg ascending.
        $this->actingAs($this->user)
            ->call('GET', $route . '?orderby=reg&order=asc')
            ->assertStatus(200)
            ->assertSee($title)
            ->assertSee("Displaying 1 to $maxShown of $total aircraft.")
            ->assertSeeText($aircraft->items()[0]->manufacturer_name);
            
        $aircraft = AircraftDetail::search(NULL)
            ->manufacturerCode(NULL)
            ->orderBy('aircraft_fully_qualified_registration_no', 'desc')
            ->paginate($perpage);
            
        // Order by reg descending.
        $this->actingAs($this->user)
            ->call('GET', $route . '?orderby=reg&order=desc')
            ->assertStatus(200)
            ->assertSee($title)
            ->assertSee("Displaying 1 to $maxShown of $total aircraft.")
            ->assertSeeText($aircraft->items()[0]->manufacturer_name);
            
        // Get random aircraft by partial name but make sure there's less than 20 results so it appears on the page.
        // NOTE: This can sometimes make this test take quite a long time.
        do {
            $randomAircraft = AircraftDetail::whereNotNull('manufacturer_name')->inRandomOrder()->first();
            $partialName = substr($randomAircraft->manufacturer_name, 0, 7);
            $resultsCount = AircraftDetail::where('manufacturer_name', 'LIKE', "%$partialName%")->count();
        } while ($resultsCount >= 20);
        
        // Search by partial name.
        $this->actingAs($this->user)
            ->call('GET', $route . "?search=$partialName")
            ->assertStatus(200)
            ->assertSee($title)
            ->assertSeeText($randomAircraft->manufacturer_name);
        
        // Search model id.
        $this->actingAs($this->user)
            ->call('GET', $route . "?search={$randomAircraft->aircraft_model_identifier}")
            ->assertStatus(200)
            ->assertSee($title)
            ->assertSeeText($randomAircraft->manufacturer_name);
            
        $aircraft = AircraftDetail::orderBy('aircraft_model_identifier', 'asc')->get();
        
        // Order by plant code ascending.
        $this->actingAs($this->user)
            ->call('GET', $route . '?orderby=model&order=asc')
            ->assertStatus(200)
            ->assertSee($title)
            ->assertSee("Displaying 1 to $maxShown of $total aircraft.")
            ->assertSee($aircraft->first()->aircraft_model_identifier);
            
        // Order by plant code descending.
        $this->actingAs($this->user)
            ->call('GET', $route . '?orderby=model&order=desc')
            ->assertStatus(200)
            ->assertSee($title)
            ->assertSee("Displaying 1 to $maxShown of $total aircraft.")
            ->assertSee($aircraft->last()->aircraft_model_identifier);
    }
    
    /**
     * Test the RCS Failure Codes Information.
     *
     * @return void
     */
    public function testRcsFailureCodesInformation()
    {
        $warnings = PiecePart::$warnings;
        $route = route('info.rcs-failure-codes');
        $title = 'Received LRU Failure Codes';
        $codes = RcsFailureCode::orderBy('RRC', 'asc')->get();
        $total = $codes->count();
        $perpage = 20;
        $maxShown = $total > $perpage ? $perpage : $total;
        
        // Order by rrc ascending.
        $this->actingAs($this->user)
            ->call('GET', $route . '?orderby=rrc&order=asc')
            ->assertStatus(200)
            ->assertSee($title)
            ->assertSee("Displaying 1 to $maxShown of $total Received LRU Failure Codes.")
            ->assertSee($codes->first()->RRC);
            
        // Make sure failed warning is shown.
        $this->actingAs($this->user)
            ->call('GET', $route . '?rrc=U&fhs=HW&ffc=FT&fcr=CR&ffi=NI')
            ->assertStatus(200)
            ->assertSee($title)
            ->assertSee($warnings['Failed']);
            
        $rrc = substr(str_shuffle('OMS'), 0, 1);
        
        // Make sure not failed warning is shown.
        $this->actingAs($this->user)
            ->call('GET', $route . "?rrc=$rrc")
            ->assertStatus(200)
            ->assertSee($title)
            ->assertSee($warnings['NotFailed']);
    }
    
    /**
     * Test the Shop Action Codes.
     *
     * @return void
     */
    public function testShopActionCodesInformation()
    {
        $route = route('info.shop-action-codes');
        $title = 'Shop Action Codes';
        $codes = ActionCode::orderBy('SAC', 'asc')->get();
        $total = $codes->count();
        $perpage = 20;
        $maxShown = $total > $perpage ? $perpage : $total;
        
        // Order by SAC ascending.
        $this->actingAs($this->user)
            ->call('GET', $route . '?orderby=sac&order=asc')
            ->assertStatus(200)
            ->assertSee($title)
            ->assertSee("Displaying 1 to $maxShown of $total Shop Action Codes.")
            ->assertSee($codes->first()->SAC);
        
        // Order by SAC descending.
        $this->actingAs($this->user)
            ->call('GET', $route . '?orderby=sac&order=desc')
            ->assertStatus(200)
            ->assertSee($title)
            ->assertSee("Displaying 1 to $maxShown of $total Shop Action Codes.")
            ->assertSee($codes->last()->SAC);
    }
    
    /**
     * Test the user roles page returns OK.
     *
     * @return void
     */
    public function testUserRolesInformation()
    {
        $this->actingAs($this->user)
            ->get(route('info.user-roles'))
            ->assertSee('User Roles')
            ->assertStatus(200);
    }
}
