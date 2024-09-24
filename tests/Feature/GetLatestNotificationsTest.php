<?php

namespace Tests\Feature;

use App\AircraftDetail;
use App\Notification;
use App\NotificationPiecePart;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class GetLatestNotificationsTest extends TestCase
{
    /**
     * Assert that no errors are output from Artisan command.
     *
     * @return void
     */
    public function testGetLatestNotifications()
    {
        Artisan::call('spec2kapp:update_notifications_and_piece_parts');
        
        $this->assertEquals('', Artisan::output());
        
        // Test SUBC status.
        $subcontracteds = Notification::whereIn('id', ['000350368529', '000350368583'])->get();
        
        foreach ($subcontracteds as $subcontracted) {
            $this->assertEquals($subcontracted->status, 'subcontracted');
        }
        
        // Test planner group.
        $plannerGroups = Notification::whereIn('id', ['000350368529', '000350368537', '000350368583'])->get();
        
        foreach ($plannerGroups as $plannerGroup) {
            $this->assertNotNull($plannerGroup->planner_group, true);
        }
        
        /*
        Test aircraft registration numbers.
        NOTE: The aircraft must be in the aircraft_details database table
        for this test to pass when doing full test suite.
        */
        $registrationNumbers = Notification::whereIn('id', ['000350368534'])->get();
        
        foreach ($registrationNumbers as $registrationNumber) {
            $this->assertNotNull($registrationNumber->rcsREM);
            $this->assertNotNull($registrationNumber->aidREG);
            $this->assertNotNull($registrationNumber->aidAIN);
            $this->assertNotNull($registrationNumber->aidAMC);
            $this->assertNotNull($registrationNumber->aidASE);
            $this->assertNotNull($registrationNumber->aidMFN);
            $this->assertNotNull($registrationNumber->aidMFR);
            
            // Also check Engine Info.
            $this->assertNotNull($registrationNumber->eidAET);
            $this->assertNotNull($registrationNumber->eidAEM);
            $this->assertNotNull($registrationNumber->eidEPC);
            $this->assertNotNull($registrationNumber->eidMFR);
        }
    }
    
    /**
     * Test that the last instance of the aircraft registration number in the notification text is recorded.
     *
     * @return void
     */
    public function testAircraftRegistrationImport()
    {
        Artisan::call('spec2kapp:update_notifications_and_piece_parts');
        
        $this->assertEquals('', Artisan::output());
        
        $aircraft = AircraftDetail::where('aircraft_fully_qualified_registration_no', 'LX-JET')->get();
        
        $this->assertDatabaseHas('notifications', [
            'id' => '000350368564',
            'aidREG' => 'LX-JET',
            'aidAIN' => substr($aircraft[0]->aircraft_identification_no, 0, 10),
            'aidAMC' => substr($aircraft[0]->aircraft_model_identifier, 0, 20),
            'aidASE' => substr($aircraft[0]->aircraft_series_identifier, 0, 10),
            'aidMFN' => substr($aircraft[0]->manufacturer_name, 0, 55),
            'aidMFR' => substr($aircraft[0]->manufacturer_code, 0, 5)
        ]);
        
        // Append blank aircraft reg to notifications text
        
        $notificationTextDataFile = 'SAP_AZRBD_001_NOTIFICATION_TEXTS.txt';
        
        $filePath = storage_path('app' . DIRECTORY_SEPARATOR . 'sap-data-testing' . DIRECTORY_SEPARATOR . $notificationTextDataFile);
        
        $contents = file_get_contents($filePath);
        
        try {
            $myfile = fopen($filePath, "a") or die("Unable to open file!");
            $txt1 = "000350368564	*	Testing the aircraft reg no. in S2K webapp when multiple numbers are entered plus a blank";
            $txt2 = "000350368564	*	@@@@";
            fwrite($myfile, "\n". $txt1);
            fwrite($myfile, "\n". $txt2);
            fclose($myfile);
            
            // 000350368564	*	Testing the aircraft reg no. in S2K webapp when multiple numbers are entered plus a blank
            // 000350368564	*	@@@@
            
            // Re-import
            Artisan::call('spec2kapp:update_notifications_and_piece_parts');
            
            // Assert that values are blank
            $this->assertEquals('', Artisan::output());
            $this->assertDatabaseHas('notifications', [
                'id' => '000350368564',
                'aidREG' => NULL,
                'aidAIN' => NULL,
                'aidAMC' => NULL,
                'aidASE' => NULL,
                'aidMFN' => NULL,
                'aidMFR' => NULL,
                'eidAET' => NULL,
                'eidAEM' => NULL,
                'eidEPC' => NULL,
                'eidMFR' => NULL
            ]);
        } finally {
            // Restore file back to previous state.
            file_put_contents($filePath, $contents);
        }
    }
    
    /**
     * Test notifications and piece parts are present in the database, includes some with double-quotes in data.
     *
     * @return void
     */
    public function testNotificationsAndPiecePartsAreInDatabase()
    {
        Artisan::call('spec2kapp:update_notifications_and_piece_parts');
        
        $this->assertEquals('', Artisan::output());
        
        $this->assertDatabaseHas('notifications', ['id' => '000350368583']);
        $this->assertDatabaseHas('notifications', ['id' => '000350368537']);
        $this->assertDatabaseHas('notifications', ['id' => '000350368529']);
        
        $this->assertDatabaseHas('notification_piece_parts', ['id' => '49602663660001', 'notification_id' => '000350383543']);
        $this->assertDatabaseHas('notification_piece_parts', ['id' => '49602991770006', 'notification_id' => '000350384085']);
        $this->assertDatabaseHas('notification_piece_parts', ['id' => '49602796990005', 'notification_id' => '000350384762']);
        $this->assertDatabaseHas('notification_piece_parts', ['id' => '49601258820006', 'notification_id' => '000350384934']);
    }
    
    /**
     * Test notification texts are in the database, includes some with double-quotes in data.
     *
     * @return void
     */
    public function testNotificationTextsAreInDatabase()
    {
        Artisan::call('spec2kapp:update_notifications_and_piece_parts');
        
        $this->assertEquals('', Artisan::output());
        
        // Test single-line text.
        $this->assertDatabaseHas('notifications', ['id' => '000300003811', 'rcsREM' => "test ZVC1 - service notification long text"]);
        
        // Test multi-line text.
        $text = "reason for return: OVERHEATING\nADDITIONAL DETAILS: TSN: 1200   CSN: 1350\nSCOPE OF WORK TO BE CARRIED OUT: OVERHAUL\nQUOTATION REQUIRED BY 31-JUNE-09";
        
        //$notification = Notification::find('000300000603');
        
        //mydd(json_encode($notification, JSON_PRETTY_PRINT));
        
        $attributes = ['id' => '000300000603', 'rcsREM' => $text];
        
        $this->assertDatabaseHas('notifications', $attributes);
        
        // Test with double-quote.
        $this->assertDatabaseHas('notifications', ['id' => '000300000179', 'rcsREM' => "\"kjm"]);
    }
    
    /**
     * Test service order texts are in database, includes some with double-quotes in data.
     *
     * @return void
     */
    public function testServiceOrderTextsAreInDatabase()
    {
        Artisan::call('spec2kapp:update_notifications_and_piece_parts');
        
        $this->assertEquals('', Artisan::output());
        
        // Test single-line text.
        $this->assertDatabaseHas('notifications', ['id' => '000300002714', 'sasINT' => "Text from Service Order. Should print on output"]);
        
        // Test multi-line text.
        $this->assertDatabaseHas('notifications', ['id' => '000300002685', 'sasINT' => "MCS MRO Service 329695-5\nRequires complete overhaul."]);
        
        // Test with double-quote.
        $this->assertDatabaseHas('notifications', ['id' => '000300002571', 'sasINT' => "\"REPAIR SERVICE,for Regr Test,YSER,LEIS\nEnter text May need replacement parts"]);
    }
    
    /**
     * Test the reservation (piece part) texts are in the database, includes some with double-quotes in data.
     *
     * @return void
     */
    public function testPiecePartTextsAreInTheDatabase()
    {
        Artisan::call('spec2kapp:update_notifications_and_piece_parts');
        
        $this->assertEquals('', Artisan::output());
        
        $attributes = [
            'id' => '49000101490002',
            'notification_id' => '000300002711',
            'wpsFDE' => "Long text for item 0020.  There must be greater than 40 characters in order to be long text."
        ];
        
        $this->assertDatabaseHas('notification_piece_parts', $attributes);
        
        // Text starting with a double-quote.
        $attributes = [
            'id' => '49000090120009',
            'notification_id' => '000300002316',
            'wpsFDE' => "\"This is some long text to test the replacement of this text with text from the pull-down in Spec2000."
        ];
        
        $this->assertDatabaseHas('notification_piece_parts', $attributes);
    }
    
    /**
     * Test that the planner group is correctly assigned where appropriate.
     *
     * @return void
     */
    public function testPlannerGroup()
    {
        Artisan::call('spec2kapp:update_notifications_and_piece_parts');
        
        $this->assertEquals('', Artisan::output());
        
        $this->assertDatabaseHas('notifications', ['id' => '000350368491', 'planner_group' => NULL]);
        
        $this->assertDatabaseHas('notifications', ['id' => '000350368537', 'planner_group' => 'Z03']);
    }
    
    /**
     * Test that the status is set to subcontracted where appropriate.
     *
     * @return void
     */
    public function testSubcontractedStatus()
    {
        Carbon::setTestNow(Carbon::now());
        
        Artisan::call('spec2kapp:update_notifications_and_piece_parts');
        
        $this->assertEquals('', Artisan::output());
        
        // Should have a default current subcontracted_at date.
        $this->assertDatabaseHas('notifications', ['id' => '000350368583', 'status' => 'subcontracted', 'subcontracted_at' => Carbon::getTestNow()->format('Y-m-d H:i:s')]);
        
        // Should have a set subcontracted_at date.
        $this->assertDatabaseHas('notifications', ['id' => '000350368529', 'status' => 'subcontracted', 'subcontracted_at' => '2019-03-15 ' . Carbon::getTestNow()->format('H:i:s')]);
        
        Carbon::setTestNow(); // Reset time.
    }
    
    /**
     * Test that the status is in_progress.
     *
     * @return void
     */
    public function testInProgress()
    {
        Carbon::setTestNow(Carbon::now());
        
        Artisan::call('spec2kapp:update_notifications_and_piece_parts');
        
        $this->assertEquals('', Artisan::output());
        
        $this->assertDatabaseHas('notifications', ['id' => '000350368423', 'status' => 'in_progress']);
        
        Carbon::setTestNow(); // Reset time.
    }
    
    /**
     * Test the status is complete_scrapped.
     *
     * @return void
     */
    public function testCompleteScrapped()
    {
        Carbon::setTestNow(Carbon::now());
        
        Artisan::call('spec2kapp:update_notifications_and_piece_parts');
        
        $this->assertEquals('', Artisan::output());
        
        $this->assertDatabaseHas('notifications', ['id' => '000350368461', 'status' => 'complete_scrapped', 'scrapped_at' => '2019-03-07 ' . Carbon::getTestNow()->format('H:i:s')]);
        
        Carbon::setTestNow(); // Reset time.
    }
    
    /**
     * Test the status is complete_scrapped.
     *
     * @return void
     */
    public function testCompleteShipped()
    {
        Carbon::setTestNow(Carbon::now());
        
        Artisan::call('spec2kapp:update_notifications_and_piece_parts');
        
        $this->assertEquals('', Artisan::output());
        
        $this->assertDatabaseHas('notifications', ['id' => '000350368512', 'status' => 'complete_shipped', 'shipped_at' => '2019-03-04 ' . Carbon::getTestNow()->format('H:i:s')]);
        
        Carbon::setTestNow(); // Reset time.
    }
    
    /**
     * Test that the plant code is correctly assigned where appropriate.
     *
     * @return void
     */
    public function testPlantCode()
    {
        Artisan::call('spec2kapp:update_notifications_and_piece_parts');
        
        $this->assertEquals('', Artisan::output());
        
        $this->assertDatabaseHas('notifications', ['id' => '000350368583', 'plant_code' => 2200]);
        
        $this->assertDatabaseHas('notifications', ['id' => '000350368512', 'plant_code' => 1116]);
        
        $this->assertDatabaseHas('notifications', ['id' => '000350368537', 'plant_code' => NULL]);
    }
    
    /**
     * Test plant code with no location makes cage code and location name both null.
     *
     * @return void
     */
    public function testPlantCodeWithoutLocationMakesNameAndCageCodeNull()
    {
        Artisan::call('spec2kapp:update_notifications_and_piece_parts');
        
        $this->assertEquals('', Artisan::output());
        
        $this->assertDatabaseHas('notifications', ['id' => '000350368999', 'plant_code' => 9999, 'hdrRON' => NULL, 'hdrROC' => NULL]);
    }
    
    /**
     * Test location name is correctly changed.
     *
     * @return void
     */
    public function testLocationNameCorrectlyChanged()
    {
        Artisan::call('spec2kapp:update_notifications_and_piece_parts');
        
        $this->assertEquals('', Artisan::output());
        
        $this->assertDatabaseHas('notifications', ['id' => '000350368512', 'plant_code' => 1116, 'hdrRON' => 'Birmingham', 'hdrROC' => 'U6578']);
    }
    
    /**
     * Test cage code is correctly assigned.
     *
     * @return void
     */
    public function testCageCodeCorrectlyAssigned()
    {
        Artisan::call('spec2kapp:update_notifications_and_piece_parts');
        
        $this->assertEquals('', Artisan::output());
        
        $this->assertDatabaseHas('notifications', ['id' => '000350368998', 'plant_code' => 1422, 'hdrRON' => 'S&S Coventry', 'hdrROC' => 'K1037']);
    }
    
    /**
     * Test location with multiple cage codes is correctly assigned.
     *
     * @return void
     */
    public function testLocationWithMultipleCageCodesCorrectlyAssigned()
    {
        Artisan::call('spec2kapp:update_notifications_and_piece_parts');
        
        $this->assertEquals('', Artisan::output());
        
        $this->assertDatabaseHas('notifications', ['id' => '000350368997', 'plant_code' => 3126, 'hdrRON' => 'Ventura County', 'hdrROC' => '05167']);
    }
    
    /**
     * Test location without cage code is assigned 'ZZZZZ'.
     *
     * @return void
     */
    public function testLocationWithoutCageCodeIsAssignedZZZZZ()
    {
        Artisan::call('spec2kapp:update_notifications_and_piece_parts');
        
        $this->assertEquals('', Artisan::output());
        
        $this->assertDatabaseHas('notifications', ['id' => '000350368996', 'plant_code' => 3325, 'hdrRON' => 'San Diego', 'hdrROC' => 'ZZZZZ']);
    }
}
