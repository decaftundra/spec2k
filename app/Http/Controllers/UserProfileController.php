<?php

namespace App\Http\Controllers;

use App\User;
use App\Alert;
use App\Events\PasswordUpdated;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests\UpdateProfileRequest;
use App\Http\Requests\UpdatePasswordRequest;

class UserProfileController extends Controller
{
    /**
     * Show the form for editing the specified resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function edit()
    {
        $this->authorize('update', Auth::user());
        
        return view('user-profile.edit');
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateProfileRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateProfileRequest $request)
    {
        $this->authorize('update', Auth::user());
        
        $user = Auth::user();
        $user->first_name = $request->first_name;
        $user->last_name = $request->last_name;
        $user->email = $request->email;
        $user->acronym = User::updateAcronym($user->id, $request->first_name, $request->last_name);
        $user->save();
        
        return redirect(route('notifications.index'))
            ->with(Alert::success('Details changed successfully!'));
    }
    
    /**
     * Show the form for editing the logged in user's password.
     *
     * @return \Illuminate\Http\Response
     */
    public function editPassword()
    {
        $this->authorize('update', Auth::user());
        
        return view('user-profile.edit-password');
    }
    
    /**
     * Update the logged in user's password.
     *
     * @param  \App\Http\Requests\UpdatePasswordRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function updatePassword(UpdatePasswordRequest $request)
    {
        $this->authorize('update', Auth::user());
        
        $user = Auth::user();
        $user->password = Hash::make($request->new_password);
        $user->save();
        
        event(new PasswordUpdated($user));
        
        return redirect(route('notifications.index'))
            ->with(Alert::success('Password changed successfully!'));
    }
}