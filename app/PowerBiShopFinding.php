<?php

namespace App;

use DateTimeInterface;
use Illuminate\Database\Eloquent\Model;

class PowerBiShopFinding extends Model
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
    public const POWER_BI_DIRECTORY = 'power-bi-data';
    
    /**
     * Get the table columns in the correct order.
     *
     * @return array
     */
    public static function getTableColumns()
    {
        return [
            "id",
            "plant_code",
            "status",
            "standby_at",
            "subcontracted_at",
            "scrapped_at",
            "shipped_at",
            "is_valid",
            "ready_to_export",
            "validation_report",
            "hdrCHG",
            "hdrROC",
            "hdrOPR",
            "hdrRON",
            "hdrWHO",
            "is_hdr_segment_valid",
            "aidMFR",
            "aidAMC",
            "aidMFN",
            "aidASE",
            "aidAIN",
            "aidREG",
            "aidOIN",
            "aidCTH",
            "aidCTY",
            "is_aid_segment_valid",
            "eidAET",
            "eidEPC",
            "eidAEM",
            "eidEMS",
            "eidMFR",
            "eidETH",
            "eidETC",
            "is_eid_segment_valid",
            "apiAET",
            "apiEMS",
            "apiAEM",
            "apiMFR",
            "apiATH",
            "apiATC",
            "is_api_segment_valid",
            "rcsSFI",
            "rcsMRD",
            "rcsMFR",
            "rcsMPN",
            "rcsSER",
            "rcsRRC",
            "rcsFFC",
            "rcsFFI",
            "rcsFCR",
            "rcsFAC",
            "rcsFBC",
            "rcsFHS",
            "rcsMFN",
            "rcsPNR",
            "rcsOPN",
            "rcsUSN",
            "rcsRET",
            "rcsCIC",
            "rcsCPO",
            "rcsPSN",
            "rcsWON",
            "rcsMRN",
            "rcsCTN",
            "rcsBOX",
            "rcsASN",
            "rcsUCN",
            "rcsSPL",
            "rcsUST",
            "rcsPDT",
            "rcsPML",
            "rcsSFC",
            "rcsRSI",
            "rcsRLN",
            "rcsINT",
            "rcsREM",
            "is_rcs_segment_valid",
            "sasINT",
            "sasSHL",
            "sasRFI",
            "sasMAT",
            "sasSAC",
            "sasSDI",
            "sasPSC",
            "sasREM",
            "is_sas_segment_valid",
            "susSHD",
            "susMFR",
            "susMPN",
            "susSER",
            "susMFN",
            "susPDT",
            "susPNR",
            "susOPN",
            "susUSN",
            "susASN",
            "susUCN",
            "susSPL",
            "susUST",
            "susPML",
            "susPSC",
            "is_sus_segment_valid",
            "rlsMFR",
            "rlsMPN",
            "rlsSER",
            "rlsRED",
            "rlsTTY",
            "rlsRET",
            "rlsDOI",
            "rlsMFN",
            "rlsPNR",
            "rlsOPN",
            "rlsUSN",
            "rlsRMT",
            "rlsAPT",
            "rlsCPI",
            "rlsCPT",
            "rlsPDT",
            "rlsPML",
            "rlsASN",
            "rlsUCN",
            "rlsSPL",
            "rlsUST",
            "rlsRFR",
            "is_rls_segment_valid",
            "lnkRTI",
            "is_lnk_segment_valid",
            "attTRF",
            "attOTT",
            "attOPC",
            "attODT",
            "is_att_segment_valid",
            "sptMAH",
            "sptFLW",
            "sptMST",
            "is_spt_segment_valid",
            "values",
            "is_misc_segment_valid",
        ];
    }
}