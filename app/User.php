<?php

namespace App;

use App\Interfaces\RecordableInterface;
use App\Location;
use App\Notifications\ResetPasswordNotification as ResetNotification;
use App\Role;
use App\Traits\RecordActivityTrait;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Laravel\Passport\HasApiTokens;

class User extends Authenticatable implements RecordableInterface
{
    use HasApiTokens, Notifiable, RecordActivityTrait;
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'first_name', 'last_name', 'email', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];
    
    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = ['fullname'];
    
    protected $dates = [
        'created_at',
        'updated_at',
        'last_login'
    ];
    
    /**
     * Only users from these domains can be registered users.
     *
     * @var array
     */
    protected static $allowedDomains = [
        'meggitt.com',
        'thefusionworks.com',
        'parker.com'
    ];
    
    // Store the user roles.
    protected $user_role;
    protected $admin_role;
    protected $site_admin_role;
    protected $data_admin_role;
    protected $inactive_role;
    
    public function __construct(array $attributes = array())
    {
        parent::__construct($attributes);
        
        $roles = Cache::remember('roles', 86400, function () {
            return Role::all();
        });
        
        $this->user_role = $roles->where('name', 'user')->first();
        $this->admin_role = $roles->where('name', 'admin')->first();
        $this->site_admin_role = $roles->where('name', 'site_admin')->first();
        $this->data_admin_role = $roles->where('name', 'data_admin')->first();
        $this->inactive_role = $roles->where('name', 'inactive')->first();
    }
    
    /**
     * Get the role that owns the user.
     */
    public function role()
    {
        return $this->belongsTo('App\Role');
    }
    
    /**
     * Get the role that owns the user.
     */
    public function location()
    {
        return $this->belongsTo('App\Location');
    }
    
    /**
     * Get the activities related to the user.
     */
    public function activities()
    {
        return $this->hasMany('App\Activity');
    }
    
    /**
     * Get the user's shop findings.
     */
    public function shop_findings()
    {
        return $this->hasMany('App\ShopFindings\ShopFinding', 'planner_group', 'planner_group');
    }
    
    /**
     * The messages that belong to the user.
     */
    public function messages()
    {
        return $this->belongsToMany('App\Message');
    }

    /**
     * Unless user is a Data Admin allow access only to users of the same location.
     *
     * @return void
     */
    protected static function boot()
    {
        parent::boot();
        
        if (auth()->check() && Gate::denies('view-all-users')) {
            static::addGlobalScope('location', function (Builder $builder) {
                $builder->where('location_id', auth()->user()->location_id);
            });
        }
    }
    
    /**
     * Get the url for the activities listing page.
     *
     * @return string
     */
    public function getActivityUrl()
    {
        return route('user.edit', $this->id);
    }
    
    /**
     * Get the url title for the activities page.
     *
     * @return string
     */
    public function getActivityUrlTitle()
    {
        return 'View ' . $this->fullname;
    }
    
    /**
     * Send the password reset notification.
     *
     * @param  string  $token
     * @return void
     */
    public function sendPasswordResetNotification($token)
    {
        $this->notify(new ResetNotification($token));
    }
    
    /**
     * Does the user want to receive the given type of message.
     *
     * @param (string) $messageName
     * @return boolean
     */
    public function wantsMessage($messageName)
    {
        $wantedMessages = $this->messages()->pluck('name')->toArray();
        
        return in_array($messageName, $wantedMessages);
    }
    
    /**
     * Scope a query to only include users of a given role.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param mixed $type
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeUsers($query)
    {
        return $query->where('role_id', $this->user_role->id);
    }
    
    /**
     * Scope a query to only include users of a given role.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param mixed $type
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeAdmins($query)
    {
        return $query->where('role_id', $this->admin_role->id);
    }
    
    /**
     * Scope a query to only include users of a given role.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param mixed $type
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeSiteAdmins($query)
    {
        return $query->where('role_id', $this->site_admin_role->id);
    }
    
    /**
     * Scope a query to only include users of a given role.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param mixed $type
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeDataAdmins($query)
    {
        return $query->where('role_id', $this->data_admin_role->id);
    }
    
    /**
     * Scope a query to only include users of a given role.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param mixed $type
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeInactives($query)
    {
        return $query->where('role_id', $this->inactive_role->id);
    }
    
    /**
     * Scope a query to filter users by a given role.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param mixed $type
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeOfRole($query, $roleId)
    {
        return is_numeric($roleId) ? $query->where('role_id', $roleId) : $query;
    }
    
    /**
     * Scope a query to filter users by a search term.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param mixed $type
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeSearch($query, $search = NULL)
    {
        if (!$search) return $query;
        
        return $query->whereNested(function($query) use ($search) {
            $query->where('last_name', 'LIKE', "%$search%")
                  ->orWhere('first_name', 'LIKE',"%$search%")
                  ->orWhere('email', 'LIKE', "%$search%")
                  ->orWhereRaw("CONCAT(first_name, ' ', last_name) LIKE ?", [$search]);
        });
    }
    
    /**
     * Get filterable paginated collection of users.
     *
     * @param (int) $filter
     * @param (string) $search
     * @param (integer) $plantCode
     * @param (string) $orderby
     * @param (string) $order
     * @return \Illuminate\Pagination\LengthAwarePaginator $users
     */
    public static function getUsers($filter = NULL, $search = NULL, $plantCode = NULL, $orderby = NULL, $order = NULL)
    {
        $users =  DB::table('users')
            ->distinct()
            ->select(
                'users.id as id',
                DB::raw("CONCAT(users.first_name, ' ', users.last_name) as fullname"),
                'users.email',
                'users.role_id',
                'users.location_id',
                'users.planner_group',
                'roles.name as role',
                'locations.name as location',
                'locations.plant_code',
                'users.last_active_at',
                DB::raw("COUNT(activities.id) as activity_count")
            )
            ->leftJoin('roles', 'users.role_id', '=', 'roles.id')
            ->leftJoin('locations', 'users.location_id', '=', 'locations.id')
            ->leftJoin('activities', 'users.id', '=', 'activities.user_id')
            ->groupBy('users.id');
            
        if ($filter) {
            $users = $users->where('users.role_id', $filter);
        }
        
        if ($orderby) {
            $users = $users->orderBy($orderby, $order);
        }
        
        if ($plantCode) {
            $users = $users->where('locations.plant_code', $plantCode);
        }
        
        if ($search) {
            $users = $users->whereNested(function($query) use ($search) {
                $query->where('users.first_name', 'LIKE', "%$search%")
                    ->orWhere('users.last_name', 'LIKE', "%$search%");
            });
        }
            
        $users = $users->paginate(20);
        
        return $users;
    }
    
    /**
     * Is the user a basic user.
     *
     * @return boolean
     */
    public function isUser()
    {
        return $this->role_id == $this->user_role->id;
    }
    
    /**
     * Is the user an admin user.
     *
     * @return boolean
     */
    public function isAdmin()
    {
        return $this->role_id == $this->admin_role->id;
    }
    
    /**
     * Is the user a site admin user.
     *
     * @return boolean
     */
    public function isSiteAdmin()
    {
        return $this->role_id == $this->site_admin_role->id;
    }
    
    /**
     * Is the user a data admin user.
     *
     * @return boolean
     */
    public function isDataAdmin(): bool
    {
        return $this->role_id == $this->data_admin_role->id;
    }
    
    /**
     * Is the user inactive.
     *
     * @return boolean
     */
    public function isInactive()
    {
        return $this->role_id == $this->inactive_role->id;
    }
    
    /**
     * Get the default search filter for the user.
     *
     * @return string
     */
    public function defaultFilter()
    {
        return $this->isUser() ? 'current' : 'all';
    }
    
    /**
     * Get the default location for non-admin users.
     *
     * @return string
     */
    public function defaultLocation()
    {
        return !$this->isUser() ? NULL : $this->location->plant_code;
    }
    
    /**
     * Get the full name for the user.
     *
     * @return bool
     */
    public function getFullnameAttribute()
    {
        return $this->attributes['first_name'].' '.$this->attributes['last_name'];
    }
    
    /**
     * Get the allowed email domains.
     *
     * @return array
     */
    protected static function getAllowedDomains()
    {
        return self::$allowedDomains;
    }
    
    /**
     * Create a unique acronym for the user.
     *
     * @return string
     */
    public static function createAcronym($firstName, $lastName)
    {
        $acronym = self::makeAcronym($firstName, $lastName);
        
        $acronymsWithNumbers = self::where('acronym', 'REGEXP', "^{$acronym}(-[0-9]*)?$")
            ->orderByRaw("acronym + 1 asc")
            ->get();
        
        if (!count($acronymsWithNumbers)) return $acronym;
    	
    	$lastAcronymWithNumber = $acronymsWithNumbers->last();
    	
    	$lastAcronymNumber = intval(str_replace($acronym . '-', '', $lastAcronymWithNumber->acronym));
    	
    	return $acronym . '-' . ($lastAcronymNumber + 1);
    }
    
    /**
     * Create the basic acronym string.
     *
     * @param (string) $firstName
     * @param (string) $lastName
     * @return string
     */
    public static function makeAcronym($firstName, $lastName)
    {
        return str_replace(' ', '', strtoupper($lastName . substr($firstName, 0, 1)));
    }
    
    /**
     * Update user acronym.
     *
     * @param (string) $userId
     * @param (string) $firstName
     * @param (string) $lastName
     * @return string
     */
    public static function updateAcronym($userId, $firstName, $lastName)
    {
        $acronym = self::makeAcronym($firstName, $lastName);
        
        $acronymsWithNumbers = self::where('id', '!=', $userId)
            ->where('acronym', 'REGEXP', "^{$acronym}(-[0-9]*)?$")
            ->orderByRaw("acronym * 1 asc")
            ->get();
        
        if (!count($acronymsWithNumbers)) return $acronym;
    	
    	$lastAcronymWithNumber = $acronymsWithNumbers->last();
    	
    	$lastAcronymNumber = intval(str_replace($acronym . '-', '', $lastAcronymWithNumber->acronym));
    	
    	return $acronym . '-' . ($lastAcronymNumber + 1);
    }
}
