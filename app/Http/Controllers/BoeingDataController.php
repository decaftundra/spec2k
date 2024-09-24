<?php

namespace App\Http\Controllers;

use App\Alert;
use App\BoeingData;
use App\AircraftDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BoeingDataController extends Controller
{
    /**
     * Show the form for editing the specified resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function edit()
    {
        $this->authorize('create', BoeingData::class);
        
        return view('boeing.edit');
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        $this->authorize('create', BoeingData::class);
        
        $this->validate($request, BoeingData::$rules);
        
        $boeing = new BoeingData();
        
        // Store file with unique timestamp.
        if ($request->file('file')->isValid()) {
            $request->file->storeAs($boeing->getCsvDirectory(), $boeing->getFilename(), 'local');
        } else {
            return redirect(route('boeing.edit'))
                ->with(Alert::error('Sorry the file was invalid. Please try again later.'));
        }
        
        try {
            $boeing->dumpSql();
        } catch (\Exception $e) {
            report($e);
        }
        
        $boeingData = $boeing->getData();
        
        if ($boeingData == false) {
            return redirect(route('boeing.edit'))
                ->with(Alert::error('The CSV file did not contain the expected data columns.'));
        }
        
        if (count($boeingData)) {
            
            $results = DB::transaction(function () use ($boeingData) {
            
                DB::disableQueryLog();
                
                $collection = collect($boeingData);
                
                $aIds = $collection->pluck('aircraft_identification_no')->toArray();
                
                // Get aircraft id numbers that should be updated.
                $updateableIdNos = DB::table('aircraft_details')
                    ->where('manufacturer_name', 'Boeing')
                    ->whereIn('aircraft_identification_no', $aIds)
                    ->pluck('aircraft_identification_no')
                    ->toArray();
                
                // Delete 'updatable' records first.
                DB::table('aircraft_details')
                    ->whereIn('aircraft_identification_no', $updateableIdNos)
                    ->delete();
                
                // Remove all out of service aircraft from collection.
                $activeAircraft = $collection->filter(function($value, $key){
                    return $value['removed_from_service'] == false;
                });
                
                $activeAircraftCleaned = [];
                
                // Unset 'removed_from_service' key and store in new array.
                foreach ($activeAircraft as $key => $value) {
                    unset($value['removed_from_service']);
                    $activeAircraftCleaned[] = $value;
                }
                
                // Insert all active aircraft from csv into database.
                foreach (collect($activeAircraftCleaned)->chunk(100) as $chunk) {
                    DB::table('aircraft_details')->insert($chunk->toArray());
                }
                
                DB::enableQueryLog();
            });
            
            // Redirect on success.
            return redirect(route('boeing.edit'))
                ->with(Alert::success('Boeing data updated successfully!'));
        
        } else {
            return redirect(route('boeing.edit'))
                ->with(Alert::error('No data found in uploaded file! No aircraft details were updated.'));
        }
    }
}
