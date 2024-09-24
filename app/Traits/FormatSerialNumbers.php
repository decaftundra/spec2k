<?php

namespace App\Traits;

trait FormatSerialNumbers
{
    /**
     * If the string is numeric trim from the beginning, otherwise trim from the end.
     *
     * @param (mixed) $data
     * @param (int) $maxLength
     * @return string
     */
    public function cleverTrim($data, $maxLength)
    {
        // If it's not too long proceed.
        if (strlen($data) <= $maxLength) return $data;
        
        // If it is numeric trim characters from beginning.
        if (is_numeric($data)) {
            $length = strlen($data);
            
            return substr($data, $length-$maxLength, $maxLength);
        }
        
        // Trim characters from the end.
        return substr($data, 0, $maxLength);
    }
    
    /**
     * Format serial number.
     * Used cleverTrim function to trim length.
     * If the data starts with a hash, remove it.
     *
     * @param (mixed) $data
     * @param (int) $maxLength
     * @return string
     */
    function formatSerialNo($data, $maxLength)
    {
        $trimmed = $this->cleverTrim($data, $maxLength);
        
        $firstChar = substr($trimmed, 0, 1);
        
        if ($firstChar == '#') return substr($trimmed, 1);
        
        return $trimmed;
    }
}