<?php

namespace Tests\Feature;

use App\Location;
use Carbon\Carbon;
use Tests\TestCase;
use App\HDR_Segment;
use App\XmlExporter;
use Illuminate\Support\Str;
use App\ShopFindings\RCS_Segment;
use App\ShopFindings\ShopFinding;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ExportTest extends TestCase
{
    /**
     * Test for a 200 response from the export index and it is displaying the correct number of records.
     *
     * @return void
     */
    public function testExportIndex()
    {
        $number = mt_rand(1, 25);
        $perPage = 20;
        
        if ($number < 20) {
            $perPage = $number;
        }
        
        $shopFindings = $this->actingAs($this->adminUser)->createValidAndCompleteShopFindingsWithPieceParts($number);
        
        $postVars = [
            'validity' => 'all',
            'location' => 'all',
            'status' => [
                'complete_scrapped',
                'complete_shipped',
            ]
        ];
        
        $this->actingAs($this->adminUser)
            ->call('POST', route('reports.export'), $postVars)
            ->assertStatus(200)
            ->assertSee('Reports to export')
            ->assertSee("Displaying 1 to $perPage of $number datasets.");
    }
    
    /**
     * Test the status filter on the export page.
     *
     * @return void
     */
    public function testStatusFilter()
    {
        $perPage = 20;
        
        $noInProgress = mt_rand(1, 10);
        $noSubcontracted = mt_rand(1, 10);
        $noScrapped = mt_rand(1, 10);
        $noShipped = mt_rand(1, 10);
        
        $from = Carbon::now()->startOfDay();
        $to = Carbon::now()->endOfDay();
        
        $inProgress = $this->createShopFindingsAndPieceParts($noInProgress, 'in_progress', 'valid', NULL, $from, $to);
        $subcontracted = $this->createShopFindingsAndPieceParts($noSubcontracted, 'subcontracted', 'valid', NULL, $from, $to);
        $scrapped = $this->createShopFindingsAndPieceParts($noScrapped, 'complete_scrapped', 'valid', NULL, $from, $to);
        $shipped = $this->createShopFindingsAndPieceParts($noShipped, 'complete_shipped', 'valid', NULL, $from, $to);
        
        $noInProgress = ShopFinding::where('status', 'in_progress')->count();
        $noSubcontracted = ShopFinding::where('status', 'subcontracted')->count();
        $noScrapped = ShopFinding::where('status', 'complete_scrapped')->count();
        $noShipped = ShopFinding::where('status', 'complete_shipped')->count();
        
        $number = ShopFinding::count();
        
        if ($number < 20) {
            $perPage = $number;
        }
        
        $postVars = [
            'validity' => 'all',
            'location' => 'all',
            'status' => [
                'in_progress',
                'subcontracted',
                'complete_scrapped',
                'complete_shipped'
            ]
        ];
        
        $this->actingAs($this->adminUser)
            ->call('POST', route('reports.export'), $postVars)
            ->assertStatus(200)
            ->assertSee('Reports to export')
            ->assertSee("Displaying 1 to $perPage of $number datasets.");
            
        $errors = session('errors');
        
        if (!empty($errors)) {
            mydd($errors);
            var_dump($attributes);
        }
            
        $postVars = [
            'validity' => 'all',
            'location' => 'all',
            'status' => [
                'in_progress'
            ]
        ];
        
        $perPage = 20;
        
        if ($noInProgress < 20) {
            $perPage = $noInProgress;
        }
        
        $this->actingAs($this->adminUser)
            ->call('POST', route('reports.export'), $postVars)
            ->assertStatus(200)
            ->assertSee('Reports to export')
            ->assertSee("Displaying 1 to $perPage of $noInProgress datasets.");
            
        $errors = session('errors');
        
        if (!empty($errors)) {
            mydd($errors);
            var_dump($attributes);
        }
            
        $postVars = [
            'validity' => 'all',
            'location' => 'all',
            'status' => [
                'subcontracted'
            ]
        ];
        
        $perPage = 20;
        
        if ($noSubcontracted < 20) {
            $perPage = $noSubcontracted;
        }
        
        $this->actingAs($this->adminUser)
            ->call('POST', route('reports.export'), $postVars)
            ->assertStatus(200)
            ->assertSee('Reports to export')
            ->assertSee("Displaying 1 to $perPage of $noSubcontracted datasets.");
            
        $errors = session('errors');
        
        if (!empty($errors)) {
            mydd($errors);
            var_dump($attributes);
        }
            
        $postVars = [
            'validity' => 'all',
            'location' => 'all',
            'status' => [
                'complete_shipped'
            ]
        ];
        
        $perPage = 20;
        
        if ($noShipped < 20) {
            $perPage = $noShipped;
        }
        
        $this->actingAs($this->adminUser)
            ->call('POST', route('reports.export'), $postVars)
            ->assertStatus(200)
            ->assertSee('Reports to export')
            ->assertSee("Displaying 1 to $perPage of $noShipped datasets.");
            
        $errors = session('errors');
        
        if (!empty($errors)) {
            mydd($errors);
            var_dump($attributes);
        }
            
        $postVars = [
            'validity' => 'all',
            'location' => 'all',
            'status' => [
                'complete_scrapped'
            ]
        ];
        
        $perPage = 20;
        
        if ($noScrapped < 20) {
            $perPage = $noScrapped;
        }
        
        $this->actingAs($this->adminUser)
            ->call('POST', route('reports.export'), $postVars)
            ->assertStatus(200)
            ->assertSee('Reports to export')
            ->assertSee("Displaying 1 to $perPage of $noScrapped datasets.");
            
        $errors = session('errors');
        
        if (!empty($errors)) {
            mydd($errors);
            var_dump($attributes);
        }
    }
    
    /**
     * Test the validity filter.
     *
     * @return void
     */
    public function testValidityFilter()
    {
        $perPage = 20;
        
        $noValid = mt_rand(1, 10);
        $noInvalid = mt_rand(1, 10);
        
        $valid = $this->createShopFindingsAndPieceParts($noValid, NULL, 'valid');
        $invalid = $this->createShopFindingsAndPieceParts($noInvalid, NULL, 'invalid');
        
        $total = ShopFinding::count();
        
        $valids = ShopFinding::where('is_valid', 1)->count();
        $invalids = ShopFinding::where('is_valid', 0)->count();
        
        if ($total < 20) {
            $perPage = $total;
        }
        
        $postVars = [
            'location' => 'all',
            'validity' => 'all',
            'status' => [
                'in_progress',
                'subcontracted',
                'complete_scrapped',
                'complete_shipped'
            ]
        ];
        
        $this->actingAs($this->adminUser)
            ->call('POST', route('reports.export'), $postVars)
            ->assertStatus(200)
            ->assertSee('Reports to export')
            ->assertSee("Displaying 1 to $perPage of $total datasets.");
            
        $errors = session('errors');
        
        if (!empty($errors)) {
            mydd($errors);
            var_dump($attributes);
        }
            
        $postVars = [
            'location' => 'all',
            'validity' => 'valid',
            'status' => [
                'in_progress',
                'subcontracted',
                'complete_scrapped',
                'complete_shipped'
            ]
        ];
        
        $perPage = 20;
        
        if ($valids < 20) {
            $perPage = $valids;
        }
        
        $this->actingAs($this->adminUser)
            ->call('POST', route('reports.export'), $postVars)
            ->assertStatus(200)
            ->assertSee('Reports to export')
            ->assertSee("Displaying 1 to $perPage of $valids datasets.");
            
        $errors = session('errors');
        
        if (!empty($errors)) {
            mydd($errors);
            var_dump($attributes);
        }
            
        $postVars = [
            'location' => 'all',
            'validity' => 'invalid',
            'status' => [
                'in_progress',
                'subcontracted',
                'complete_scrapped',
                'complete_shipped'
            ]
        ];
        
        $perPage = 20;
        
        if ($invalids < 20) {
            $perPage = $invalids;
        }
        
        $this->actingAs($this->adminUser)
            ->call('POST', route('reports.export'), $postVars)
            ->assertStatus(200)
            ->assertSee('Reports to export')
            ->assertSee("Displaying 1 to $perPage of $invalids datasets.");
            
        $errors = session('errors');
        
        if (!empty($errors)) {
            mydd($errors);
            var_dump($attributes);
        }
    }
    
    /**
     * Test the location filter.
     *
     * @return void
     */
    public function testLocationFilter()
    {
        $locations = Location::inRandomOrder()->take(2)->get();
        
        $perPage = 20;
        
        $noLocation1 = mt_rand(1, 10);
        $noLocation2 = mt_rand(1, 10);
        
        $location1records = $this->createShopFindingsAndPieceParts($noLocation1, NULL, NULL, $locations[0]->plant_code);
        $location2records = $this->createShopFindingsAndPieceParts($noLocation2, NULL, NULL, $locations[1]->plant_code);
        
        $noLocation1 = ShopFinding::where('plant_code', $locations[0]->plant_code)->whereHas('HDR_Segment')->count();
        $noLocation2 = ShopFinding::where('plant_code', $locations[1]->plant_code)->whereHas('HDR_Segment')->count();
        
        $total = ShopFinding::count();
        
        if ($total < 20) {
            $perPage = $total;
        }
        
        $postVars = [
            'location' => 'all',
            'validity' => 'all',
            'status' => [
                'in_progress',
                'subcontracted',
                'complete_scrapped',
                'complete_shipped'
            ]
        ];
        
        $this->actingAs($this->adminUser)
            ->call('POST', route('reports.export'), $postVars)
            ->assertStatus(200)
            ->assertSee('Reports to export')
            ->assertSee("Displaying 1 to $perPage of $total datasets.");
            
        $errors = session('errors');
        
        if (!empty($errors)) {
            mydd($errors);
            var_dump($attributes);
        }
            
        $postVars = [
            'location' => $locations[0]->plant_code,
            'validity' => 'all',
            'status' => [
                'in_progress',
                'subcontracted',
                'complete_scrapped',
                'complete_shipped'
            ]
        ];
        
        $perPage = 20;
        
        if ($noLocation1 < 20) {
            $perPage = $noLocation1;
        }
        
        $response = $this->actingAs($this->adminUser)
            ->call('POST', route('reports.export'), $postVars)
            ->assertStatus(200)
            ->assertSee('Reports to export')
            ->assertSee("Displaying 1 to $perPage of $noLocation1 datasets.");
            
        $errors = session('errors');
        
        if (!empty($errors)) {
            mydd($errors);
            var_dump($attributes);
        }
            
        $postVars = [
            'location' => $locations[1]->plant_code,
            'validity' => 'all',
            'status' => [
                'in_progress',
                'subcontracted',
                'complete_scrapped',
                'complete_shipped'
            ]
        ];
        
        $perPage = 20;
        
        if ($noLocation2 < 20) {
            $perPage = $noLocation2;
        }
        
        $this->actingAs($this->adminUser)
            ->call('POST', route('reports.export'), $postVars)
            //->assertStatus(200)
            ->assertSee('Reports to export')
            ->assertSee("Displaying 1 to $perPage of $noLocation2 datasets.");
            
        $errors = session('errors');
        
        if (!empty($errors)) {
            mydd($errors);
            var_dump($attributes);
        }
    }
    
    /**
     * Test multiple notification id filter.
     *
     * @return void
     */
    public function testMultipleNotificationIdFilter()
    {
        $total = mt_rand(1, 20);
        $noOfResults = mt_rand(1, $total);
        
        $shipped = $this->createShopFindingsAndPieceParts($total, 'complete_shipped', 'valid');
        
        $randomIds = ShopFinding::where('is_valid', true)
            ->where('status', 'complete_shipped')
            ->inRandomOrder()
            ->take($noOfResults)
            ->pluck('id')
            ->toArray();
            
        // Debug.
        if (count($randomIds) != $noOfResults) {
            $debug1 = ShopFinding::with('HDR_Segment')
                ->with('ShopFindingsDetail.AID_Segment')
                ->with('ShopFindingsDetail.EID_Segment')
                ->with('ShopFindingsDetail.API_Segment')
                ->with('ShopFindingsDetail.RCS_Segment')
                ->with('ShopFindingsDetail.SAS_Segment')
                ->with('ShopFindingsDetail.SUS_Segment')
                ->with('ShopFindingsDetail.RLS_Segment')
                ->with('ShopFindingsDetail.LNK_Segment')
                ->with('ShopFindingsDetail.ATT_Segment')
                ->with('ShopFindingsDetail.SPT_Segment')
                ->with('ShopFindingsDetail.Misc_Segment')
                ->whereIn('id', $shipped->pluck('id')->toArray())
                ->where('is_valid', false)
                ->get();
                
            foreach ($debug1 as $shopFinding) {
                mydd($shopFinding->getValidationReport());
            }
        }
        
        $postVars = [
            'validity' => 'all',
            'location' => 'all',
            'status' => [
                'complete_shipped'
            ],
            'notification_ids' => implode(',', $randomIds)
        ];
        
        $this->actingAs($this->adminUser)
            ->call('POST', route('reports.export'), $postVars)
            ->assertStatus(200)
            ->assertSee('Reports to export')
            ->assertSee("Displaying 1 to $noOfResults of $noOfResults datasets.");
            
        $errors = session('errors');
        
        if (!empty($errors)) {
            mydd($errors);
            var_dump($attributes);
        }
    }
    
    /**
     * Test multiple part number filter.
     *
     * @return void
     */
    public function testMultiplePartNosFilter()
    {
        $total = mt_rand(1, 20);
        $noOfResults = mt_rand(1, $total);
        
        $shipped = $this->createShopFindingsAndPieceParts($total, 'complete_shipped', 'valid');
        
        $randomPartNos = RCS_Segment::inRandomOrder()
            ->take($noOfResults)
            ->pluck('MPN')
            ->toArray();
        
        $postVars = [
            'validity' => 'all',
            'location' => 'all',
            'status' => [
                'complete_shipped'
            ],
            'part_nos' => implode(',', $randomPartNos)
        ];
        
        $this->actingAs($this->adminUser)
            ->call('POST', route('reports.export'), $postVars)
            ->assertStatus(200)
            ->assertSee('Reports to export')
            ->assertSee("Displaying 1 to $noOfResults of $noOfResults datasets.");
            
        $errors = session('errors');
        
        if (!empty($errors)) {
            mydd($errors);
            var_dump($attributes);
        }
    }
    
    /**
     * Test date range filter.
     *
     * @return void
     */
    public function testDateRangeFilter()
    {
        $perPage = 20;
        
        $noBefore = mt_rand(1, 10);
        $noDuring = mt_rand(1, 10);
        $noAfter = mt_rand(1, 10);
        
        $total = $noBefore + $noDuring + $noAfter;
        
        $before = Carbon::now()->subMonths(24);
        $from = Carbon::now()->subMonths(18)->startOfDay();
        $to = Carbon::now()->subMonths(12)->endOfDay();
        $after = Carbon::now();
        
        $sfBefore = $this->createShopFindingsAndPieceParts($noBefore, 'complete_shipped', 'valid', NULL, $before, $from);
        $sfDuring = $this->createShopFindingsAndPieceParts($noDuring, 'complete_shipped', 'valid', NULL, $from, $to);
        $sfAfter = $this->createShopFindingsAndPieceParts($noAfter, 'complete_shipped', 'valid', NULL, $to, $after);
        
        $noDuring = ShopFinding::where('status', 'complete_shipped')->whereBetween('shipped_at', [$from, $to])->count();
        $noDuringValid = ShopFinding::where('is_valid', true)->where('status', 'complete_shipped')->whereBetween('shipped_at', [$from, $to])->count();
        
        // Very occasionally this test fails because no shopfindings are found. Debug here...
        if ($noDuring == 0) {
            
            mydd($before);
            mydd($from);
            mydd($to);
            mydd($after);
            
            mydd(ShopFinding::get());
            
            dd('No shopfindings found. Check test... ExportTest - testDateRangeFilter');
        }
        
        if ($noDuring < 20) {
            $perPage = $noDuring;
        }
        
        $postVars = [
            'location' => 'all',
            'validity' => 'valid',
            'status' => [
                'complete_shipped',
            ],
            'date_start' => $from->format('d/m/Y'),
            'date_end' => $to->format('d/m/Y')
        ];
        
        // Debug.
        if ($noDuringValid != $noDuring) {
            $debug1 = ShopFinding::with('HDR_Segment')
                ->with('ShopFindingsDetail.AID_Segment')
                ->with('ShopFindingsDetail.EID_Segment')
                ->with('ShopFindingsDetail.API_Segment')
                ->with('ShopFindingsDetail.RCS_Segment')
                ->with('ShopFindingsDetail.SAS_Segment')
                ->with('ShopFindingsDetail.SUS_Segment')
                ->with('ShopFindingsDetail.RLS_Segment')
                ->with('ShopFindingsDetail.LNK_Segment')
                ->with('ShopFindingsDetail.ATT_Segment')
                ->with('ShopFindingsDetail.SPT_Segment')
                ->with('ShopFindingsDetail.Misc_Segment')
                ->where('status', 'complete_shipped')
                ->whereBetween('shipped_at', [$from, $to])
                ->where('is_valid', false)
                ->get();
                
            foreach ($debug1 as $shopFinding) {
                mydd($shopFinding->getValidationReport());
            }
        }
        
        $this->actingAs($this->adminUser)
            ->call('POST', route('reports.export'), $postVars)
            ->assertStatus(200)
            ->assertSee('Reports to export')
            ->assertSee("Displaying 1 to $perPage of $noDuring datasets.")
            ->assertSee('Shop Findings XML')
            ->assertSee('Piece Parts XML')
            ->assertSee('Zip');
            
        $errors = session('errors');
        
        if (!empty($errors)) {
            mydd($errors);
            var_dump($attributes);
        }
    }
    
    /**
     * Test the xml downloads.
     *
     * @return void
     */
    public function testXMLDownloads()
    {
        $noDuring = mt_rand(1, 10);
        
        $from = Carbon::now()->subMonths(18)->startOfDay();
        $to = Carbon::now()->subMonths(12)->endOfDay();
        
        $sfDuring = $this->createShopFindingsAndPieceParts($noDuring, 'complete_shipped', 'valid', NULL, $from, $to);
        
        $postVars = [
            'location' => 'all',
            'validity' => 'valid',
            'status' => [
                'complete_shipped',
            ],
            'date_start' => $from->format('d/m/Y'),
            'date_end' => $to->format('d/m/Y'),
            'download-sf' => 1
        ];
        
        Carbon::setTestNow(Carbon::now());
        
        $response = $this->actingAs($this->adminUser)
            ->call('POST', route('reports.export'), $postVars)
            ->assertStatus(200);
            
        $filename = 'SHOP2K-SH' . Carbon::getTestNow()->format('ymd') . '.xml';
            
        $this->assertTrue($response->headers->get('content-disposition') == 'attachment; filename='. $filename);
        
        Carbon::setTestNow(); // Reset time.
        
        $postVars = [
            'location' => 'all',
            'validity' => 'valid',
            'status' => [
                'complete_shipped',
            ],
            'date_start' => $from->format('d/m/Y'),
            'date_end' => $to->format('d/m/Y'),
            'download-pp' => 1
        ];
        
        Carbon::setTestNow(Carbon::now());
        
        $response = $this->actingAs($this->adminUser)
            ->call('POST', route('reports.export'), $postVars)
            ->assertStatus(200);
            
        $filename = 'PP2K-PP' . Carbon::getTestNow()->format('ymd') . '.xml';
            
        $this->assertTrue($response->headers->get('content-disposition') == 'attachment; filename='. $filename);
            
        Carbon::setTestNow(); // Reset time.
        
        $postVars = [
            'location' => 'all',
            'validity' => 'valid',
            'status' => [
                'complete_shipped',
            ],
            'date_start' => $from->format('d/m/Y'),
            'date_end' => $to->format('d/m/Y'),
            'download-zip' => 1
        ];
        
        Carbon::setTestNow(Carbon::now());
        
        $response = $this->actingAs($this->adminUser)
            ->call('POST', route('reports.export'), $postVars)
            ->assertStatus(200);
            
        $name = auth()->check() ? auth()->user()->fullname : 'app';
        $id = auth()->check() ? auth()->id() : 0;
        
        $filename = 'xml-export-' . Carbon::getTestNow()->timestamp . '-' . $id . '-' . Str::slug($name) . '.zip';
        
        // Needs "" around filename here for some reason.
        $this->assertTrue($response->headers->get('content-disposition') == 'attachment; filename=' . $filename);
            
        Carbon::setTestNow(); // Reset time.
    }
    
    /**
     * Test the xml downloads.
     *
     * @return void
     */
    public function testXMLDownloadWithoutWPSSegment()
    {
        $noDuring = mt_rand(1, 10);
        
        $from = Carbon::now()->subMonths(18)->startOfDay();
        $to = Carbon::now()->subMonths(12)->endOfDay();
        
        $sfDuring = $this->createShopFindingsAndPiecePartsWithoutWPSSegment(1, 'complete_shipped', 'invalid', NULL, $from, $to);
        
        $postVars = [
            'location' => 'all',
            'validity' => 'invalid',
            'status' => [
                'complete_shipped',
            ],
            'date_start' => $from->format('d/m/Y'),
            'date_end' => $to->format('d/m/Y'),
            'download-sf' => 1
        ];
        
        Carbon::setTestNow(Carbon::now());
        
        $response = $this->actingAs($this->adminUser)
            ->call('POST', route('reports.export'), $postVars)
            ->assertStatus(200);
            
        $filename = 'SHOP2K-SH' . Carbon::getTestNow()->format('ymd') . '.xml';
            
        $this->assertTrue($response->headers->get('content-disposition') == 'attachment; filename='. $filename);
        
        Carbon::setTestNow(); // Reset time.
        
        $postVars = [
            'location' => 'all',
            'validity' => 'invalid',
            'status' => [
                'complete_shipped',
            ],
            'date_start' => $from->format('d/m/Y'),
            'date_end' => $to->format('d/m/Y'),
            'download-pp' => 1
        ];
        
        Carbon::setTestNow(Carbon::now());
        
        $response = $this->actingAs($this->adminUser)
            ->call('POST', route('reports.export'), $postVars)
            ->assertStatus(200);
            
        $filename = 'PP2K-PP' . Carbon::getTestNow()->format('ymd') . '.xml';
            
        $this->assertTrue($response->headers->get('content-disposition') == 'attachment; filename='. $filename);
            
        Carbon::setTestNow(); // Reset time.
        
        $postVars = [
            'location' => 'all',
            'validity' => 'invalid',
            'status' => [
                'complete_shipped',
            ],
            'date_start' => $from->format('d/m/Y'),
            'date_end' => $to->format('d/m/Y'),
            'download-zip' => 1
        ];
        
        Carbon::setTestNow(Carbon::now());
        
        $response = $this->actingAs($this->adminUser)
            ->call('POST', route('reports.export'), $postVars)
            ->assertStatus(200);
            
        $name = auth()->check() ? auth()->user()->fullname : 'app';
        $id = auth()->check() ? auth()->id() : 0;
        
        $filename = 'xml-export-' . Carbon::getTestNow()->timestamp . '-' . $id . '-' . Str::slug($name) . '.zip';
        
        // Needs "" around filename here for some reason.
        $this->assertTrue($response->headers->get('content-disposition') == 'attachment; filename=' . $filename);
            
        Carbon::setTestNow(); // Reset time.
    }
    
    /**
     * Test encoded string query request.
     *
     * @return void
     */
    public function testEncodedQueryString()
    {
        $perPage = 20;
        
        $noDuring = mt_rand(1, 10);
        
        $from = Carbon::now()->subMonths(18)->startOfDay();
        $to = Carbon::now()->subMonths(12)->endOfDay();
        
        $sfDuring = $this->createShopFindingsAndPieceParts($noDuring, 'complete_shipped', 'valid', NULL, $from, $to);
        
        $noDuring = ShopFinding::where('status', 'complete_shipped')->whereBetween('shipped_at', [$from, $to])->count();
        
        $noDuring2 = ShopFinding::where('status', 'complete_shipped')->whereBetween('shipped_at', [$from, $to])->get();
        
        // Debug.
        if (count($noDuring2) != $noDuring) {
            $debug1 = ShopFinding::with('HDR_Segment')
                ->with('ShopFindingsDetail.AID_Segment')
                ->with('ShopFindingsDetail.EID_Segment')
                ->with('ShopFindingsDetail.API_Segment')
                ->with('ShopFindingsDetail.RCS_Segment')
                ->with('ShopFindingsDetail.SAS_Segment')
                ->with('ShopFindingsDetail.SUS_Segment')
                ->with('ShopFindingsDetail.RLS_Segment')
                ->with('ShopFindingsDetail.LNK_Segment')
                ->with('ShopFindingsDetail.ATT_Segment')
                ->with('ShopFindingsDetail.SPT_Segment')
                ->with('ShopFindingsDetail.Misc_Segment')
                ->whereIn('id', $noDuring2->pluck('id')->toArray())
                ->where('is_valid', false)
                ->get();
                
            foreach ($debug1 as $shopFinding) {
                mydd($shopFinding->getValidationReport());
            }
        }
        
        if ($noDuring < 20) {
            $perPage = $noDuring;
        }
  
        $postVars = [
            'location' => 'all',
            'validity' => 'valid',
            'status' => [
                'complete_shipped',
            ],
            'date_start' => $from->format('d/m/Y'),
            'date_end' => $to->format('d/m/Y'),
            'download-zip' => 1
        ];
        
        $encodedString = app()->call('App\XmlExporter@encodeQueryString', ['input' => $postVars]);
        
        $this->actingAs($this->adminUser)
            ->get(route('reports.export') . '?encoded=' . $encodedString)
            ->assertStatus(200)
            ->assertSee('Reports to export')
            ->assertSee("Displaying 1 to $noDuring of $noDuring datasets.")
            ->assertSee('Shop Findings XML')
            ->assertSee('Piece Parts XML')
            ->assertSee('Zip');
    }
}
