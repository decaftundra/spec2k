<?php

namespace App\Http\Controllers;

use App\Role;
use App\User;
use App\Alert;
use App\Activity;
use App\Location;
use App\Policies\UserPolicy;
use App\Mail\UserRegistered;
use Illuminate\Http\Request;
use App\Http\Requests\UserRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class UserController extends Controller
{
    /**
     * Whitelist of allowed orderby parameters.
     *
     * @var array
     */
    public static $orderbyWhitelist = [
        'name' => 'users.last_name',
        'email' => 'users.email',
        'role' => 'roles.name',
        'location' => 'locations.name',
        'last' => 'users.last_active_at',
        'group' => 'users.planner_group'
    ];
    
    /**
     * The default order by column.
     *
     * @var string
     */
    public static $defaultOrderBy = 'name';
    
    /**
     * The default order.
     *
     * @var string
     */
    public static $defaultOrder = 'asc';
    
    /**
     * Get values for roles dropdown depending on user role.
     *
     * @return array
     */
    private function getRolesArray()
    {
        $rolesArray = Role::where('id', '<=', auth()->user()->role_id)
            ->orderBy('id', 'asc')
            ->pluck('name', 'id')
            ->toArray();
            
        $inactiveRoleArray = Role::where('name', 'inactive')
            ->pluck('name', 'id')
            ->toArray();
            
        if (auth()->user()->isSiteAdmin() || auth()->user()->isDataAdmin()) {
            $rolesArray = array_replace($rolesArray, $inactiveRoleArray);
        }
        
        return $rolesArray;
    }
    
    /**
     * Get values for roles dropdown depending on user role.
     *
     * @return array
     */
    private function getLocationsArray()
    {
        $filter = [];
        
        if (auth()->user()->isDataAdmin()) {
            $locations = Location::orderBy('name')->get();
        } else {
            $locations =  Location::where('id', auth()->user()->location_id)
                ->orderBy('name')
                ->get();
        }
        
        if (count($locations)) {
            foreach ($locations as $location) {
                $filter[$location->id] = $location->name . ' [' . $location->sap_location_name . ']';
            }
        }
        
        return $filter;
    }
    
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $this->authorize('index', User::class);
        
        $reportingOrganisations = Location::filter('view-all-users');
        
        $search = $request->search;
        $filter = $request->filter ?? NULL;
        
        if ($request->pc == 'All') {
            $plantCode = NULL;
        } else if ($request->pc) {
            $plantCode = $request->pc;
        } else {
            $plantCode = NULL;
        }
        
        $orderby = self::$orderbyWhitelist[self::$defaultOrderBy];
        $order = self::$defaultOrder;
        
        if ($request->has('orderby') && array_key_exists($request->orderby, self::$orderbyWhitelist)) {
            $orderby = self::$orderbyWhitelist[$request->orderby];
        }
        
        if ($request->has('order') && in_array($request->order, ['desc', 'asc'])) {
            $order = $request->order;
        }
        
        if (auth()->check() && Gate::denies('view-all-users')) {
            $plantCode = Location::findOrFail(auth()->user()->location_id)->plant_code;
        }
        
        $users = User::getUsers($filter, $search, $plantCode, $orderby, $order);
        
        $roles = $this->getRolesArray();
        
        $request->merge(['order' => $order == 'asc' ? 'desc' : 'asc'])->flash(); // swap order
        
        return view('user.index')
            ->with('users', $users)
            ->with('roles', $roles)
            ->with('reportingOrganisations', $reportingOrganisations);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $this->authorize('create', User::class);
        
        $roles = $this->getRolesArray();
        $locations = $this->getLocationsArray();
        
        return view('user.create', compact('roles', 'locations'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\UserRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(UserRequest $request)
    {
        $this->authorize('create', User::class);
        
        $user = new User;
        $user->first_name = $request->first_name;
        $user->last_name = $request->last_name;
        $user->email = $request->email;
        $user->role_id = $request->role_id;
        $user->location_id = $request->location_id;
        $user->password = Hash::make(Str::random(32));
        $user->planner_group = $request->filled('planner_group') ? strtoupper($request->planner_group) : NULL;
        $user->acronym = User::createAcronym($user->first_name, $user->last_name);
        
        // This will check the user isn't trying to create another user with a higher role.
        $this->authorize('update', $user);
        
        $user->save();
        
        Mail::to($user)->send(new UserRegistered($user));
        
        return redirect(route('user.index'))
            ->with(Alert::success('New user registered successfully!'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\User  $user
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request, User $user)
    {
        $this->authorize('update', $user);
        
        $roles = $this->getRolesArray();
        $locations = $this->getLocationsArray();
        
        return view('user.edit', compact('roles', 'user', 'locations'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UserRequest  $request
     * @param  \App\User  $user
     * @return \Illuminate\Http\Response
     */
    public function update(UserRequest $request, User $user)
    {
        $user->first_name = $request->first_name;
        $user->last_name = $request->last_name;
        $user->email = $request->email;
        $user->role_id = $request->role_id;
        $user->location_id = $request->location_id;
        $user->planner_group = $request->filled('planner_group') ? strtoupper($request->planner_group) : NULL;
        $user->acronym = User::updateAcronym($user->id, $request->first_name, $request->last_name);
        
        $this->authorize('update', $user);
        
        $user->save();
        
        return redirect(route('user.index'))
            ->with(Alert::success('User updated successfully!'));
    }
    
    /**
     * Show the form for deleting a resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\User  $user
     * @return \Illuminate\Http\Response
     */
    public function delete(Request $request, User $user)
    {
        $this->authorize('delete', $user);
        
        return view('user.delete', compact('user'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Http\Requests\UserRequest  $request
     * @param  \App\User  $user
     * @return \Illuminate\Http\Response
     */
    public function destroy(UserRequest $request, User $user)
    {
        $this->authorize('delete', $user);
        
        // Disassociate the user from their activities before deleting.
        $userActivities = Activity::where('user_id', $user->id)->update(['user_id' => NULL]);
        
        $user->messages()->sync([]); // Remove any associated message preferences.
        
        $user->delete();
        
        return redirect(route('user.index'))
            ->with(Alert::success('User deleted successfully!'));
    }
}