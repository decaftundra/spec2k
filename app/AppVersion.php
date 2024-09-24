<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AppVersion extends Model
{
    /**
     * Get the application version.
     *
     * @return string
     */
    public static function getAppVersion()
    {
        return AppVersion::orderBy('id', 'desc')->first()->app_version;
    }
}
