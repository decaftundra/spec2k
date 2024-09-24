<?php

namespace App\Providers;

use App\AppVersion;
use App\Codes\UtasReasonCode;
use Telemetry_Client;
use Illuminate\Support\Collection;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Validator;
use Illuminate\Pagination\LengthAwarePaginator;
use Laravel\Dusk\DuskServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Paginator::useBootstrap();
        
        Paginator::useBootstrapThree();
        
        Schema::defaultStringLength(191);
        
        // Easy pagination of collections.
        if (!Collection::hasMacro('paginate')) {

            Collection::macro('paginate', 
                function ($perPage = 15, $page = null, $options = []) {
                $page = $page ?: (Paginator::resolveCurrentPage() ?: 1);
                return (new LengthAwarePaginator(
                    $this->forPage($page, $perPage), $this->count(), $perPage, $page, $options))
                    ->withPath('');
            });
        }
        
        // Check against old password
        Validator::extend('old_password', function ($attribute, $value, $parameters, $validator) {
            return Hash::check($value, Auth::user()->password);
        });
        
        // Validate a float
        Validator::extend('float', function ($attribute, $value, $parameters, $validator) {
            $maxDigits = (int) $parameters[0];
            $maxDecimals = (int) $parameters[1];
            $beforeDecimalPoint = ($maxDigits - $maxDecimals) >= 1 ? ($maxDigits - $maxDecimals) : 1;
            
            $regex = '/^[0-9]{1,'.$beforeDecimalPoint.'}(\.[0-9]{1,'.$maxDecimals.'})?$/';
            
            return preg_match($regex, $value);
        });
        
        // Field must be empty when other field is a certain value.
        Validator::extend('empty_when', function($attribute, $value, $parameters, $validator){
            
            $key = $parameters[0];
            
            unset($parameters[0]);
            
            $data = $validator->getData();
            
            if (isset($data[$key]) && in_array($data[$key], $parameters)) {
                return empty($value);
            }
            
            return true;
        });
        
        Validator::extend('valid_reason_for_type', function($attribute, $value, $parameters, $validator){
            $data = $validator->getData();
            
            return UtasReasonCode::where('TYPE', $data['Type'])->where('REASON', $data['Reason'])->count();
        });
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        if ($this->app->environment('local', 'testing')) {
            $this->app->register(DuskServiceProvider::class);
            $this->app->register(\Staudenmeir\DuskUpdater\DuskServiceProvider::class);
        }
    }
}