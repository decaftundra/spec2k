<?php

namespace App\Providers;

use App\User;
use App\Issue;
use App\Segment;
use App\Activity;
use App\CageCode;
use App\PartList;
use App\Customer;
use App\Location;
use App\BoeingData;
use App\MaintenanceNotice;
use App\Notification;
use App\Policies\UserPolicy;
use App\Policies\IssuePolicy;
use App\Policies\SegmentPolicy;
use App\Policies\CageCodePolicy;
use App\Policies\ActivityPolicy;
use App\Policies\CustomerPolicy;
use App\Policies\LocationPolicy;
use App\Policies\MaintenanceNoticePolicy;
use App\Policies\PartListPolicy;
use App\Policies\BoeingDataPolicy;
use App\Policies\NotificationPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;
use Laravel\Passport\Passport;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        User::class => UserPolicy::class,
        Notification::class => NotificationPolicy::class,
        Customer::class => CustomerPolicy::class,
        Issue::class => IssuePolicy::class,
        BoeingData::class => BoeingDataPolicy::class,
        Location::class => LocationPolicy::class,
        PartList::class => PartListPolicy::class,
        Activity::class => ActivityPolicy::class,
        CageCode::class => CageCodePolicy::class,
        Segment::class => SegmentPolicy::class,
        MaintenanceNotice::class => MaintenanceNoticePolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();
        
        Passport::routes();

        Gate::define('view-all-notifications', function ($user) {
            return !$user->isUser();
        });
        
        Gate::define('view-all-shopfindings', function ($user) {
            return !$user->isUser();
        });
        
        Gate::define('view-all-locations', function ($user) {
            return $user->isDataAdmin();
        });
        
        Gate::define('view-all-cage-codes', function ($user) {
            return $user->isDataAdmin();
        });
        
        Gate::define('view-all-part-lists', function ($user) {
            return $user->isDataAdmin();
        });
        
        Gate::define('view-all-users', function ($user) {
            return $user->isDataAdmin();
        });
        
        Gate::define('view-all-activities', function ($user) {
            return $user->isDataAdmin();
        });
        
        Gate::define('view-a-users-activities', function($authUser, $user){
            if ($authUser->isDataAdmin()) return true;
            
            return !$authUser->isUser() && ($authUser->location_id == $user->location_id);
        });
        
        Gate::define('edit-all-inputs', function ($user) {
            return !$user->isUser();
        });
        
        Gate::define('import-csv-data', function ($user) {
            return $user->isDataAdmin();
        });
        
        Gate::define('manage-utas-data', function ($user) {
            return $user->isDataAdmin();
        });
    }
}
