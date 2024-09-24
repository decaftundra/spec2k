<?php

namespace Tests\Feature;

use App\Notification;
use App\NotificationPiecePart;
use App\SAPAccessToken;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Laravel\Passport\Passport;

use Mockery;

class ApiTest extends TestCase
{
    /**
     * Send notification to api.
     *
     * @return void
     */
    public function testApiCreateNotifications()
    {
        // This notification should end up with a different repair station name.
        
        $data = [
            "id" => "000350116330",
            "plant_code" => "3101",
            "rcsMPN" => "271-126-030-046",
            "rcsSER" => "AD75394",
            "hdrROC" => "79318",
            "hdrRON" => "MCS North Hollywood",
            "rcsMRD" => "2013-10-11",
        ];
        
        $this->withoutMiddleware(\Laravel\Passport\Http\Middleware\CheckClientCredentials::class);
        
        $this->ajaxPost(route('api.create-notification'), $data)->assertStatus(201);
        
        $url = route('notifications.index') . '?roc=All&search=' . (string) $data['id'];
        
        // Search in frontend.
        $this->actingAs($this->dataAdminUser)
            ->call('GET', $url)
            ->assertStatus(200)
            ->assertSee('Displaying 1 to 1 of 1 notifications.')
            ->assertSee($data['rcsSER'])
            ->assertSee($data['hdrROC'])
            ->assertSee($data['rcsMPN']);
            
        $this->assertDatabaseHas('notifications', [
            "id" => "000350116330",
            "plant_code" => "3101",
            "rcsMPN" => "271-126-030-046",
            "rcsSER" => "AD75394",
            "hdrROC" => "79318",
            "hdrRON" => "North Hollywood"
        ]);
        
        // This notification should end up with a different repair station name and the default 'ZZZZZ' cage code.
        
        $data = [
            "id" => "000350116331",
            "plant_code" => "3325",
            "rcsMPN" => "271-126-030-046",
            "rcsSER" => "AD75394",
            "hdrROC" => "12345",
            "hdrRON" => "MCS San Diego",
            "rcsMRD" => "2013-10-11",
        ];
        
        $this->withoutMiddleware(\Laravel\Passport\Http\Middleware\CheckClientCredentials::class);
        
        $this->ajaxPost(route('api.create-notification'), $data)->assertStatus(201);
        
        $url = route('notifications.index') . '?roc=All&search=' . (string) $data['id'];
        
        // Search in frontend.
        $this->actingAs($this->dataAdminUser)
            ->call('GET', $url)
            ->assertStatus(200)
            ->assertSee('Displaying 1 to 1 of 1 notifications.')
            ->assertSee($data['rcsSER'])
            ->assertSee('ZZZZZ')
            ->assertSee($data['rcsMPN']);
            
        $this->assertDatabaseHas('notifications', [
            "id" => "000350116331",
            "plant_code" => "3325",
            "rcsMPN" => "271-126-030-046",
            "rcsSER" => "AD75394",
            "hdrROC" => "ZZZZZ",
            "hdrRON" => "San Diego"
        ]);
        
        // This notification should end up with a NULL repair station name and a NULL cage code.
        
        $data = [
            "id" => "000350116331",
            "plant_code" => "1234",
            "rcsMPN" => "271-126-030-046",
            "rcsSER" => "AD75394",
            "hdrROC" => "12345",
            "hdrRON" => "Fake Repair Station",
            "rcsMRD" => "2013-10-11",
        ];
        
        $this->withoutMiddleware(\Laravel\Passport\Http\Middleware\CheckClientCredentials::class);
        
        $this->ajaxPost(route('api.create-notification'), $data)->assertStatus(201);
        
        $url = route('notifications.index') . '?roc=All&search=' . (string) $data['id'];
        
        // Search in frontend.
        $this->actingAs($this->dataAdminUser)
            ->call('GET', $url)
            ->assertStatus(200)
            ->assertSee('Displaying 1 to 1 of 1 notifications.')
            ->assertSee($data['rcsSER'])
            ->assertSee($data['rcsMPN']);
            
        $this->assertDatabaseHas('notifications', [
            "id" => "000350116331",
            "plant_code" => "1234",
            "rcsMPN" => "271-126-030-046",
            "rcsSER" => "AD75394",
            "hdrROC" => NULL,
            "hdrRON" => NULL
        ]);
    }
    
    /**
     * Send notification update to api.
     *
     * @return void
     */
    public function testApiUpdateNotifications()
    {
        $randomNotification = Notification::inRandomOrder()->first();
        
        $data = [
            "id" => $randomNotification->id,
            "plant_code" => "2200",
            "rcsMPN" => "271-126-030-046",
            "rcsSER" => "AD75394",
            "hdrROC" => "S3960",
            "hdrRON" => "Meggitt SA",
            "rcsMRD" => "2013-10-11",
        ];
        
        $this->withoutMiddleware(\Laravel\Passport\Http\Middleware\CheckClientCredentials::class);
        
        $this->ajaxPost(route('api.create-notification'), $data)->assertStatus(201);
        
        $this->assertDatabaseHas('notifications', $data);
        
        $url = route('notifications.index') . '?roc=All&search=' . (string) $data['id'];
        
        // Search in frontend.
        $this->actingAs($this->dataAdminUser)
            ->call('GET', $url)
            ->assertStatus(200)
            ->assertSee('Displaying 1 to 1 of 1 notifications.')
            ->assertSee($data['rcsSER'])
            ->assertSee($data['hdrROC'])
            ->assertSee($data['rcsMPN']);
            
        $this->assertDatabaseHas('notifications', [
            "id" => $randomNotification->id,
            "plant_code" => "2200",
            "rcsMPN" => "271-126-030-046",
            "rcsSER" => "AD75394",
            "hdrROC" => "S3960",
            "hdrRON" => "Meggitt SA"
        ]);
    }
    
    /**
     * Test that the correct notification data is saved from the middleware api.
     *
     * @return void
     */
    public function testFetchNotificationFromSAPApi()
    {
        // Remove from Cache.
        Cache::forget('fetched_notification_data.000300002685');
        
        // First create the notification with minimal data.
        $notification = factory(Notification::class)->create([
            "id" => "000300002685",
            "plant_code" => "3101",
            "rcsSFI" => "000300002685"
        ]);
        
        $this->actingAs($this->dataAdminUser)
            ->get(route('header.edit', '000300002685'))
            ->assertStatus(200)
            ->assertSee('Notification data updated from SAP middleware.');
        
        $this->assertDatabaseHas('notifications', [
            'id' => '000300002685',
            'plant_code' => 3101,
            'hdrROC' => '79318',
            'hdrRON' => 'North Hollywood',
            'rcsSFI' => '000300002685',
            //'rcsMRD' => '2014-11-13', leave this out because date format changes when saved to DB.
            'rcsMFR' => '79318',
            'rcsMPN' => '329695-5',
            'rcsSER' => '0019'
        ]);
        
        $piecePart1 = [
            'wpsSFI' => '000300002685',
            'wpsPPI' => '49000099570001',
            'wpsMPN' => 'MCSCOM01-3100',
            'wpsPDT' => 'TEST MATERIAL FOR MCS',
            'rpsMPN' => 'MCSCOM01-3100',
        ];
        
        $piecePart3 = [
            "wpsSFI" => '000300002685',
            "wpsPPI" => '49000099570003',
            "wpsMPN" => '329695-4',
            //"wpsPDT" => 'VALVEÂ°VALVE',
            "nhsSER" => '4',
            "rpsMPN" => '329695-4',
        ];
        
        $piecePart4 = [
            "wpsSFI" => '000300002685',
            "wpsPPI" => '49000099590001',
            "wpsMPN" => 'MCSCOM01-3100-2',
            "wpsFDE" => "\nItem 0020 long text has to have more text than 40 characters to becomelong text.",
            "wpsPDT" => 'TEST MATERIAL FOR MCS BATCH',
            "rpsMPN" => 'MCSCOM01-3100-2',
        ];
        
        $piecePart5 = [
            "wpsSFI" => '000300002685',
            "wpsPPI" => '49000099570002',
            "wpsMPN" => 'MCSCOM01-3100-3',
            "wpsPDT" => 'TEST MATERIAL FOR MCS;YCOM,',
            "rpsMPN" => 'MCSCOM01-3100-3',
        ];
        
        $this->assertDatabaseHas('notification_piece_parts', $piecePart1);
        $this->assertDatabaseHas('notification_piece_parts', $piecePart3);
        $this->assertDatabaseHas('notification_piece_parts', $piecePart4);
        $this->assertDatabaseHas('notification_piece_parts', $piecePart5);
        
        //mydd(json_encode(NotificationPiecePart::find('49000099590001')));
    }
    
    /**
     * Test that the middleware re-fetches a valid token if the one stored does not authenticate.
     *
     * @return void
     */
    public function testInvalidToken()
    {
        // Remove from Cache.
        Cache::forget('fetched_notification_data.000300002685');
        
        // First create the notification with minimal data.
        $notification = factory(Notification::class)->create([
            "id" => "000300002685",
            "plant_code" => "3101",
            "rcsSFI" => "000300002685"
        ]);
        
        // Purge current tokens.
        $oldTokens = SAPAccessToken::all();
        
        if ($oldTokens && count($oldTokens)) {
            foreach ($oldTokens as $oldToken) {
                $oldToken->delete();
            }
        }
        
        // Create new invalid token.
        $invalidToken = factory(SAPAccessToken::class)->create();
        
        $this->actingAs($this->dataAdminUser)
            ->get(route('header.edit', '000300002685'))
            ->assertStatus(200)
            ->assertSee('Notification data updated from SAP middleware.');
        
        $this->assertDatabaseHas('notifications', [
            'id' => '000300002685',
            'plant_code' => 3101,
            'hdrROC' => '79318',
            'hdrRON' => 'North Hollywood',
            'rcsSFI' => '000300002685',
            //'rcsMRD' => '2014-11-13', leave this out because date format changes when saved to DB.
            'rcsMFR' => '79318',
            'rcsMPN' => '329695-5',
            'rcsSER' => '0019'
        ]);
        
        $piecePart1 = [
            'wpsSFI' => '000300002685',
            'wpsPPI' => '49000099570001',
            'wpsMPN' => 'MCSCOM01-3100',
            'wpsPDT' => 'TEST MATERIAL FOR MCS',
            'rpsMPN' => 'MCSCOM01-3100',
        ];
        
        $piecePart3 = [
            "wpsSFI" => '000300002685',
            "wpsPPI" => '49000099570003',
            "wpsMPN" => '329695-4',
            //"wpsPDT" => 'VALVEÂ°VALVE',
            "nhsSER" => '4',
            "rpsMPN" => '329695-4',
        ];
        
        $piecePart4 = [
            "wpsSFI" => '000300002685',
            "wpsPPI" => '49000099590001',
            "wpsMPN" => 'MCSCOM01-3100-2',
            "wpsFDE" => "\nItem 0020 long text has to have more text than 40 characters to becomelong text.",
            "wpsPDT" => 'TEST MATERIAL FOR MCS BATCH',
            "rpsMPN" => 'MCSCOM01-3100-2',
        ];
        
        $piecePart5 = [
            "wpsSFI" => '000300002685',
            "wpsPPI" => '49000099570002',
            "wpsMPN" => 'MCSCOM01-3100-3',
            "wpsPDT" => 'TEST MATERIAL FOR MCS;YCOM,',
            "rpsMPN" => 'MCSCOM01-3100-3',
        ];
        
        $this->assertDatabaseHas('notification_piece_parts', $piecePart1);
        $this->assertDatabaseHas('notification_piece_parts', $piecePart3);
        $this->assertDatabaseHas('notification_piece_parts', $piecePart4);
        $this->assertDatabaseHas('notification_piece_parts', $piecePart5);
    }
    
    /**
     * Test that the correct notification data is saved from the middleware api. This notification has one single piece part.
     *
     * @return void
     */
    public function testFetchNotificationWithSinglePiecePartFromSAPApi()
    {
        // Remove from Cache.
        Cache::forget('fetched_notification_data.000300000977');
        
        /*
        
        {"notification":{"id":"000300000977","plant_code":"3200","hdrROC":"0B9R9","hdrRON":"Meggitt (New Hampshire)","rcsSFI":"000300000977","rcsMRD":"2011-09-29","rcsMFR":"","rcsMPN":"76-174-8-S","rcsSER":"100-987","rcsREM":"","sasREM":"\\nService (Civil Aerospace)\\nTesting long text some more\\nAnd more","susSHD":"0000-00-00","susMFR":"","susMPN":"","susSER":"","planner_group":"","status":"in_progress","subcontracted_at":"0000-00-00","scrapped_at":"0000-00-00","shipped_at":"0000-00-00","piece_parts":{"item":{"wpsSFI":"000300000977","wpsPPI":"49000011950001","wpsMPN":"76-174-1102-8","wpsFDE":"","wpsPDT":"TLN COMMISSIONS TESTING DO NOT CHANGE","nhsMFR":"","nhsMPN":"76-174-8-S","nhsSER":"","nhsPNR":"","rpsMPN":"76-174-1102-8","reversal_id":""}}}}
        
        */
        
        // First create the notification with minimal data.
        $notification = factory(Notification::class)->create([
            "id" => "000300000977",
            "plant_code" => "3200",
            "rcsSFI" => "000300000977",
            "rcsMFR" => ''
        ]);
        
        $this->actingAs($this->dataAdminUser)
            ->get(route('header.edit', '000300000977'))
            ->assertStatus(200)
            ->assertSee('Notification data updated from SAP middleware.');
        
        $this->assertDatabaseHas('notifications', [
            'id' => '000300000977',
            'plant_code' => 3200,
            'hdrROC' => NULL,
            'hdrRON' => NULL,
            'rcsSFI' => '000300000977',
            //'rcsMRD' => '2014-11-13', leave this out because date format changes when saved to DB.
            'rcsMFR' => '',
            'rcsMPN' => '76-174-8-S',
            'rcsSER' => '100-987'
        ]);
        
        $piecePart1 = [
            'wpsSFI' => '000300000977',
            'wpsPPI' => '49000011950001',
            'wpsMPN' => '76-174-1102-8',
            'wpsPDT' => 'TLN COMMISSIONS TESTING DO NOT CHANGE',
            'rpsMPN' => '76-174-1102-8',
        ];
        
        $this->assertDatabaseHas('notification_piece_parts', $piecePart1);
    }
}
