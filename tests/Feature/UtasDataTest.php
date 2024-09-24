<?php

namespace Tests\Feature;

use App\UtasCode;
use App\UtasPartNumber;
use App\UtasReasonCode;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class UtasDataTest extends TestCase
{
    /**
     * Test import Utas Codes csv.
     *
     * @return void
     */
    public function testImportUtasCodes()
    {
        $this->actingAs($this->dataAdminUser)
            ->get(route('utas-data.utas-codes'))
            ->assertSee('Import Collins/Utas Codes CSV Data')
            ->assertStatus(200);
            
        // Test using real file.
        $attributes = [
            'file' => new UploadedFile(storage_path('app/utas-data/' . 'utas-codes.csv'), 'utas-codes.csv', 'text/csv', NULL, true)
        ];
        
        // Mock the time.
        $time = Carbon::now();
        Carbon::setTestNow($time);
        
        $response = $this->actingAs($this->dataAdminUser)
            ->call('POST', route('utas-data.import-utas-codes'), $attributes)->assertStatus(302);
            
        $errors = session('errors');
        
        if (!empty($errors)) {
            mydd($errors);
            var_dump($attributes);
            die();
        }
        
        $this->get($response->headers->get('Location'))
            ->assertSee('CSV data imported successfully!');
            
        $importedUtasCodes = UtasCode::where('DESCR', 'THIS IS TEST DATA')->get();
        
        $this->assertGreaterThan(0, $importedUtasCodes->count());
    }
    
    /**
     * Test import Utas Part Numbers csv.
     *
     * @return void
     */
    public function testImportUtasPartNumbers()
    {
        $this->actingAs($this->dataAdminUser)
            ->get(route('utas-data.utas-part-numbers'))
            ->assertSee('Import Collins/Utas Part Numbers CSV Data')
            ->assertStatus(200);
            
        // Test using real file.
        $attributes = [
            'file' => new UploadedFile(storage_path('app/utas-data/' . 'utas-part-numbers.csv'), 'utas-part-numbers.csv', 'text/csv', NULL, true)
        ];
        
        // Mock the time.
        $time = Carbon::now();
        Carbon::setTestNow($time);
        
        $response = $this->actingAs($this->dataAdminUser)
            ->call('POST', route('utas-data.import-utas-part-numbers'), $attributes)->assertStatus(302);
            
        $errors = session('errors');
        
        if (!empty($errors)) {
            mydd($errors);
            var_dump($attributes);
            die();
        }
        
        $this->get($response->headers->get('Location'))
            ->assertSee('CSV data imported successfully!');
            
        $importedUtasPartNumbers = UtasPartNumber::where('description', 'This is test data')->get();
        
        $this->assertGreaterThan(0, $importedUtasPartNumbers->count());
    }
    
    /**
     * Test import Utas Reason Codes csv.
     *
     * @return void
     */
    public function testImportUtasReasonCodes()
    {
        $this->actingAs($this->dataAdminUser)
            ->get(route('utas-data.utas-reason-codes'))
            ->assertSee('Import Collins/Utas Reason Codes CSV Data')
            ->assertStatus(200);
            
        // Test using real file.
        $attributes = [
            'file' => new UploadedFile(storage_path('app/utas-data/' . 'utas-reason-codes.csv'), 'utas-reason-codes.csv', 'text/csv', NULL, true)
        ];
        
        // Mock the time.
        $time = Carbon::now();
        Carbon::setTestNow($time);
        
        $response = $this->actingAs($this->dataAdminUser)
            ->call('POST', route('utas-data.import-utas-reason-codes'), $attributes)->assertStatus(302);
            
        $errors = session('errors');
        
        if (!empty($errors)) {
            mydd($errors);
            var_dump($attributes);
            die();
        }
        
        $this->get($response->headers->get('Location'))
            ->assertSee('CSV data imported successfully!');
            
        $importedUtasReasonCodes = UtasReasonCode::where('REASON', 'THIS IS TEST DATA')->get();
        
        $this->assertGreaterThan(0, $importedUtasReasonCodes->count());
    }
    
    /**
     * Test the Utas Codes csv export.
     *
     * @return void
     */
    public function testExportUtasCodes()
    {
        $response = $this->actingAs($this->dataAdminUser)
            ->get(route('utas-data.export-utas-codes'))
            ->assertStatus(200);
            
        $this->assertTrue($response->headers->get('content-disposition') == 'attachment; filename=' . 'utas-codes.csv');
    }
    
    /**
     * Test the Utas Part Numbers csv export.
     *
     * @return void
     */
    public function testExportUtasPartNumbers()
    {
        $response = $this->actingAs($this->dataAdminUser)
            ->get(route('utas-data.export-utas-part-numbers'))
            ->assertStatus(200);
            
        $this->assertTrue($response->headers->get('content-disposition') == 'attachment; filename=' . 'utas-part-numbers.csv');
    }
    
    /**
     * Test the Utas Reason Codes csv export.
     *
     * @return void
     */
    public function testExportUtasReasonCodes()
    {
        $response = $this->actingAs($this->dataAdminUser)
            ->get(route('utas-data.export-utas-reason-codes'))
            ->assertStatus(200);
            
        $this->assertTrue($response->headers->get('content-disposition') == 'attachment; filename=' . 'utas-reason-codes.csv');
    }
}