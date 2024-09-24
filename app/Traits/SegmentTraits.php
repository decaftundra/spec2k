<?php

namespace App\Traits;

trait SegmentTraits
{
    /**
     * Put form inputs in order.
     *
     * @param (array) $a
     * @param (array) $b
     * @return boolean
     */
    public static function orderFormInputs($a, $b)
    {
        $c = $a['order'] ?? NULL;
        $d = $b['order'] ?? NULL;
        
        if ($c == $d) {
            return 0;
        }
        return ($c < $d) ? -1 : 1;
    }
}