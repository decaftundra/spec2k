<?php

namespace App\Policies;

use App\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class AircraftDetailPolicy
{
    use HandlesAuthorization;

    public function before($user, $ability)
    {
        if ($user->isDataAdmin()) {
            return true;
        }
        
        return false;
    }
}
