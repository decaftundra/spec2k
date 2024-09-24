<?php

namespace App\Http\Requests;

use App\User;
use App\Rules\RestrictEmailDomain;
use Illuminate\Support\Facades\Log;
use Illuminate\Foundation\Http\FormRequest;

class UserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        $user = $this->route()->parameter('user');
        
        switch($this->method()) {
            case 'DELETE':
            {
                return $this->user()->can('delete', $user);
            }
            case 'POST':
            {
                return $this->user()->can('create', User::class);
            }
            case 'PUT':
            case 'PATCH':
            {
                return $this->user()->can('update', $user);
            }
            default:
            {
                return false;
            };
        }
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $user = $this->route()->parameter('user');
        
        switch($this->method()) {
            case 'DELETE':
            {
                return [];
            }
            case 'POST':
            {
                return [
                    'first_name' => 'required|string|max:255',
                    'last_name' => 'required|string|max:255',
                    'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email', new RestrictEmailDomain],
                    'role_id' => 'required|integer',
                    'location_id' => 'required|integer',
                    'planner_group' => 'nullable|not_in:Z11,Z14,Z18|unique:users,planner_group'
                ];
            }
            case 'PUT':
            case 'PATCH':
            {
                return [
                    'first_name' => 'required|string|max:255',
                    'last_name' => 'required|string|max:255',
                    'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,'.$user->id, new RestrictEmailDomain],
                    'role_id' => 'required|integer',
                    'location_id' => 'required|integer',
                    'planner_group' => 'nullable|not_in:Z11,Z14,Z18|unique:users,planner_group,'.$user->id
                ];
            }
            default:break;
        }
    }
    
    /**
     * Log validation errors.
     *
     * @param  \Illuminate\Validation\Validator  $validator
     * @return void
     */
    public function withValidator($validator)
    {
        if ($validator->fails()) {
            Log::error('User '.$this->method().' validation errors!', [$validator->errors()]);
        }
    }
}