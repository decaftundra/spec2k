<?php

namespace App\Policies;

use App\User;
use App\CageCode;
use Illuminate\Auth\Access\HandlesAuthorization;

class CageCodePolicy
{
    use HandlesAuthorization;

    public function before($user, $ability)
    {
        if ($user->isDataAdmin()) {
            return true;
        }
    }
}
