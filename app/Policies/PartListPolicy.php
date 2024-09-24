<?php

namespace App\Policies;

use App\User;
use App\PartList;
use Illuminate\Auth\Access\HandlesAuthorization;

class PartListPolicy
{
    use HandlesAuthorization;
    
    public function before($user, $ability)
    {
        if ($user->isDataAdmin()) {
            return true;
        }
    }
    
    /**
     * Determine whether the user can view the part list index.
     *
     * @param  \App\User  $user
     * @return mixed
     */
    public function index(User $user)
    {
        return $user->isSiteAdmin();
    }

    /**
     * Determine whether the user can create a part list.
     *
     * @param  \App\User  $user
     * @return mixed
     */
    public function create(User $user)
    {
        $user->load('location');
        
        // Don't allow a part list to be created if on already exists for that location.
        $partList = PartList::where('location_id', $user->location->id)->first();
        
        if ($partList) {
            return false;
        }
        
        return $user->isSiteAdmin();
    }
    
    /**
     * Determine whether the user can view the location.
     *
     * @param  \App\User  $user
     * @param  \App\PartList  $partList
     * @return mixed
     */
    public function show(User $user, PartList $partList)
    {
        if ($user->isSiteAdmin()) {
            $user->load('location');
            
            return $user->location->id == $partList->location_id;
        }
        
        return false;
    }

    /**
     * Determine whether the user can update the location.
     *
     * @param  \App\User  $user
     * @param  \App\PartList  $partList
     * @return mixed
     */
    public function edit(User $user, PartList $partList)
    {
        if ($user->isSiteAdmin()) {
            $user->load('location');
            
            return $user->location->id == $partList->location_id;
        }
        
        return false;
    }

    /**
     * Determine whether the user can delete the location.
     *
     * @param  \App\User  $user
     * @param  \App\PartList  $partList
     * @return mixed
     */
    public function delete(User $user, PartList $partList)
    {
        if ($user->isSiteAdmin()) {
            $user->load('location');
            
            return $user->location->id == $partList->location_id;
        }
        
        return false;
    }
}
