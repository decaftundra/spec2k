<?php

namespace App\Http\Controllers;

use App\PowerBiShopFinding;
use App\PowerBiToDoShopFinding;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use App\Console\Commands\ExtractPowerBiData;
use App\Console\Commands\ExtractToDoPowerBiData;

class PowerBIController extends Controller
{
    /**
     * The directory where the power bi files are stored.
     *
     * @var string
     */
    protected $directory;
    
    /**
     * The directory where the power bi files are stored.
     *
     * @var string
     */
    protected $toDoDirectory;
    
    /**
     * Set the directory attribute.
     *
     * @return void
     */
    public function __construct()
    {
        $this->directory = PowerBiShopFinding::POWER_BI_DIRECTORY;
        $this->toDoDirectory = PowerBiToDoShopFinding::POWER_BI_TO_DO_DIRECTORY;
    }
    
    /**
     * Display a listing of the power bi csv files.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (!Gate::allows('view-all-locations')) {
            return abort(403, 'You are not authorised to view that page.');
        }
        
        $powerBiFiles = [];
        $toDoPowerBiFiles = [];
        
        if (Storage::disk('local')->exists($this->directory)) {
            $powerBiFiles = $this->getPowerBiFiles();
        }
        
        if (Storage::disk('local')->exists($this->directory)) {
            $toDoPowerBiFiles = $this->getToDoPowerBiFiles();
        }
        
        // Merge...
        $powerBiFiles = array_merge($powerBiFiles, $toDoPowerBiFiles);
        
        return view('power-bi.index')->with('files', $powerBiFiles);
    }
    
    /**
     * Download the power bi files.
     *
     * @param (integer) $id
     * @return \Illuminate\Http\Response
     */
    public function download($id) {
        
        if (!Gate::allows('view-all-locations')) {
            return abort(403, 'You are not authorised to view that page.');
        }
        
        $powerBiFiles = $this->getPowerBiFiles();
        $toDoPowerBiFiles = $this->getToDoPowerBiFiles();
        
        // Merge...
        $powerBiFiles = array_merge($powerBiFiles, $toDoPowerBiFiles);
        
        return response()->download(Storage::disk('local')->path($powerBiFiles[$id]));
    }
    
    /**
     * Get an array of power bi files.
     *
     * @return array
     */
    private function getPowerBiFiles()
    {
        return Storage::disk('local')->allFiles($this->directory);
    }
    
    /**
     * Get an array of to do power bi files.
     *
     * @return array
     */
    private function getToDoPowerBiFiles()
    {
        return Storage::disk('local')->allFiles($this->toDoDirectory);
    }
}
