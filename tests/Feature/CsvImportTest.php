<?php

namespace Tests\Feature;

use App\Notification;
use App\NotificationPiecePart;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class CsvImportTest extends TestCase
{
    //$this->markTestSkipped('Needs updating as now using upserts and deleting saved segments.');
    
    protected $shopFindingsCsvFile = 'shop_findings_legacy_importation_example.csv';
    
    protected $piecePartsCsvFile = 'piece_parts_legacy_importation_example.csv';
    
    /**
     * Test the shop findings csv legacy data uploads without any errors.
     *
     * @return void
     */
    public function testShopFindingCsvImport()
    {
        $this->actingAs($this->dataAdminUser)
            ->get(route('csv-importer.create'))
            ->assertSee('Import CSV Data')
            ->assertStatus(200);
            
        // Test using real file.
        $attributes = [
            'shopfindings_file' => new UploadedFile(storage_path('app/legacy-csv-data/' . $this->shopFindingsCsvFile), $this->shopFindingsCsvFile, 'text/csv', NULL, true)
        ];
        
        // Mock the time.
        $time = Carbon::now();
        Carbon::setTestNow($time);
        
        $response = $this->actingAs($this->dataAdminUser)
            ->call('POST', route('csv-importer.store'), $attributes)->assertStatus(302);
            
        $errors = session('errors');
        
        if (!empty($errors)) {
            mydd($errors);
            var_dump($attributes);
            die();
        }
        
        $this->get($response->headers->get('Location'))
            ->assertSee('CSV data imported successfully!');
            
        $importedNotifications = Notification::where('is_csv_import', 1)->get();
        
        $this->assertGreaterThan(0, $importedNotifications->count());
    }
    
    /**
     * Test the shop findings csv legacy data uploads without any errors.
     *
     * @return void
     */
    public function testPiecePartCsvImport()
    {
        $this->actingAs($this->dataAdminUser)
            ->get(route('csv-importer.create'))
            ->assertSee('Import CSV Data')
            ->assertStatus(200);
            
        // Test using real file.
        $attributes = [
            'pieceparts_file' => new UploadedFile(storage_path('app/legacy-csv-data/' . $this->piecePartsCsvFile), $this->piecePartsCsvFile, 'text/csv', NULL, true)
        ];
        
        // Mock the time.
        $time = Carbon::now();
        Carbon::setTestNow($time);
        
        $response = $this->actingAs($this->dataAdminUser)
            ->call('POST', route('csv-importer.store'), $attributes)->assertStatus(302);
            
        $errors = session('errors');
        
        if (!empty($errors)) {
            mydd($errors);
            var_dump($attributes);
            die();
        }
        
        $this->get($response->headers->get('Location'))
            ->assertSee('CSV data imported successfully!');

        $importedPieceParts = NotificationPiecePart::whereIn('notification_id', ['000350434741', '000350432709'])->get();
        
        $this->assertEquals(5, $importedPieceParts->count());
    }
}
