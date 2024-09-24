<?php

namespace App\Traits;

trait MiscTraits
{
    /**
     * Check if a string ends with another string.
     *
     * @param (string) $string
     * @param (string) $endString
     * @return boolean
     */
    public function endsWith($string, $endString) 
    { 
        $len = strlen($endString); 
        if ($len == 0) { 
            return true; 
        } 
        return (substr($string, -$len) === $endString); 
    }
}