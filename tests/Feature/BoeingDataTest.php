<?php

namespace Tests\Feature;

use Carbon\Carbon;
use Tests\TestCase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class BoeingDataTest extends TestCase
{
    protected $csvFile = 'Aircraft-Operator-ICAO-Lists-28-as-of-2006-04-2019.csv';
    
    protected $wrongCsvFile = 'wrong-file.csv';
    
    /**
     * Test only data admins can edit and update boeing data.
     *
     * @return void
     */
    public function testOnlyDataAdminCanEditBoeingData()
    {
        $this->markTestSkipped('Boeing Data import is no longer used.');
        
        $this->actingAs($this->user)
            ->get(route('boeing.edit'))
            ->assertStatus(403);
        
        $this->actingAs($this->adminUser)
            ->get(route('boeing.edit'))
            ->assertStatus(403);
            
        $this->actingAs($this->siteAdminUser)
            ->get(route('boeing.edit'))
            ->assertStatus(403);
            
        // Test using real file.
        $attributes = [
            'file' => new UploadedFile(storage_path('app/boeing-test-csv/' . $this->csvFile), $this->csvFile, 'text/csv', NULL, true)
        ];
        
        // Mock the time.
        $time = Carbon::now();
        Carbon::setTestNow($time);
        
        $response = $this->actingAs($this->user)
            ->call('PUT', route('boeing.update'), $attributes)
            ->assertStatus(403);
        
        $response = $this->actingAs($this->adminUser)
            ->call('PUT', route('boeing.update'), $attributes)
            ->assertStatus(403);
            
        $response = $this->actingAs($this->siteAdminUser)
            ->call('PUT', route('boeing.update'), $attributes)
            ->assertStatus(403);
    }
    
    /**
     * A basic test example.
     *
     * @return void
     */
    public function testUpdateBoeingData()
    {
        $this->markTestSkipped('Boeing Data import is no longer used.');
        
        $this->actingAs($this->dataAdminUser)
            ->get(route('boeing.edit'))
            ->assertStatus(200);
        
        // Test using real file.
        $attributes = [
            'file' => new UploadedFile(storage_path('app/boeing-test-csv/' . $this->csvFile), $this->csvFile, 'text/csv', NULL, true)
        ];
        
        // Mock the time.
        $time = Carbon::now();
        Carbon::setTestNow($time);
        
        $response = $this->actingAs($this->dataAdminUser)
            ->call('PUT', route('boeing.update'), $attributes)->assertStatus(302);
            
        $errors = session('errors');
        
        if (!empty($errors)) {
            mydd($errors);
            var_dump($attributes);
            die();
        }
        
        $this->get($response->headers->get('Location'))
            ->assertSee('Boeing data updated successfully!');
        
        // Assert file uploaded.
        Storage::disk('local')->assertExists('boeing-csv-files/boeing-data-' . $time->timestamp . '.csv');
        
        // Assert database dumped.
        Storage::disk('local')->assertExists('aircraft-detail-dumps/aircraft-details-' . $time->timestamp . '.sql');
        
        // Assert new database records exist.
        $this->assertDatabaseHas('aircraft_details', ['created_at' => $time->format('Y-m-d H:i:s')]);
        
        // Cleanup test files.
        Storage::disk('local')->delete('boeing-csv-files/boeing-data-' . $time->timestamp . '.csv');
        Storage::disk('local')->delete('aircraft-detail-dumps/aircraft-details-' . $time->timestamp . '.sql');
        
        Carbon::setTestNow();
    }
    
    /**
     * Test that an empty csv file causes the expected error.
     *
     * @return void
     */
    public function testEmptyCsvThrowsError()
    {
        $this->markTestSkipped('Boeing Data import is no longer used.');
        
        // Test using real file.
        $attributes = [
            'file' => UploadedFile::fake()->create('empty-file-test.csv', 0)
        ];
        
        $response = $this->actingAs($this->dataAdminUser)
            ->call('PUT', route('boeing.update'), $attributes)
            ->assertStatus(302);
            
        $this->get($response->headers->get('Location'))
            ->assertSee('The CSV file did not contain the expected data columns.');
    }
    
    /**
     * Test that a csv file with incorrect data causes the expected error.
     *
     * @return
     */
    public function testWrongCsvThrowsError()
    {
        $this->markTestSkipped('Boeing Data import is no longer used.');
        
        $this->actingAs($this->dataAdminUser)
            ->get(route('boeing.edit'))
            ->assertStatus(200);
        
        // Test using real file.
        $attributes = [
            'file' => new UploadedFile(storage_path('app/boeing-test-csv/' . $this->wrongCsvFile), $this->wrongCsvFile, 'text/csv', NULL, true)
        ];
        
        $response = $this->actingAs($this->dataAdminUser)
            ->call('PUT', route('boeing.update'), $attributes)->assertStatus(302);
            
        $this->get($response->headers->get('Location'))
            ->assertSee('The CSV file did not contain the expected data columns.');
    }
}
