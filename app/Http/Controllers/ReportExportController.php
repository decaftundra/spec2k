<?php

namespace App\Http\Controllers;

use App\Location;
use Carbon\Carbon;
use App\HDR_Segment;
use App\XmlExporter;
use App\ValidationProfiler;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\PieceParts\PiecePart;
use Illuminate\Http\Response;
use App\ShopFindings\ShopFinding;
use Illuminate\Support\Facades\Storage;

class ReportExportController extends Controller
{
    /**
     * Whitelist of allowed orderby parameters.
     *
     * @var array
     */
    public static $orderbyWhitelist = [
        'id' => 'id',
        'status' => 'status',
        'acronym' => 'acronym',
        'RCS_MPN' => 'RCS_MPN',
        'RCS_SER' => 'RCS_SER',
        'HDR_ROC' => 'HDR_ROC',
        'HDR_RON' => 'HDR_RON',
        'SUS_SER' => 'SUS_SER',
        //'piece_part_count' => 'piece_part_count',
        'is_utas' => 'is_utas',
        'ship_scrap_date' => 'ship_scrap_date',
        'is_valid' => 'is_valid'
    ];
    
    /**
     * The default order by column.
     *
     * @var string
     */
    public static $defaultOrderBy = 'id';
    
    /**
     * The default order.
     *
     * @var string
     */
    public static $defaultOrder = 'asc';
    
    /**
     * The XmlExporter instance.
     *
     * @var XmlExporter
     */
    protected $xmlExporter;
    
    /**
     * Set the xmlExporter property.
     *
     * @param  \App\XmlExporter $xmlExporter
     * @return void
     */
    public function __construct(XmlExporter $xmlExporter)
    {
        $this->xmlExporter = $xmlExporter;
    }
    
    /**
     * Show the form and list of reports for export.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $orderby = self::$orderbyWhitelist[self::$defaultOrderBy];
        $order = self::$defaultOrder;
        
        if ($request->has('orderby') && array_key_exists($request->orderby, self::$orderbyWhitelist)) {
            $orderby = self::$orderbyWhitelist[$request->orderby];
        }
        
        if ($request->has('order') && in_array($request->order, ['desc', 'asc'])) {
            $order = $request->order;
        }
        
        if (!empty($request->all()) && !$request->has('encoded') && !$request->has('reset')) {
            $this->validate($request, [
                'validity' => 'required|in:all,valid,invalid',
                'location' => 'required',
                'status' => 'required|array',
                'part_nos' => 'nullable|string',
                'notification_ids' => 'nullable|string',
                'date_start' => 'nullable|date_format:d/m/Y',
                'date_end' => 'nullable|date_format:d/m/Y|after:date_start',
                'download-sf' => 'nullable',
                'download-pp' => 'nullable',
                'download-zip' => 'nullable'
            ]);
        }
        
        if ($request->has('encoded') && strlen($request->encoded)) {
            $inputArray = $this->xmlExporter->decodeQueryString($request->encoded);
            $request->offsetUnset('encoded');
            $request->merge($inputArray);
        }
        
        // We use this to include in the info.txt file.
        $encodedQueryString = $this->xmlExporter->encodeQueryString($request->all());
        
        $notificationIds = [];
        $partNos = [];
        
        if ($request->has('part_nos') && !is_null($request->part_nos)) {
            $partNos = $this->xmlExporter->delimitedToArray($request->part_nos);
        }
        
        if ($request->has('notification_ids') && !is_null($request->notification_ids)) {
            $notificationIds = $this->xmlExporter->delimitedToArray($request->notification_ids);
        }
        
        $locations = Location::filter('view-all-shopfindings');
        
        $statuses = ShopFinding::$statuses;
        
        if (auth()->check()) {
            $defaultLocation = auth()->user()->location->plant_code;
        }
        
        // If there are no headers saved the locations dropdown will be empty, so add user's default.
        if (empty($locations)) {
            $locations = [
                $defaultLocation => auth()->user()->location->plant_code
            ];
        }
        
        $defaultLocation = in_array($defaultLocation, $locations) ? $defaultLocation : 'all';
        
        $validity = $request->validity ?? 'valid';
        $location = $request->location ?? $defaultLocation;
        $status = $request->status ?? ['complete_scrapped', 'complete_shipped'];
        $from = $request->date_start ?? NULL;
        $to = $request->date_end ?? NULL;
        
        $allRecords = ShopFinding::getExportList($location, $status, $validity, $notificationIds, $partNos, $from, $to, $orderby, $order);
            
        if ($request->has('date_start') && $request->has('date_end') && count($allRecords)) {
            
            $allRecordIds = $allRecords->pluck('id')->toArray();
            
            try {
                $from = Carbon::createFromFormat('d/m/Y', $request->date_start) ?? NULL;
            } catch (\InvalidArgumentException $e) {
                $from = NULL;
            }
            
            try {
                $to = Carbon::createFromFormat('d/m/Y', $request->date_end) ?? NULL;
            } catch (\InvalidArgumentException $e) {
                $to = NULL;
            }
            
            if ($request->has('download-sf')) {
                return $this->downloadShopFindingsXml($allRecordIds, $from, $to);
            }
            
            if ($request->has('download-pp')) {
                return $this->downloadPiecePartsXml($allRecordIds, $from, $to);
            }
            
            if ($request->has('download-zip')) {
                return $this->downloadZip($allRecordIds, $from, $to, $encodedQueryString);
            }
        }
        
        $request->merge(['order' => $order == 'asc' ? 'desc' : 'asc'])->flash(); // swap order
        
        return view('export.index')
            ->with('defaultLocation', $defaultLocation)
            ->with('locations', $locations)
            ->with('statuses', $statuses)
            ->with('allRecords', $allRecords);
    }
    
    /**
     * Download the shop findings xml file.
     *
     * @param  array  $allRecordIds
     * @param  \Carbon\Carbon  $from
     * @param  \Carbon\Carbon  $to
     * @return \Illuminate\Http\Response
     */
    public function downloadShopFindingsXml($allRecordIds, Carbon $from, Carbon $to)
    {
        $allRecords = $this->getRecords($allRecordIds);
        
        $xml = $this->xmlExporter->createShopFindingsXmlFile($allRecords, $from, $to);
        
        return response($xml)
            ->header('Cache-Control', 'public')
            ->header('Content-Description', 'File Transfer')
            ->header('Content-Disposition', 'attachment; filename='.$this->xmlExporter->getShopFindingsFilename())
            ->header('Content-Transfer-Encoding', 'binary')
            ->header('Content-Type', 'text/xml');
    }
    
    /**
     * Download the piece parts xml file.
     *
     * @param  array  $allRecordIds
     * @param  \Carbon\Carbon  $from
     * @param  \Carbon\Carbon  $to
     * @return \Illuminate\Http\Response
     */
    public function downloadPiecePartsXml($allRecordIds, Carbon $from, Carbon $to)
    {
        $allRecords = $this->getRecords($allRecordIds);
        
        $xml = $this->xmlExporter->createPiecePartsXmlFile($allRecords, $from, $to);
        
        return response($xml)
            ->header('Cache-Control', 'public')
            ->header('Content-Description', 'File Transfer')
            ->header('Content-Disposition', 'attachment; filename='.$this->xmlExporter->getPiecePartsFilename())
            ->header('Content-Transfer-Encoding', 'binary')
            ->header('Content-Type', 'text/xml');
    }
    
    /**
     * Download and delete the zip file.
     *
     * @param  array  $allRecordIds
     * @param  \Carbon\Carbon  $from
     * @param  \Carbon\Carbon  $to
     * @param  string  $encodedString
     * @return \Illuminate\Http\Response
     */
    public function downloadZip($allRecordIds, Carbon $from, Carbon $to, $encodedString)
    {
        $allRecords = $this->getRecords($allRecordIds);
        
        $filePath = $this->xmlExporter->createZipArchive($allRecords, $from, $to, $encodedString);
        
        return response()->download($filePath)->deleteFileAfterSend(true);
    }
    
    /**
     * Get an eloquent collection of shopfindings...
     *
     * @param (array) $allRecordIds
     * @return Illuminate\Database\Eloquent\Collection
     */
    protected function getRecords($allRecordIds)
    {
        return Shopfinding::with('HDR_Segment')
            ->with('ShopFindingsDetail.RCS_Segment')
            ->with('ShopFindingsDetail.SAS_Segment')
            ->with('ShopFindingsDetail.SUS_Segment')
            ->with('ShopFindingsDetail.RLS_Segment')
            ->with('ShopFindingsDetail.LNK_Segment')
            ->with('ShopFindingsDetail.AID_Segment')
            ->with('ShopFindingsDetail.EID_Segment')
            ->with('ShopFindingsDetail.API_Segment')
            ->with('ShopFindingsDetail.ATT_Segment')
            ->with('ShopFindingsDetail.SPT_Segment')
            ->with('ShopFindingsDetail.Misc_Segment')
            ->with('PiecePart.PiecePartDetails.WPS_Segment')
            ->with('PiecePart.PiecePartDetails.NHS_Segment')
            ->with('PiecePart.PiecePartDetails.RPS_Segment')
            ->whereIn('id', $allRecordIds)
            ->get();
    }
}
