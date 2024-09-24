<?php

namespace App\Rules;

use App\User;
use App\Traits\MiscTraits;
use Illuminate\Contracts\Validation\Rule;

class RestrictEmailDomain implements Rule
{
    use MiscTraits;
    
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct()
    {
        
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        $allowedDomains = User::getAllowedDomains();
        
        foreach ($allowedDomains as $domain) {
            if ($this->endsWith($value, $domain)) {
                return true;
            }
        }
        
        return false;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'The email address is not permitted.';
    }
}
