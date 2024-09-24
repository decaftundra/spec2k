<?php

namespace App\Policies;

use App\User;
use App\Issue;
use Illuminate\Auth\Access\HandlesAuthorization;

class IssuePolicy
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
        return true;
    }
    
    public function create(User $user)
    {
        return true;
    }
    
    public function show(User $user, Issue $issue)
    {
        return true;
    }
    
    public function edit(User $user, Issue $issue)
    {
        return false;
    }
    
    public function delete(User $user, Issue $issue)
    {
        return false;
    }
}
