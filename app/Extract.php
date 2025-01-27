<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Extract extends Model
{
    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = [
        'created_at',
        'updated_at',
        'started_at',
        'ended_at'
    ];
}
