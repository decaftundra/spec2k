<?php

namespace App\Providers;

use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Event;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],
        'Illuminate\Auth\Events\Registered' => [
            'App\Listeners\LogRegisteredUser',
        ],
        'Illuminate\Auth\Events\Failed' => [
            'App\Listeners\LogFailedLogin',
        ],
        'Illuminate\Auth\Events\Logout' => [
            'App\Listeners\LogSuccessfulLogout',
        ],
        'Illuminate\Auth\Events\Login' => [
            'App\Listeners\LogSuccessfulLogin',
        ],
        'Illuminate\Auth\Events\PasswordReset' => [
            'App\Listeners\LogSuccessfulPasswordReset',
        ],
        'App\Events\PasswordUpdated' => [
            'App\Listeners\LogPasswordUpdate',
        ],
        'App\Events\SegmentSaving' => [
            //'App\Listeners\UpdateValidation'
        ],
        'App\Events\SegmentCreated' => [
            'App\Listeners\CreateSegmentValidation'
        ],
        'App\Events\SegmentUpdated' => [
            'App\Listeners\UpdateSegmentValidation'
        ],
        'App\Events\SegmentDeleted' => [
            'App\Listeners\UpdateValidation'
        ],
        'App\Events\ShopFindingCreated' => [
            'App\Listeners\SetShopFindingPlannerGroup',
            'App\Listeners\SetShopFindingStatus',
            'App\Listeners\SetShopFindingPlantCode'
        ],
        'Illuminate\Mail\Events\MessageSending' => [
            'App\Listeners\RestrictEmailDomain'
        ],
        
        /*
        Whenever a notification planner group, plant code or status is updated
        mirror the changes within the shopfinding record.
        */
        
        'App\Events\NotificationStatusUpdating' => [
            'App\Listeners\SetShopFindingStatus'
        ],
        'App\Events\NotificationPlannerGroupUpdating' => [
            'App\Listeners\SetShopFindingPlannerGroup'
        ],
        
        'App\Events\NotificationPlantCodeUpdating' => [
            'App\Listeners\SetShopFindingPlantCode'
        ],
        
        /*
        This is fired whenever the GetLatestNotificationsAndPieceParts command runs.
        And ensures the notification planner group and status are mirrored
        in the corresponding shopfinding records
        */
        
        'App\Events\SyncShopFindings' => [
            'App\Listeners\SetShopFindingPlannerGroup',
            'App\Listeners\SetShopFindingStatus',
            'App\Listeners\SetShopFindingPlantCode'
        ],
        
        'App\Events\PiecePartsBatchSave' => [
            'App\Listeners\UpdateValidationOnPiecePartsBatchSave'
        ],
        
        'App\Events\PiecePartsBatchCreated' => [
            'App\Listeners\PiecePartBatchActivitiesCreate'
        ],
        
        'App\Events\PiecePartsBatchUpdated' => [
            'App\Listeners\PiecePartBatchActivitiesUpdate'
        ],
        
        'App\Events\PartListSaved' => [
            'App\Listeners\FireRemoveUnwantedPartsCommand'
        ],
        
        'App\Events\PartListDeleted' => [
            'App\Listeners\FireRemoveUnwantedPartsCommand'
        ],
        
        /*
        When a notification piece part is created
        if it is a reversal trigger delete reversed piece part listener
        */
        
        'App\Events\NotificationPiecePartReversal' => [
            'App\Listeners\RemoveReversedPiecePart'
        ]
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
