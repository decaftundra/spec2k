<?php

namespace App\Http\Middleware;

use App\Alert;
use App\Exceptions\MiddlewareException;
use App\Notification;
use App\NotificationPiecePart;
use App\SAPOAuth2Client;
use App\SAPAccessToken;
use Closure;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class FetchNotificationData
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        // if notification data has NOT been fetched in the last 10 minutes
            // fetch it from the middleware
            // save to database
            // save flag in cache
        // else
            // retrieve data from database
            
            
        // Disable this middleware in production for now
        //if (\App::environment(['local', 'testing', 'dev'])) {
                
            $notification = $request->route('notification');
    
            $id = $notification->id;
            
            if (!Cache::has('fetched_notification_data.' . $id)) {
                try {
                    
                    $client = new SAPOAuth2Client;
                    
                    $notificationEndpoint = env('API_NOTIFICATION_ENDPOINT');
            
                    $response = $client->apiGetRequest($notificationEndpoint, ['notification' => $id]);
                    
                    // If unauthenticated response, purge and reset token, then retry.
                    if ($response->status == 401) {
                        Log::info('Unauthenticated API Middleware response for Notification: ' . $id, [$response->content]);
                        
                        $token = SAPAccessToken::first();
                        
                        if ($token) {
                            Log::info('Token Data:', [$token]);
                        } else {
                            Log::info('No Tokens Found!');
                        }
                        
                        Log::info('Resetting Token');
                        
                        $client->resetAccessToken();
                        
                        // Create new response.
                        $response = $client->apiGetRequest($notificationEndpoint, ['notification' => $id]);
                    }
                    
                    if (($response->status == 200) && !empty($response->content->notification->id)) {
                        
                        Log::info('Successful API Middleware data retrieval for Notification: ' . $id, [$response->content]);
                        
                        // Store Notification and Notification Piece Part data in database...
                        Notification::updateFromMiddleware($response->content->notification);
                        
                        if (!empty($response->content->notification->piece_parts->item)) {
                            
                            if (!is_array($response->content->notification->piece_parts->item)) {
                                NotificationPiecePart::updateOrCreateFromMiddleware([$response->content->notification->piece_parts->item]);
                            } else {
                                NotificationPiecePart::updateOrCreateFromMiddleware($response->content->notification->piece_parts->item);
                            }
                        }
                        
                        $request->flash(Alert::success('Notification data updated from SAP middleware.'));
                        
                    } else if ($response->status != 404) {
                        Log::info('Error fetching notification data from SAP middleware. Notification ID: ' . $id);
                        
                        $token = SAPAccessToken::first();
                        
                        if ($token) {
                            Log::info('Token Data:', [$token]);
                        } else {
                            Log::info('No Tokens Found!');
                        }
                        
                        $client->resetAccessToken();
                        
                        $message = 'Error fetching notification data from SAP middleware. Notification ID: ' . $id . "\n";
                        $message .= json_encode($response);
                        throw new MiddlewareException($message);
                    }
                
                    Cache::put('fetched_notification_data.' . $id, true, 600);
                    
                } catch (MiddlewareException $e) {
                    report($e);
                }
            }
        //}
        
        return $next($request);
    }
}