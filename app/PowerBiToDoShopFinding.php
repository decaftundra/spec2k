<?php

namespace App;

use DateTimeInterface;
use Illuminate\Database\Eloquent\Model;

class PowerBiToDoShopFinding extends Model
{
    /**
     * Prepare a date for array / JSON serialization.
     *
     * @param  \DateTimeInterface  $date
     * @return string
     */
    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }
    
    /**
     * The "type" of the primary key ID.
     *
     * @var string
     */
    protected $keyType = 'string';
    
    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = [
        'standby_at',
        'subcontracted_at',
        'scrapped_at',
        'shipped_at',
        'rcsMRD',
        'susSHD',
        'rlsRED',
        'rlsDOI'
    ];
    
    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = [];
    
    /**
     * Disable timestamps.
     */
    public $timestamps = false;
    
    /**
     * The power bi directory name.
     *
     * @const string
     */
    public const POWER_BI_TO_DO_DIRECTORY = 'power-bi-to-do-data';
}