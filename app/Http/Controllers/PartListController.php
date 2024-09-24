<?php

namespace App\Http\Controllers;

use App\Alert;
use App\PartList;
use App\Location;
use Illuminate\Http\Request;

class PartListController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $this->authorize('index', PartList::class);
        
        $locations = Location::editable()
            ->with('part_list')
            ->paginate(20);
        
        return view('part-list.index')->with('locations', $locations);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Location $location)
    {
        $this->authorize('edit', $location);
        $this->authorize('create', PartList::class);
        
        return view('part-list.create')->with('location', $location);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, Location $location)
    {
        $this->authorize('edit', $location);
        $this->authorize('create', PartList::class);
        
        $request->validate(PartList::getRules());
        
        $partList = new PartList;
        $partList->location_id = $request->location_id;
        $partList->context = 'exclude'; // Only excluded parts allowed for now.
        $partList->parts = $request->parts;
        $partList->save();
        
        return redirect()->route('part-list.index')
            ->with(Alert::success('New excluded part numbers list created successfully!'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request, PartList $partList)
    {
        $this->authorize('edit', $partList);
        
        return view('part-list.edit')->with('partList', $partList);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, PartList $partList)
    {
        $this->authorize('edit', $partList);
        
        $request->validate(PartList::getRules($partList->id));
        
        $partList->context = 'exclude'; // Only excluded parts allowed for now.
        $partList->parts = $request->parts;
        $partList->save();
        
        return redirect()->route('part-list.index')
            ->with(Alert::success('Excluded part numbers list updated successfully!'));
    }
    
    /**
     * Show the form for deleting the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function delete(Request $request, PartList $partList)
    {
        $this->authorize('delete', $partList);
        
        return view('part-list.delete')->with('partList', $partList);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, PartList $partList)
    {
        $this->authorize('delete', $partList);
        
        $partList->delete();
        
        return redirect()->route('part-list.index')
            ->with(Alert::success('Excluded part numbers list deleted successfully!'));
    }
}
