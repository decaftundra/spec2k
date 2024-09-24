<?php

namespace App\Policies;

use App\User;
use App\Notification;
use Illuminate\Auth\Access\HandlesAuthorization;

class NotificationPolicy
{
    use HandlesAuthorization;

    public function before($user, $ability)
    {
        if ($user->isDataAdmin()) {
            return true;
        }
        
        if ($user->isSiteAdmin()) {
            return true;
        }
        
        if ($user->isAdmin()) {
            return true;
        }
    }
         
    /**
     * Determine whether the user can view the model.
     *
     * @param  \App\User  $user
     * @param  \App\Notification  $model
     * @return mixed
     */
    public function show(User $user, Notification $model)
    {
        $user->load('location');
        
        if ($user->location->plant_code == $model->plant_code) return true;
        
        return false;
    }
}
