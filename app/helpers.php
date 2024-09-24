<?php

if (! function_exists('mydd')) {
    
    /**
     * Simple array/object dump with optional die.
     *
     * @param (mixed) $data
     * @param (bool) $die
     * @return string
     */
    function mydd($data, $die = null) {
        echo '<pre>';
        print_r($data);
        echo '</pre>';
        
        $die ? die() : null;
    }
}

if (! function_exists('set_active')) {
    
    /**
     * See whether the current route is active.
     *
     * @param (string) $url
     * @return boolean
     */
    function set_active($url)
    {
        return request()->is($url);
    }
}

if (! function_exists('isfloat')) {
    
    /**
     * See whether the value is a float.
     *
     * @param (mixed) $value
     * @return boolean
     */
    function isfloat($value) {
        // PHP automagically tries to coerce $value to a number
        return is_float($value + 0);
    }
}

if (! function_exists('tick')) {
    /**
     * Show glyphicon ok sign.
     */
    function tick()
    {
        echo '<span class="text-success glyphicon glyphicon-ok-sign" aria-hidden="true"></span>';
    }
}

if (! function_exists('question')) {
    /**
     * Show glyphicon question sign.
     */
    function question()
    {
        echo '<span class="text-warning glyphicon glyphicon-question-sign" aria-hidden="true"></span>';
    }
}

if (! function_exists('cross')) {
    /**
     * Show glyphicon remove sign.
     */
    function cross()
    {
        echo '<span class="text-danger glyphicon glyphicon-remove-sign" aria-hidden="true"></span>';
    }
}

if (! function_exists('asterisk')) {
    /**
     * Show asterisk.
     */
    function asterisk()
    {
        echo '<span class="text-danger">*</span>';
    }
}