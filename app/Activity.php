<?php

namespace App;

use App\User;
use App\PartList;
use Carbon\Carbon;
use App\HDR_Segment;
use App\PieceParts\WPS_Segment;
use App\PieceParts\NHS_Segment;
use App\PieceParts\RPS_Segment;
use App\ShopFindings\AID_Segment;
use App\ShopFindings\EID_Segment;
use App\ShopFindings\API_Segment;
use App\ShopFindings\RCS_Segment;
use App\ShopFindings\SAS_Segment;
use App\ShopFindings\SUS_Segment;
use App\ShopFindings\RLS_Segment;
use App\ShopFindings\LNK_Segment;
use App\ShopFindings\ATT_Segment;
use App\ShopFindings\SPT_Segment;
use App\ShopFindings\Misc_Segment;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;

class Activity extends Model
{
    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = ['id'];
    
    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = ['notification_id', 'class', 'fullname'];
    
    /**
     * Get the user related to the activity.
     */
    public function user()
    {
        return $this->belongsTo('App\User');
    }
    
    /**
     * Get the shop finding associated to the activity.
     */
    public function shop_finding()
    {
        return $this->belongsTo('App\ShopFindings\ShopFinding');
    }
    
    public function morphTo($name = NULL, $type = NULL, $id = NULL, $ownerKey = NULL)
    {
        return \App\PieceParts\WPS_Segment::where('id', $id)->first();
    }
    
    /**
     * Get the associated subject instance.
     *
     * @return mixed
     */
    public function getSubject()
    {
        $class = '\\'. $this->subject_type;
        
        return $class::find($this->subject_id);
    }
    
    /**
     * Parse the subject type into a readable form.
     *
     * @return string
     */
    public function getSubjectName()
    {
        return str_replace('_', ' ', (new \ReflectionClass($this->subject_type))->getShortName());
    }
    
    /**
     * Get the notification id for the activity if applicable.
     *
     * @return bool
     */
    public function getNotificationIdAttribute()
    {
        $item = $this->subject_type::find($this->subject_id);
        
        return $item && method_exists($item, 'getShopFindingId') ? $item->getShopFindingId() : NULL;
    }
    
    /**
     * Get the class of the activity.
     *
     * @return bool
     */
    public function getClassAttribute()
    {
        return (new \ReflectionClass($this->subject_type))->getShortName();
    }
    
    /**
     * Get the name of the user that actioned the activity.
     *
     * @return string
     */
    public function getFullnameAttribute()
    {
        return $this->user ? $this->user->fullname : 'Unknown';
    }
    
    /**
     * Get a filtered list of activities.
     *
     * @param (string) $search
     * @param (string) $type
     * @param (integer) $plantCode
     * @param (string) $dateStart
     * @param (string) $dateEnd
     * @param (string) $orderby
     * @param (string) $order
     * @return \Illuminate\Pagination\LengthAwarePaginator $activities
     */
    public static function getActivities($search = NULL, $type = NULL, $plantCode = NULL, $dateStart = NULL, $dateEnd = NULL, $orderby = 'activities.created_at', $order = 'desc')
    {
        try {
            $dateStart = Carbon::createFromFormat('d/m/Y', $dateStart)->format('Y-m-d 00:00:00') ?? NULL;
        } catch (\InvalidArgumentException $e) {
            $dateStart = NULL;
        }
        
        try {
            $dateEnd = Carbon::createFromFormat('d/m/Y', $dateEnd)->format('Y-m-d 23:59:59') ?? NULL;
        } catch (\InvalidArgumentException $e) {
            $dateEnd = NULL;
        }
        
        $activities = Activity::select(
                'activities.*',
                'u.id as uid',
                'part_lists.id as pl_id',
                DB::raw("COALESCE(
                    hdr_segments.shop_finding_id,
                    wps_segments.SFI,
                    aid.shop_finding_id,
                    eid.shop_finding_id,
                    api.shop_finding_id,
                    rcs_segments.SFI,
                    sas.shop_finding_id,
                    sus.shop_finding_id,
                    rls.shop_finding_id,
                    lnk.shop_finding_id,
                    att.shop_finding_id,
                    spt.shop_finding_id,
                    misc.shop_finding_id,
                    nhs_pp.shop_finding_id,
                    rps_pp.shop_finding_id
                ) as notification_id"),
                DB::raw("COALESCE(not.plant_code, u_loc.plant_code, pl_loc.plant_code) as plant_code"),
                DB::raw("COALESCE(wps_segments.PPI, nhs_segments.piece_part_detail_id, rps_segments.piece_part_detail_id) as piece_part_id"),
                DB::raw(
                'CASE activities.subject_type
                    WHEN "'.addslashes(User::class).'" THEN "User"
                    WHEN "'.addslashes(HDR_Segment::class).'" THEN "HDR_Segment"
                    WHEN "'.addslashes(WPS_Segment::class).'" THEN "WPS_Segment"
                    WHEN "'.addslashes(NHS_Segment::class).'" THEN "NHS_Segment"
                    WHEN "'.addslashes(RPS_Segment::class).'" THEN "RPS_Segment"
                    WHEN "'.addslashes(AID_Segment::class).'" THEN "AID_Segment"
                    WHEN "'.addslashes(EID_Segment::class).'" THEN "EID_Segment"
                    WHEN "'.addslashes(API_Segment::class).'" THEN "API_Segment"
                    WHEN "'.addslashes(RCS_Segment::class).'" THEN "RCS_Segment"
                    WHEN "'.addslashes(SAS_Segment::class).'" THEN "SAS_Segment"
                    WHEN "'.addslashes(SUS_Segment::class).'" THEN "SUS_Segment"
                    WHEN "'.addslashes(RLS_Segment::class).'" THEN "RLS_Segment"
                    WHEN "'.addslashes(LNK_Segment::class).'" THEN "LNK_Segment"
                    WHEN "'.addslashes(ATT_Segment::class).'" THEN "ATT_Segment"
                    WHEN "'.addslashes(SPT_Segment::class).'" THEN "SPT_Segment"
                    WHEN "'.addslashes(Misc_Segment::class).'" THEN "Misc_Segment"
                    WHEN "'.addslashes(PartList::class).'" THEN "PartList"
                    ELSE "User"
                END AS type'
                ),
                'activities.created_at',
                'not.plant_code'
            )
            
            // The user doing the activity.
            ->leftJoin('users as actioner', 'activities.user_id', '=', 'actioner.id')
            
            // The user being created/deleted/updated.
            ->leftJoin('users as u', function ($join) {
                $join->on('activities.subject_id', '=', 'u.id');
                $join->where('activities.subject_type', '=', User::class);
            })
            
            // The location of the user that was created/updated/deleted/
            ->leftJoin('locations as u_loc', 'u.location_id', '=', 'u_loc.id')
            
            ->leftJoin('hdr_segments', function ($join) {
                $join->on('activities.subject_id', '=', 'hdr_segments.id');
                $join->where('activities.subject_type', '=', HDR_Segment::class);
            })
            
            ->leftJoin('wps_segments', function ($join) {
                $join->on('activities.subject_id', '=', 'wps_segments.PPI');
                $join->where('activities.subject_type', '=', WPS_Segment::class);
            })
            
            ->leftJoin('nhs_segments', function ($join) {
                $join->on('activities.subject_id', '=', 'nhs_segments.id');
                $join->where('activities.subject_type', '=', NHS_Segment::class);
            })
            ->leftJoin('piece_part_details as nhs', 'NHS_Segments.piece_part_detail_id', '=', 'nhs.id')
            ->leftJoin('piece_parts as nhs_pp', 'nhs_pp.id', '=', 'nhs.piece_part_id')
            
            ->leftJoin('rps_segments', function ($join) {
                $join->on('activities.subject_id', '=', 'rps_segments.id');
                $join->where('activities.subject_type', '=', RPS_Segment::class);
            })
            ->leftJoin('piece_part_details as rps', 'RPS_Segments.piece_part_detail_id', '=', 'rps.id')
            ->leftJoin('piece_parts as rps_pp', 'rps_pp.id', '=', 'rps.piece_part_id')
            
            ->leftJoin('aid_segments', function ($join) {
                $join->on('activities.subject_id', '=', 'aid_segments.id');
                $join->where('activities.subject_type', '=', AID_Segment::class);
            })
            ->leftJoin('shop_findings_details as aid', 'aid_segments.shop_findings_detail_id', '=', 'aid.id')
            
            ->leftJoin('eid_segments', function ($join) {
                $join->on('activities.subject_id', '=', 'eid_segments.id');
                $join->where('activities.subject_type', '=', EID_Segment::class);
            })
            ->leftJoin('shop_findings_details as eid', 'eid_segments.shop_findings_detail_id', '=', 'eid.id')
            
            ->leftJoin('api_segments', function ($join) {
                $join->on('activities.subject_id', '=', 'api_segments.id');
                $join->where('activities.subject_type', '=', API_Segment::class);
            })
            ->leftJoin('shop_findings_details as api', 'api_segments.shop_findings_detail_id', '=', 'api.id')
            
            ->leftJoin('rcs_segments', function ($join) {
                $join->on('activities.subject_id', '=', 'rcs_segments.id');
                $join->where('activities.subject_type', '=', RCS_Segment::class);
            })
            
            ->leftJoin('sas_segments', function ($join) {
                $join->on('activities.subject_id', '=', 'sas_segments.id');
                $join->where('activities.subject_type', '=', SAS_Segment::class);
            })
            ->leftJoin('shop_findings_details as sas', 'sas_segments.shop_findings_detail_id', '=', 'sas.id')
            
            ->leftJoin('sus_segments', function ($join) {
                $join->on('activities.subject_id', '=', 'sus_segments.id');
                $join->where('activities.subject_type', '=', SUS_Segment::class);
            })
            ->leftJoin('shop_findings_details as sus', 'sus_segments.shop_findings_detail_id', '=', 'sus.id')
            
            ->leftJoin('rls_segments', function ($join) {
                $join->on('activities.subject_id', '=', 'rls_segments.id');
                $join->where('activities.subject_type', '=', RLS_Segment::class);
            })
            ->leftJoin('shop_findings_details as rls', 'rls_segments.shop_findings_detail_id', '=', 'rls.id')
            
            ->leftJoin('LNK_Segments', function ($join) {
                $join->on('activities.subject_id', '=', 'LNK_Segments.id');
                $join->where('activities.subject_type', '=', LNK_Segment::class);
            })
            ->leftJoin('shop_findings_details as lnk', 'LNK_Segments.shop_findings_detail_id', '=', 'lnk.id')
            
            ->leftJoin('att_segments', function ($join) {
                $join->on('activities.subject_id', '=', 'att_segments.id');
                $join->where('activities.subject_type', '=', ATT_Segment::class);
            })
            ->leftJoin('shop_findings_details as att', 'att_segments.shop_findings_detail_id', '=', 'att.id')
            
            ->leftJoin('SPT_Segments', function ($join) {
                $join->on('activities.subject_id', '=', 'SPT_Segments.id');
                $join->where('activities.subject_type', '=', SPT_Segment::class);
            })
            ->leftJoin('shop_findings_details as spt', 'SPT_Segments.shop_findings_detail_id', '=', 'spt.id')
            
            ->leftJoin('misc_Segments', function ($join) {
                $join->on('activities.subject_id', '=', 'misc_Segments.id');
                $join->where('activities.subject_type', '=', Misc_Segment::class);
            })
            ->leftJoin('shop_findings_details as misc', 'misc_Segments.shop_findings_detail_id', '=', 'misc.id')
            
            ->leftJoin('part_lists', function ($join) {
                $join->on('activities.subject_id', '=', 'part_lists.id');
                $join->where('activities.subject_type', '=', PartList::class);
            })
            
            ->leftJoin('locations as pl_loc', 'part_lists.location_id', '=', 'pl_loc.id')
            
            ->leftJoin('HDR_Segments as hdr', DB::raw("COALESCE(
                    HDR_Segments.shop_finding_id,
                    WPS_Segments.SFI,
                    aid.shop_finding_id,
                    eid.shop_finding_id,
                    api.shop_finding_id,
                    RCS_Segments.SFI,
                    sas.shop_finding_id,
                    sus.shop_finding_id,
                    rls.shop_finding_id,
                    lnk.shop_finding_id,
                    att.shop_finding_id,
                    spt.shop_finding_id,
                    misc.shop_finding_id,
                    nhs_pp.shop_finding_id,
                    rps_pp.shop_finding_id
                )"), '=', 'hdr.shop_finding_id')
                
            ->leftJoin('notifications as not', DB::raw("COALESCE(
                    HDR_Segments.shop_finding_id,
                    WPS_Segments.SFI,
                    aid.shop_finding_id,
                    eid.shop_finding_id,
                    api.shop_finding_id,
                    RCS_Segments.SFI,
                    sas.shop_finding_id,
                    sus.shop_finding_id,
                    rls.shop_finding_id,
                    lnk.shop_finding_id,
                    att.shop_finding_id,
                    spt.shop_finding_id,
                    misc.shop_finding_id,
                    nhs_pp.shop_finding_id,
                    rps_pp.shop_finding_id
                )"), '=', 'not.id');
            
        if ($search) {
            if ($search) {
                $activities = $activities->whereNested(function($query) use ($search) {
                    $query->where(DB::raw("CONCAT(actioner.first_name, ' ', actioner.last_name)"), 'LIKE', "%$search%")
                        ->orWhere('HDR_Segments.shop_finding_id', 'LIKE', "%$search%")
                        ->orWhere('WPS_Segments.SFI', 'LIKE', "%$search%")
                        ->orWhere('aid.shop_finding_id', 'LIKE', "%$search%")
                        ->orWhere('eid.shop_finding_id', 'LIKE', "%$search%")
                        ->orWhere('api.shop_finding_id', 'LIKE', "%$search%")
                        ->orWhere('RCS_Segments.SFI', 'LIKE', "%$search%")
                        ->orWhere('sas.shop_finding_id', 'LIKE', "%$search%")
                        ->orWhere('sus.shop_finding_id', 'LIKE', "%$search%")
                        ->orWhere('rls.shop_finding_id', 'LIKE', "%$search%")
                        ->orWhere('lnk.shop_finding_id', 'LIKE', "%$search%")
                        ->orWhere('att.shop_finding_id', 'LIKE', "%$search%")
                        ->orWhere('spt.shop_finding_id', 'LIKE', "%$search%")
                        ->orWhere('misc.shop_finding_id', 'LIKE', "%$search%")
                        ->orWhere('nhs_pp.shop_finding_id', 'LIKE', "%$search%")
                        ->orWhere('rps_pp.shop_finding_id', 'LIKE', "%$search%");
                });
            }
        }
        
        if ($dateStart) {
            $activities = $activities->whereNested(function($query) use ($dateStart) {
                $query->where('activities.created_at', '>=', $dateStart);
            });
        }
        
        if ($dateEnd) {
            $activities = $activities->whereNested(function($query) use ($dateEnd) {
                $query->where('activities.created_at', '<=', $dateEnd);
            });
        }
        
        if ($plantCode) {
            $activities = $activities->having('plant_code', $plantCode);
        }
        
        if ($type) {
            $activities = $activities->having('type', $type);
        }
        
        // Doesn't work properly without the get() action chained.
        $activities = $activities->orderBy($orderby, $order)->get()->paginate(20);
        
        return $activities;
    }
}
