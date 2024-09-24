<?php

namespace App\Policies;

use App\User;
use App\BoeingData;
use Illuminate\Auth\Access\HandlesAuthorization;

class BoeingDataPolicy
{
    use HandlesAuthorization;

    public function before($user, $ability)
    {
        if ($user->isDataAdmin()) {
            return true;
        }
    }
    
    public function create(User $user)
    {
        return false;
    }
}
