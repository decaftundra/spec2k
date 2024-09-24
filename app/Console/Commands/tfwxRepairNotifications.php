<?php

namespace App\Console\Commands;

use App\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Console\Command;

use App\Notification;
use App\NotificationPiecePart;
use App\SAPOAuth2Client;
use App\SAPAccessToken;


class tfwxRepairNotifications extends Command
{
    // LJMApr24 this is in case I cant do this via a normal PHP script.
    protected $hidden = true;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'spec2kapp:tfwx_repair_notifications';
    // note to call on lindsays localhost:cd to the root of the site and simply: php artisan spec2kapp:tfwx_repair_notifications
    // note to call on WEDGE:



    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'LJM MGTSUP-864';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {

        // need to do the sql stuff raw in here.


        $szBrokenNotificationEmail = 'MGTSUP-864 beginning Notification Rescue: ----';
        Log::info('MGTSUP-864 beginning Notification Rescue: '); // confirmed works on DEV


        $tfwxBrokenNotifications = DB::select(" SELECT * FROM shop_findings where status = 'in_progress' and ((scrapped_at is not null) or (shipped_at is not null));");
        foreach ($tfwxBrokenNotifications as $tfwxBrokenNotification)
        {
            $szBrokenNotificationEmail = $szBrokenNotificationEmail.' --- Fixing '.$tfwxBrokenNotification->id;
            Log::info('Fixing '.$tfwxBrokenNotification->id);

















            // LJM try the actual notification get



            //route('header.edit', $tfwxBrokenNotification->id)->middleware('fetch-notification'); // ->assertStatus(200)->assertSee('Notification data updated from SAP middleware.');
//            visit(route('header.edit', $tfwxBrokenNotification->id))
//            Route::get('/dataset/edit/'.$tfwxBrokenNotification->id.'/header')->name('header.edit');


            try {

                $client = new SAPOAuth2Client;

                $notificationEndpoint = env('API_NOTIFICATION_ENDPOINT');

                $response = $client->apiGetRequest($notificationEndpoint, ['notification' => $tfwxBrokenNotification->id]);

                // If unauthenticated response, purge and reset token, then retry.
                if ($response->status == 401) {
//                    $szBrokenNotificationEmail = $szBrokenNotificationEmail.'Unauthenticated API Middleware response for Notification: ' . $tfwxBrokenNotification->id. [$response->content];
                    Log::info('Unauthenticated API Middleware response for Notification: ' . $tfwxBrokenNotification->id, [$response->content]);



                    $token = SAPAccessToken::first();

                    $token ? Log::info('Token Data:', [$token]) : Log::info('No Tokens Found!');

                    Log::info('Resetting Token');
                    $szBrokenNotificationEmail = $szBrokenNotificationEmail.'Resetting Token';




                    $client->resetAccessToken();

                    // Create new response.
                    $response = $client->apiGetRequest($notificationEndpoint, ['notification' => $tfwxBrokenNotification->id]);

                    $szBrokenNotificationEmail = $szBrokenNotificationEmail . ' -- Error 401     - ';

                }

                if (($response->status == 200) && !empty($response->content->notification->id)) {

                    Log::info('Successful API Middleware data retrieval for Notification: ' . $tfwxBrokenNotification->id, [$response->content]);
//                    $szBrokenNotificationEmail = $szBrokenNotificationEmail.'Successful API Middleware data retrieval for Notification: ' . $tfwxBrokenNotification->id. [$response->content];

                    // Store Notification and Notification Piece Part data in database...
                    Notification::updateFromMiddleware($response->content->notification);

                    if (!empty($response->content->notification->piece_parts->item)) {

                        if (!is_array($response->content->notification->piece_parts->item)) {
                            NotificationPiecePart::updateOrCreateFromMiddleware([$response->content->notification->piece_parts->item]);
                        } else {
                            NotificationPiecePart::updateOrCreateFromMiddleware($response->content->notification->piece_parts->item);
                        }
                    }

                    $this->info('Successful API Middleware data retrieval & update for Notification: ' . $tfwxBrokenNotification->id);

                    $szBrokenNotificationEmail = $szBrokenNotificationEmail . ' -- Fixed     - ';


                } else if ($response->status != 404) {
                    Log::info('Error fetching notification data from SAP middleware. Notification ID: ' . $tfwxBrokenNotification->id);
//                    $szBrokenNotificationEmail = $szBrokenNotificationEmail.'Error fetching notification data from SAP middleware. Notification ID: ' . $tfwxBrokenNotification->id;

//                    $this->error('Error fetching notification data from SAP middleware. Notification ID: ' . $tfwxBrokenNotification->id);

                    $token = SAPAccessToken::first();

                    if ($token) {
                        Log::info('Token Data:', [$token]);
                        $szBrokenNotificationEmail = $szBrokenNotificationEmail.'Token Data:'. [$token];
                    } else {
                        Log::info('No Tokens Found!');
                        $szBrokenNotificationEmail = $szBrokenNotificationEmail.'No Tokens Found!';
                    }

                    $client->resetAccessToken();

                    $message = 'Error fetching notification data from SAP middleware. Notification ID: ' . $tfwxBrokenNotification->id . "\n";
//                    $szBrokenNotificationEmail = $szBrokenNotificationEmail.'Error fetching notification data from SAP middleware. Notification ID: ' . $tfwxBrokenNotification->id . "\n";

                    $message .= json_encode($response);


                    $szBrokenNotificationEmail = $szBrokenNotificationEmail . ' -- Error 404     - ';

                    throw new MiddlewareException($message);
                }

            }
            catch (MiddlewareException $e) {
                Log::error($e->getMessage());

                $szBrokenNotificationEmail = $szBrokenNotificationEmail.'  -  Exception  -  '.$e->getMessage()."\n";
            }

            // Pause.
            sleep(1);
























            //            $szBrokenNotificationEmail = $szBrokenNotificationEmail.'--Fixed '.$tfwxBrokenNotification->id;
            //            Log::info('Fixed '.$tfwxBrokenNotification->id);

        }

        $szBrokenNotificationEmail = $szBrokenNotificationEmail.'  -- MGTSUP-864 ending Notification Rescue: ---- ';
        Log::info('MGTSUP-864 ending Notification Rescue.');


        Mail::raw($szBrokenNotificationEmail, function ($message)
        {
            $message->subject('MGTSUP-864 beginning Notification Rescue')
                ->from('meggittproductperformance@meggitt.com')
                ->to('lindsay@thefusionworks.com');
        }); // confirmed works on DEV

    }

}
