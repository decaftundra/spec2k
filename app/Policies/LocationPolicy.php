<?php

namespace App\Policies;

use App\User;
use App\Location;
use Illuminate\Auth\Access\HandlesAuthorization;

class LocationPolicy
{
    use HandlesAuthorization;

    public function before($user, $ability)
    {
        if ($user->isDataAdmin()) {
            return true;
        }
    }
    
    /**
     * Determine whether the user can view the locations index.
     *
     * @param  \App\User  $user
     * @return mixed
     */
    public function index(User $user)
    {
        return $user->isSiteAdmin();
    }

    /**
     * Determine whether the user can create locations.
     *
     * @param  \App\User  $user
     * @return mixed
     */
    public function create(User $user)
    {
        return false;
    }
    
    /**
     * Determine whether the user can view the location.
     *
     * @param  \App\User  $user
     * @param  \App\Location  $location
     * @return mixed
     */
    public function show(User $user, Location $location)
    {
        if ($user->isSiteAdmin()) {
            
            return $user->location_id == $location->id;
        }
        
        return false;
    }

    /**
     * Determine whether the user can update the location.
     *
     * @param  \App\User  $user
     * @param  \App\Location  $location
     * @return mixed
     */
    public function edit(User $user, Location $location)
    {
        if ($user->isSiteAdmin()) {
            $user->load('location');
            
            return $user->location_id == $location->id;
        }
        
        return false;
    }

    /**
     * Determine whether the user can delete the location.
     *
     * @param  \App\User  $user
     * @param  \App\Location  $location
     * @return mixed
     */
    public function delete(User $user, Location $location)
    {
        return false;
    }
}
