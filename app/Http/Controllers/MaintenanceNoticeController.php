<?php

namespace App\Http\Controllers;

use App\Alert;
use App\MaintenanceNotice;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class MaintenanceNoticeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $this->authorize('index', MaintenanceNotice::class);
        
        $orderByWhitelist = [
            'title' => 'title',
            'date' => 'updated_at'
        ];
        
        $defaultOrder = 'asc';
        $defaultOrderBy = 'date';
        
        $orderBy = $orderByWhitelist[$defaultOrderBy];
        $order = $defaultOrder;
        
        if ($request->orderby && array_key_exists($request->orderby, $orderByWhitelist)) {
            $orderBy = $orderByWhitelist[$request->orderby];
        }
        
        if ($request->order && in_array($request->order, ['desc', 'asc'])) {
            $order = $request->order;
        }
        
        $search = $request->search ?? NULL;
        
        $maintenanceNotices = MaintenanceNotice::search($search)->orderBy($orderBy, $order)->paginate(20);
        
        $request->merge(['order' => $order == 'asc' ? 'desc' : 'asc'])->flash(); // swap order
        
        return view('maintenance-notice.index')->with('maintenanceNotices', $maintenanceNotices);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $this->authorize('create', MaintenanceNotice::class);
        
        return view('maintenance-notice.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->authorize('create', MaintenanceNotice::class);
        
        $request->validate([
            'title' => 'required|unique:maintenance_notices|max:255',
            'contents' => 'required',
            'display' => 'boolean'
        ]);
        
        $maintenanceNotice = new MaintenanceNotice;
        $maintenanceNotice->title = $request->title;
        $maintenanceNotice->contents = $request->contents;
        $maintenanceNotice->display = $request->has('display') ? $request->display : 0;
        $maintenanceNotice->save();
        
        return redirect(route('maintenance-notice.index'))->with(Alert::success('New Maintenance Notice created successfully!'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $maintenanceNotice = MaintenanceNotice::findOrFail($id);
        
        $this->authorize('edit', $maintenanceNotice);
        
        return view('maintenance-notice.edit')->with('maintenanceNotice', $maintenanceNotice);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $maintenanceNotice = MaintenanceNotice::findOrFail($id);
        
        $this->authorize('edit', $maintenanceNotice);
        
        $request->validate([
            'title' => ['required', 'max:255', Rule::unique('maintenance_notices')->ignore($maintenanceNotice->id)],
            'contents' => 'required',
            'display' => 'boolean'
        ]);
        
        $maintenanceNotice->title = $request->title;
        $maintenanceNotice->contents = $request->contents;
        $maintenanceNotice->display = $request->has('display') ? $request->display : 0;
        $maintenanceNotice->save();
        
        return redirect(route('maintenance-notice.index'))->with(Alert::success('Maintenance Notice updated successfully!'));
    }

    /**
     * Show the form for deleting the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function delete($id)
    {
        $maintenanceNotice = MaintenanceNotice::findOrFail($id);
        
        $this->authorize('delete', $maintenanceNotice);
        
        return view('maintenance-notice.delete')->with('maintenanceNotice', $maintenanceNotice);
    }
    
    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $maintenanceNotice = MaintenanceNotice::findOrFail($id);
        
        $this->authorize('delete', $maintenanceNotice);
        
        $maintenanceNotice->delete();
        
        return redirect(route('maintenance-notice.index'))->with(Alert::success('Maintenance Notice deleted successfully!'));
    }
}
