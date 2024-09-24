<?php

namespace App;

use DateTimeInterface;
use Illuminate\Database\Eloquent\Model;

class PowerBiPiecePart extends Model
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
        'wpsMRD'
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
     * Get the table columns in the correct order.
     *
     * @return array
     */
    public static function getTableColumns()
    {
        return [
            "id",
            "notification_id",
            "wpsSFI",
            "wpsPPI",
            "wpsPFC",
            "wpsMFR",
            "wpsMFN",
            "wpsMPN",
            "wpsSER",
            "wpsFDE",
            "wpsPNR",
            "wpsOPN",
            "wpsUSN",
            "wpsPDT",
            "wpsGEL",
            "wpsMRD",
            "wpsASN",
            "wpsUCN",
            "wpsSPL",
            "wpsUST",
            "is_wps_segment_valid",
            "nhsMFR",
            "nhsMPN",
            "nhsSER",
            "nhsMFN",
            "nhsPNR",
            "nhsOPN",
            "nhsUSN",
            "nhsPDT",
            "nhsASN",
            "nhsUCN",
            "nhsSPL",
            "nhsUST",
            "nhsNPN",
            "is_nhs_segment_valid",
            "rpsMPN",
            "rpsMFR",
            "rpsMFN",
            "rpsSER",
            "rpsPNR",
            "rpsOPN",
            "rpsUSN",
            "rpsASN",
            "rpsUCN",
            "rpsSPL",
            "rpsUST",
            "rpsPDT",
            "is_rps_segment_valid"
        ];
    }
}
