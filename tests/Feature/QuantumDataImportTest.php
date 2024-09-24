<?php

namespace Tests\Feature;

use Carbon\Carbon;
use Tests\TestCase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Artisan;
use App\Notification;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\NotificationPiecePart;

class QuantumDataImportTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */
    public function testQuantumDataImport()
    {
        Artisan::call('spec2kapp:import_miami_quantum_data');
        
        $this->assertEquals('', Artisan::output());
    }
}
