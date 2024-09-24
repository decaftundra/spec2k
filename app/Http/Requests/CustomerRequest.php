<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CustomerRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $customer = $this->route()->parameter('customer');
        
        switch($this->method()) {
            case 'DELETE':
            {
                return [];
            }
            case 'POST':
            {
                return [
                    'company_name' => 'required|max:55|unique:customers,company_name',
                    'icao' => 'nullable|min:3|max:5|unique:customers,icao'
                ];
            }
            case 'PUT':
            case 'PATCH':
            {
                return [
                    'company_name' => 'required|max:55|unique:customers,company_name,' . $customer->id,
                    'icao' => 'nullable|min:3|max:5|unique:customers,icao, ' . $customer->id
                ];
            }
            default:break;
        }
    }
}
