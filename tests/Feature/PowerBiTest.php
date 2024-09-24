<?php

namespace Tests\Feature;

use App\PowerBiShopFinding;
use App\PowerBiToDoShopFinding;
use Carbon\Carbon;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PowerBiTest extends TestCase
{
    /**
     * Test the Power Bi extraction command.
     *
     * @return void
     */
    public function testPowerBiExtraction()
    {
        Storage::fake('local');
        
        $from = Carbon::now();
        $to = Carbon::now();
        
        $this->createShopFindingsAndPieceParts(10, 'in_progress', 'valid', NULL, $from, $to);
        $this->createShopFindingsAndPieceParts(10, 'subcontracted', 'valid', NULL, $from, $to);
        $this->createShopFindingsAndPieceParts(10, 'complete_scrapped', 'valid', NULL, $from, $to);
        $this->createShopFindingsAndPieceParts(10, 'complete_shipped', 'valid', NULL, $from, $to);
        $this->createShopFindingsAndPieceParts(10, 'in_progress', 'invalid');
        
        Artisan::call('spec2kapp:extract_power_bi_data');
        
        $this->assertEquals('', Artisan::output());
        
        $powerBiShopFindings = DB::table('power_bi_shop_findings')
            ->select('power_bi_shop_findings.*')
            ->get();
            
        $powerBiPieceParts = DB::table('power_bi_piece_parts')
            ->select('power_bi_piece_parts.*')
            ->get();
            
        $this->assertEquals($powerBiShopFindings->count(), 50);
        $this->assertEquals($powerBiPieceParts->count(), true);
        
        // Write files to csv
        Artisan::call('spec2kapp:write_power_bi_tables_to_csv');
        
        // Assert files exist after extract.
        $shopFindingFilename = 'power_bi_shop_findings_' . Carbon::now()->format('d-m-Y') . '.csv';
        $piecePartsFilename = 'power_bi_piece_parts_' . Carbon::now()->format('d-m-Y') . '.csv';
        
        Storage::disk('local')->assertExists(PowerBiShopFinding::POWER_BI_DIRECTORY . DIRECTORY_SEPARATOR . $shopFindingFilename);
        Storage::disk('local')->assertExists(PowerBiShopFinding::POWER_BI_DIRECTORY . DIRECTORY_SEPARATOR . $piecePartsFilename);
        
        // Assert downloads work OK.
        $response = $this->actingAs($this->dataAdminUser)
            ->get(route('power-bi.download', 0))
            ->assertStatus(200);
            
        $this->assertTrue($response->headers->get('content-disposition') == 'attachment; filename=' . $piecePartsFilename);
        
        $response = $this->actingAs($this->dataAdminUser)
            ->get(route('power-bi.download', 1))
            ->assertStatus(200);
            
        $this->assertTrue($response->headers->get('content-disposition') == 'attachment; filename=' . $shopFindingFilename);
    }
    
    /**
     * Test the Power Bi To Do extraction command.
     *
     * @return void
     */
    public function testToDoPowerBiExtraction()
    {
        Storage::fake('local');
        
        $from = Carbon::now();
        $to = Carbon::now();
        
        Artisan::call('spec2kapp:extract_to_do_power_bi_data');
        
        $this->assertEquals('', Artisan::output());
        
        $powerBiShopFindings = DB::table('power_bi_to_do_shop_findings')
            ->select('power_bi_to_do_shop_findings.*')
            ->get();
            
        /*
        $powerBiPieceParts = DB::table('power_bi_to_do_piece_parts')
            ->select('power_bi_to_do_piece_parts.*')
            ->get();
        */
            
        $this->assertEquals($powerBiShopFindings->count(), true);
        //$this->assertEquals($powerBiPieceParts->count(), true);
        
        // Write files to csv
        Artisan::call('spec2kapp:write_to_do_power_bi_tables_to_csv');
        
        // Assert files exist after extract.
        $shopFindingFilename = 'power_bi_to_do_shop_findings_' . Carbon::now()->format('d-m-Y') . '.csv';
        //$piecePartsFilename = 'power_bi_to_do_piece_parts_' . Carbon::now()->format('d-m-Y') . '.csv';
        
        Storage::disk('local')->assertExists(PowerBiToDoShopFinding::POWER_BI_TO_DO_DIRECTORY . DIRECTORY_SEPARATOR . $shopFindingFilename);
        //Storage::disk('local')->assertExists(PowerBiToDoShopFinding::POWER_BI_TO_DO_DIRECTORY . DIRECTORY_SEPARATOR . $piecePartsFilename);
        
        // Assert downloads work OK.
        $response = $this->actingAs($this->dataAdminUser)
            ->get(route('power-bi.download', 0))
            ->assertStatus(200);
            
        //$this->assertTrue($response->headers->get('content-disposition') == 'attachment; filename="' . $piecePartsFilename .'"');
        
        /*
        $response = $this->actingAs($this->dataAdminUser)
            ->get(route('power-bi.download', 1))
            ->assertStatus(200);
            
        
        */
        
        $this->assertTrue($response->headers->get('content-disposition') == 'attachment; filename=' . $shopFindingFilename);
    }
    
    /**
     * Test the power bi index returns ok for data admins only.
     *
     * @return void
     */
    public function testPowerBiIndex()
    {
        $this->actingAs($this->dataAdminUser)
            ->get(route('power-bi.index'))
            ->assertSee('Power BI CSV Files')
            ->assertStatus(200);
            
        $this->actingAs($this->siteAdminUser)
            ->get(route('power-bi.index'))
            ->assertStatus(403);
            
        $this->actingAs($this->adminUser)
            ->get(route('power-bi.index'))
            ->assertStatus(403);
            
        $this->actingAs($this->user)
            ->get(route('power-bi.index'))
            ->assertStatus(403);
    }
}
