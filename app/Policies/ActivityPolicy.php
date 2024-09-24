<?php

namespace App\Policies;

use App\User;
use App\Activity;
use Illuminate\Auth\Access\HandlesAuthorization;

class ActivityPolicy
{
    use HandlesAuthorization;

    public function before($user, $ability)
    {
        if ($user->isDataAdmin()) {
            return true;
        }
    }
    
    /**
     * Determine whether the user can view the activities index.
     *
     * @param  \App\User  $user
     * @return mixed
     */
    public function index(User $user)
    {
        return !$user->isUser();
    }
    
    /**
     * Determine whether the user can view the location.
     *
     * @param  \App\User  $user
     * @param  \App\Location  $location
     * @return mixed
     */
    public function show(User $user, Activity $activity)
    {
        if (!$user->isUser()) {
            
            $activityUser = User::findOrFail($activity->user_id);
            
            return $user->location_id == $activityUser->location_id;
        }
        
        return false;
    }
}
