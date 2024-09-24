<?php

namespace App\Policies;

use App\Role;
use App\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class UserPolicy
{
    use HandlesAuthorization;

    public function before($user, $ability)
    {
        if ($user->isInactive()) {
            return false;
        }
        
        if ($user->isDataAdmin()) {
            return true;
        }
    }
    
    /**
     * Can the user view a listing of users.
     *
     * @param  \App\User  $user
     * @return mixed
     */
    public function index(User $user)
    {
        return !$user->isUser();
    }
    
    /**
     * Determine whether the user can view the model.
     *
     * @param  \App\User  $user
     * @param  \App\User  $model
     * @return mixed
     */
    public function show(User $user, User $model)
    {
        if (!$user->isUser()) {
            return $user->location_id == $model->location_id;
        }
        
        return false;
    }

    /**
     * Determine whether the user can create models.
     *
     * @param  \App\User  $user
     * @return mixed
     */
    public function create(User $user)
    {
        if ($user->isSiteAdmin()) {
            return true;
        }
        
        return false;
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  \App\User  $user
     * @param  \App\User  $model
     * @return mixed
     */
    public function update(User $user, User $model)
    {
        /*$inactiveRole = Role::where('name', 'inactive')->first();
        
        // Allow site admins to make users of the same site inactive as long as they are not a data admin.
        if (
            ($model->role_id == $inactiveRole->id) &&
            ($user->location_id == $model->location_id) &&
            (($model->getRawOriginal('role_id') <= $user->role_id))
        ) {
            return $user->isSiteAdmin();
        }
        
        // Model will only have an original role value if already saved.
        if ($model->getRawOriginal('role_id')) {
            // Don't allow an admin to downgrade a user of higher role.
            if ($user->role_id < $model->getRawOriginal('role_id')) return false;
        }
        
        // Don't allow an admin to upgrade a user to a higher role than themselves.
        if ($user->role_id < $model->role_id) return false;
        
        if ($user->isSiteAdmin()) {
            // Model will only have an original location value if already saved.
            if ($model->getRawOriginal('location_id')) {
                // Don't allow an admin to edit users from a different site.
                return $user->location_id == $model->getRawOriginal('location_id');
            }
            
            // Don't allow an admin to create users from a different site.
            return $user->location_id == $model->location_id;
        }
        
        // A user can edit their own account.
        if ($user->id === $model->id) {
            return true;
        }
        
        return false;*/
        
        // Model will only have an original role value if already saved.
        if ($model->getOriginal('role_id')) {
            
            $originalRole = Role::findOrFail($model->getOriginal('role_id'));
            
            //mydd($user->role->rank);
            //mydd($originalRole->rank);
            
            // Don't allow an admin to downgrade a user of higher rank.
            if ($user->role->rank < $originalRole->rank) return false;
        }
        
        $newRank = Role::findOrFail($model->role_id)->rank;
        
        // Don't allow an admin to upgrade a user to a higher rank than themselves.
        if ($user->role->rank < $newRank) return false;
        
        if ($user->isSiteAdmin()) {
            // Model will only have an original location value if already saved.
            if ($model->getOriginal('location_id')) {
                // Don't allow an admin to edit users from a different site.
                return $user->location_id == $model->getOriginal('location_id');
            }
            
            // Don't allow an admin to create users from a different site.
            return $user->location_id == $model->location_id;
        }
        
        // A user can edit their own account.
        if ($user->id === $model->id) {
            return true;
        }
        
        return false;
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\User  $user
     * @param  \App\User  $model
     * @return mixed
     */
    public function delete(User $user, User $model)
    {
        /*
        // Don't allow an admin to delete a user of higher role
        if ($user->role_id < $model->getRawOriginal('role_id')) return false;
        
        // Don't allow an admin to upgrade a user to a higher role than themselves.
        if ($user->role_id < $model->role_id) return false;
        
        if ($user->isSiteAdmin()) {
            return $user->location_id == $model->getRawOriginal('location_id');
        }
        */
        
        return false;
    }
}