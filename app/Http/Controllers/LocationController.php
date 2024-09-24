<?php

namespace App\Http\Controllers;

use App\Alert;
use App\CageCode;
use App\Location;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class LocationController extends Controller
{
    /**
     * An array of all timezones.
     *
     * @var array
     */
    protected $timezones;
    
    /**
     * Redirect url.
     *
     * @var string
     */
    protected $redirectTo;
    
    /**
     * Set the timezones array.
     *
     * @return void
     */
    public function __construct()
    {
        $this->timezones = \DateTimeZone::listIdentifiers(\DateTimeZone::ALL);
        $this->cageCodes = CageCode::orderBy('cage_code', 'asc')->pluck('cage_code', 'id')->toArray();
        
        $this->redirectTo = route('location.index');
    }
    
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $this->authorize('index', Location::class);
        
        $orderByWhitelist = [
            'name' => 'name',
            'sap_name' => 'sap_location_name',
            'code' => 'plant_code',
            'timezone' => 'timezone'
        ];
        
        $defaultOrder = 'asc';
        $defaultOrderBy = 'name';
        
        $orderBy = $orderByWhitelist[$defaultOrderBy];
        $order = $defaultOrder;
        
        if ($request->orderby && array_key_exists($request->orderby, $orderByWhitelist)) {
            $orderBy = $orderByWhitelist[$request->orderby];
        }
        
        if ($request->order && in_array($request->order, ['desc', 'asc'])) {
            $order = $request->order;
        }
        
        $search = $request->search ?? NULL;
        
        $locations = Location::editable()
            ->with('users')
            ->with('cage_codes')
            ->search($search)
            ->orderBy($orderBy, $order)
            ->paginate(20);
        
        $request->merge(['order' => $order == 'asc' ? 'desc' : 'asc'])->flash(); // swap order
        
        return view('location.index')
            ->with('locations', $locations);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $this->authorize('create', Location::class);
        
        return view('location.create')
            ->with('timezones', $this->timezones)
            ->with('cageCodes', $this->cageCodes);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->authorize('create', Location::class);
        
        $attributes = [
            'sap_location_name' => 'ERP Name'
        ];
        
        $request->validate([
            'plant_code' => 'numeric|required|max:9999|unique:locations',
            'name' => 'required|string|unique:locations',
            'sap_location_name' => 'required|string|unique:locations',
            'timezone' => ['required', 'string', Rule::in($this->timezones)],
            'cage_codes' => 'array'
        ], [], $attributes);
        
        $location = new Location;
        $location->sap_location_name = $request->sap_location_name;
        $location->name = $request->name;
        $location->plant_code = $request->plant_code;
        $location->timezone = $request->timezone;
        $location->save();
        
        $location->cage_codes()->sync($request->cage_codes);
        
        return redirect(route('location.index'))
            ->with(Alert::success('New repair station created successfully!'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Location  $location
     * @return \Illuminate\Http\Response
     */
    public function edit(Location $location)
    {
        $this->authorize('edit', $location);
        
        return view('location.edit')
            ->with('location', $location)
            ->with('timezones', $this->timezones)
            ->with('cageCodes', $this->cageCodes)
            ->with('locationCageCodes', $location->cage_codes->pluck('id')->toArray());
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Location  $location
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Location $location)
    {
        $this->authorize('edit', $location);
        
        $attributes = [
            'sap_location_name' => 'ERP Name'
        ];
        
        $request->validate([
            'plant_code' => ['numeric', 'required', 'max:9999', Rule::unique('locations')->ignore($location->id)],
            'name' => ['required', 'string', Rule::unique('locations')->ignore($location->id)],
            'sap_location_name' => ['required', 'string', Rule::unique('locations')->ignore($location->id)],
            'timezone' => ['required', 'string', Rule::in($this->timezones)],
            'cage_codes' => 'array'
        ], [], $attributes);
        
        $location->plant_code = $request->plant_code;
        $location->sap_location_name = $request->sap_location_name;
        $location->name = $request->name;
        $location->timezone = $request->timezone;
        $location->save();
        
        $location->cage_codes()->sync($request->cage_codes);
        
        return redirect($this->redirectTo)
            ->with(Alert::success('Repair Station updated successfully!'));
    }

    /**
     * Show the form for deleting the specified resource.
     *
     * @param  \App\Location  $location
     * @return \Illuminate\Http\Response
     */
    public function delete(Location $location)
    {
        $this->authorize('delete', $location);
        
        // Don't allow deletion of location if users are related to it.
        if ($location->users->count()) {
            return redirect($this->redirectTo)
                ->with(Alert::error('Error, there are users related to this repair station so it cannot be deleted.'));
        }
        
        return view('location.delete')->with('location', $location);
    }
    
    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Location  $location
     * @return \Illuminate\Http\Response
     */
    public function destroy(Location $location)
    {
        $this->authorize('delete', $location);
        
        // Don't allow deletion of location if users are related to it.
        if ($location->users->count()) {
            return redirect($this->redirectTo)
                ->with(Alert::error('Error, there are users related to this repair station so it cannot be deleted.'));
        }
        
        $location->cage_codes()->sync([]);
        
        $location->delete();
        
        return redirect($this->redirectTo)
            ->with(Alert::success('Repair Station deleted successfully!'));
    }
}
