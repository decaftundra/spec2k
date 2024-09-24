<?php

namespace App\Policies;

use App\User;
use App\Segment;
use App\ShopFindings\ShopFinding;
use Illuminate\Auth\Access\HandlesAuthorization;

class SegmentPolicy
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
     * Determine whether the user can delete the model.
     *
     * @param  \App\User  $user
     * @param  \App\Segment  $model
     * @return mixed
     */
    public function delete(User $user, Segment $model)
    {
        $user->load('location');
        
        $shopFinding = ShopFinding::findOrFail($model->getShopFindingId());
        
        return $user->location->plant_code == $shopFinding->plant_code;
    }
}
