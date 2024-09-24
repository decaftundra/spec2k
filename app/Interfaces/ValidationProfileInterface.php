<?php

namespace App\Interfaces;

interface ValidationProfileInterface
{
    public function isMandatory();
    
    /**
     * Get the form input array.
     *
     * @return array
     */
    public function getFormInputs();
    
    /**
     * Get form attributes for naming inputs on errors.
     *
     * @return array
     */
    public function getFormAttributes();
    
    /**
     * Get the validation rules.
     *
     * @param (int) $id
     * @return array
     */
    public function getValidationRules($id = NULL);
    
    /**
     * Get any custom validation messages.
     *
     * @return array
     */
    public function getValidationMessages();
}