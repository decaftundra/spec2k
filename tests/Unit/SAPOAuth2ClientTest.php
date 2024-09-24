<?php

namespace Tests\Unit;

use App\Notification;
use App\NotificationPiecePart;
use App\SAPAccessToken;
use App\SAPOAuth2Client;
use Carbon\Carbon;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;

class SAPOAuth2ClientTest extends TestCase
{
    /**
     * Test that an invalid token returns a 401 response status.
     *
     * @return void
     */
    public function testInvalidOAuthToken()
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
        
        $client = new SAPOAuth2Client;
        
        $notificationEndpoint = env('API_NOTIFICATION_ENDPOINT');

        $response = $client->apiGetRequest($notificationEndpoint, ['notification' => '000300002685']);
        
        $this->assertEquals(401, $response->status);
    }

}