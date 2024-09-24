<?php

namespace App\Http\Controllers;

use App\Alert;
use App\Exports\UtasCodeExport;
use App\Exports\UtasPartNumberExport;
use App\Exports\UtasReasonCodeExport;
use App\Imports\UtasCodeImport;
use App\Imports\UtasPartNumberImport;
use App\Imports\UtasReasonCodeImport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Maatwebsite\Excel\Facades\Excel;

class UtasDataController extends Controller
{
    public $failures = false;
    
    /**
     * Show the form.
     *
     * @return \Illuminate\Http\Response
     */
    public function importUtasCodesForm()
    {
        if (! Gate::allows('manage-utas-data')) {
            abort(403);
        }
        
        return view('utas-data.utas-codes');
    }
    
    /**
     * Import the Utas Codes CSV.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function importUtasCodes(Request $request) 
    {
        if (! Gate::allows('manage-utas-data')) {
            abort(403);
        }
        
        // Validate upload form.
        $request->validate(['file' => 'required|file|mimes:csv,txt']);
        
        try {
            DB::beginTransaction();
            
            // Recommended when importing larger CSVs
            DB::disableQueryLog();
            
            // Using truncate will commit the transaction so we use delete instead.
            DB::delete('DELETE from utas_codes');
            
            DB::statement('ALTER TABLE utas_codes AUTO_INCREMENT = 1');
            (new UtasCodeImport)->import(request()->file('file'), null, \Maatwebsite\Excel\Excel::CSV);
            DB::commit();
        } catch (\Maatwebsite\Excel\Validators\ValidationException $e) {
            $this->failures = $e->failures();
            DB::rollBack();
        }
        
        if ($this->failures) {
            return redirect()
                ->route('utas-data.utas-codes')
                ->with(Alert::warning('Error! Import failed.'))
                ->with('failures', $this->failures);
        }
        
        return redirect()
            ->route('utas-data.utas-codes')
            ->with(Alert::success('CSV data imported successfully!'));
    }
    
    /**
     * Export Utas Codes as CSV file.
     *
     * @return \Illuminate\Http\Response|\Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function exportUtasCodes() 
    {
        if (! Gate::allows('manage-utas-data')) {
            abort(403);
        }
        
        return (new UtasCodeExport)->download('utas-codes.csv', \Maatwebsite\Excel\Excel::CSV, [
            'Content-Type' => 'text/csv',
        ]);
    }
    
    /**
     * Show the form.
     *
     * @return \Illuminate\Http\Response
     */
    public function importUtasPartNumbersForm()
    {
        if (! Gate::allows('manage-utas-data')) {
            abort(403);
        }
        
        return view('utas-data.utas-part-numbers');
    }
    
    /**
     * Import the Utas Codes CSV.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function importUtasPartNumbers(Request $request) 
    {
        if (! Gate::allows('manage-utas-data')) {
            abort(403);
        }
        
        // Validate upload form.
        $request->validate(['file' => 'required|file|mimes:csv,txt']);
        
        try {
            DB::beginTransaction();
            
            // Recommended when importing larger CSVs
            DB::disableQueryLog();
            
            // Using truncate will commit the transaction so we use delete instead.
            DB::delete('DELETE from utas_part_numbers');
            
            DB::statement('ALTER TABLE utas_part_numbers AUTO_INCREMENT = 1');
            (new UtasPartNumberImport)->import(request()->file('file'), null, \Maatwebsite\Excel\Excel::CSV);
            DB::commit();
        } catch (\Maatwebsite\Excel\Validators\ValidationException $e) {
            $this->failures = $e->failures();
            DB::rollBack();
        }
        
        if ($this->failures) {
            return redirect()
                ->route('utas-data.utas-part-numbers')
                ->with(Alert::warning('Error! Import failed.'))
                ->with('failures', $this->failures);
        }
        
        return redirect()
            ->route('utas-data.utas-part-numbers')
            ->with(Alert::success('CSV data imported successfully!'));
    }
    
    /**
     * Export Utas Part Numbers as CSV file.
     *
     * @return \Illuminate\Http\Response|\Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function exportUtasPartNumbers() 
    {
        if (! Gate::allows('manage-utas-data')) {
            abort(403);
        }
        
        return (new UtasPartNumberExport)->download('utas-part-numbers.csv', \Maatwebsite\Excel\Excel::CSV, [
            'Content-Type' => 'text/csv',
        ]);
    }
    
    /**
     * Show the form.
     *
     * @return \Illuminate\Http\Response
     */
    public function importUtasReasonCodesForm()
    {
        if (! Gate::allows('manage-utas-data')) {
            abort(403);
        }
        
        return view('utas-data.utas-reason-codes');
    }
    
    /**
     * Import the Utas Reason Codes CSV.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function importUtasReasonCodes(Request $request) 
    {
        if (! Gate::allows('manage-utas-data')) {
            abort(403);
        }
        
        // Validate upload form.
        $request->validate(['file' => 'required|file|mimes:csv,txt']);
        
        try {
            DB::beginTransaction();
            
            // Recommended when importing larger CSVs
            DB::disableQueryLog();
            
            // Using truncate will commit the transaction so we use delete instead.
            DB::delete('DELETE from utas_reason_codes');
            
            DB::statement('ALTER TABLE utas_reason_codes AUTO_INCREMENT = 1');
            (new UtasReasonCodeImport)->import(request()->file('file'), null, \Maatwebsite\Excel\Excel::CSV);
            DB::commit();
        } catch (\Maatwebsite\Excel\Validators\ValidationException $e) {
            $this->failures = $e->failures();
            DB::rollBack();
        }
        
        if ($this->failures) {
            return redirect()
                ->route('utas-data.utas-reason-codes')
                ->with(Alert::warning('Error! Import failed.'))
                ->with('failures', $this->failures);
        }
        
        return redirect()
            ->route('utas-data.utas-reason-codes')
            ->with(Alert::success('CSV data imported successfully!'));
    }
    
    /**
     * Export Utas Reason Codes as CSV file.
     *
     * @return \Illuminate\Http\Response|\Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function exportUtasReasonCodes() 
    {
        if (! Gate::allows('manage-utas-data')) {
            abort(403);
        }
        
        return (new UtasReasonCodeExport)->download('utas-reason-codes.csv', \Maatwebsite\Excel\Excel::CSV, [
            'Content-Type' => 'text/csv',
        ]);
    }
}
