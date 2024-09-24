<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    /**
     * The users that belong to the message notification.
     */
    public function users()
    {
        return $this->belongsToMany('App\User');
    }
}
