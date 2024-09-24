<?php

namespace App\Traits;

trait DateTrait
{
    /**
     * Validate the date format.
     *
     * @params string $date
     * @params string $format
     * @return boolean
     */
    public static function validateDate($date, $format = 'd/m/Y'){
        $d = \DateTime::createFromFormat($format, $date);
        return $d && $d->format($format) === $date;
    }
}