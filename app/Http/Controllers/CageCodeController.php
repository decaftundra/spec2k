<?php

namespace App\Http\Controllers;

use App\Alert;
use App\CageCode;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class CageCodeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $this->authorize('view-all-cage-codes');
        
        $orderByWhitelist = [
            'code' => 'cage_code',
            'info' => 'info'
        ];
        
        $defaultOrder = 'asc';
        $defaultOrderBy = 'code';
        
        $orderBy = $orderByWhitelist[$defaultOrderBy];
        $order = $defaultOrder;
        
        if ($request->orderby && array_key_exists($request->orderby, $orderByWhitelist)) {
            $orderBy = $orderByWhitelist[$request->orderby];
        }
        
        if ($request->order && in_array($request->order, ['desc', 'asc'])) {
            $order = $request->order;
        }
        
        $search = $request->search ?? NULL;
        
        $cageCodes = CageCode::search($search)
            ->orderBy($orderBy, $order)
            ->paginate(20);
        
        $request->merge(['order' => $order == 'asc' ? 'desc' : 'asc'])->flash(); // swap order
        
        return view('cage-code.index')
            ->with('cageCodes', $cageCodes);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $this->authorize('view-all-cage-codes');
        
        return view('cage-code.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->authorize('view-all-cage-codes');
        
        $request->validate([
            'cage_code' => ['required', 'unique:cage_codes', 'min:3', 'max:5', Rule::notIn(['ZZZZZ', 'zzzzz'])],
             'info' => 'nullable|string|max:500'
        ]);
        
        $cageCode = new CageCode;
        $cageCode->cage_code = $request->cage_code;
        $cageCode->info = $request->info;
        $cageCode->save();
        
        return redirect(route('cage-code.index'))
            ->with(Alert::success('New cage code created successfully!'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\CageCode  $cageCode
     * @return \Illuminate\Http\Response
     */
    public function edit(CageCode $cageCode)
    {
        $this->authorize('view-all-cage-codes');
        
        return view('cage-code.edit')->with('cageCode', $cageCode);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\CageCode  $cageCode
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, CageCode $cageCode)
    {
        $this->authorize('view-all-cage-codes');
        
        $request->validate([
            'cage_code' => ['required', Rule::unique('cage_codes')->ignore($cageCode->id), 'min:3', 'max:5', Rule::notIn(['ZZZZZ', 'zzzzz'])],
            'info' => 'nullable|string|max:500'
        ]);
        
        $cageCode->cage_code = $request->cage_code;
        $cageCode->info = $request->info;
        $cageCode->save();
        
        return redirect(route('cage-code.index'))
            ->with(Alert::success('Cage code updated successfully!'));
    }

    /**
     * Show the form for deleting the specified resource.
     *
     * @param  \App\CageCode  $cageCode
     * @return \Illuminate\Http\Response
     */
    public function delete(CageCode $cageCode)
    {
        $this->authorize('view-all-cage-codes');
        
        if ($cageCode->locations->count()) {
            return redirect(route('cage-code.index'))
                ->with(Alert::error('Error, this cage code is related to at least one location, so it cannot be deleted.'));
        }
        
        return view('cage-code.delete')->with('cageCode', $cageCode);
    }
    
    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\CageCode  $cageCode
     * @return \Illuminate\Http\Response
     */
    public function destroy(CageCode $cageCode)
    {
        $this->authorize('view-all-cage-codes');
        
        if ($cageCode->locations->count()) {
            return redirect(route('cage-code.index'))
                ->with(Alert::error('Error, this cage code is related to at least one location, so it cannot be deleted.'));
        }
        
        $cageCode->delete();
        
        return redirect(route('cage-code.index'))
            ->with(Alert::success('Cage code deleted successfully!'));
    }
}
