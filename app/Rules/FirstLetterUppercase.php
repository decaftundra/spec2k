<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class FirstLetterUppercase implements Rule
{
    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        return substr(strtoupper($value), 0, 1) === substr($value, 0, 1);
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        'The first letter of :attribute must be uppercase.';
    }
}
