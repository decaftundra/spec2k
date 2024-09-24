<?php

namespace App\Policies;

use App\MaintenanceNotice;
use App\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class MaintenanceNoticePolicy
{
    use HandlesAuthorization;
    
    public function before($user, $ability)
    {
        if ($user->isDataAdmin()) {
            return true;
        }
    }

    public function index(User $user)
    {
        return false;
    }
    
    public function create(User $user)
    {
        return false;
    }
    
    public function show(User $user, MaintenanceNotice $maintenanceNotice)
    {
        return false;
    }
    
    public function edit(User $user, MaintenanceNotice $maintenanceNotice)
    {
        return false;
    }
    
    public function delete(User $user, MaintenanceNotice $maintenanceNotice)
    {
        return false;
    }
}
