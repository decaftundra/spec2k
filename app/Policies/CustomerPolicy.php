<?php

namespace App\Policies;

use App\User;
use App\Customer;
use Illuminate\Auth\Access\HandlesAuthorization;

class CustomerPolicy
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
    
    public function show(User $user, Customer $customer)
    {
        return false;
    }
    
    public function edit(User $user, Customer $customer)
    {
        return false;
    }
    
    public function delete(User $user, Customer $customer)
    {
        return false;
    }
}