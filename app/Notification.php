<?php

  namespace App;

  use App\Codes\PartStatusCode;
  use App\Events\NotificationPlannerGroupUpdating;
  use App\Events\NotificationPlantCodeUpdating;
  use App\Events\NotificationStatusUpdating;
  use App\Interfaces\AID_SegmentInterface;
  use App\Interfaces\API_SegmentInterface;
  use App\Interfaces\ATT_SegmentInterface;
  use App\Interfaces\EID_SegmentInterface;
  use App\Interfaces\HDR_SegmentInterface;
  use App\Interfaces\LNK_SegmentInterface;
  use App\Interfaces\RCS_SegmentInterface;
  use App\Interfaces\RecordInterface;
  use App\Interfaces\RLS_SegmentInterface;
  use App\Interfaces\SAS_SegmentInterface;
  use App\Interfaces\SegmentInterface;
  use App\Interfaces\SPT_SegmentInterface;
  use App\Interfaces\SUS_SegmentInterface;
  use App\ShopFindings\ShopFinding;
  use App\Traits\StatusTrait;
  use Carbon\Carbon;
  use DateTimeInterface;
  use Illuminate\Database\Eloquent\Builder;
  use Illuminate\Database\Eloquent\Model;
  use Illuminate\Database\Eloquent\SoftDeletes;
  use Illuminate\Support\Facades\App;
  use Illuminate\Support\Facades\Artisan;
  use Illuminate\Support\Facades\DB;
  use Illuminate\Support\Facades\Gate;
  use Illuminate\Support\Facades\Log;
  use Illuminate\Database\Eloquent\Relations\HasMany;


  class Notification extends Model implements
    RecordInterface,
    SegmentInterface,
    HDR_SegmentInterface,
    AID_SegmentInterface,
    EID_SegmentInterface,
    API_SegmentInterface,
    RCS_SegmentInterface,
    SAS_SegmentInterface,
    SUS_SegmentInterface,
    RLS_SegmentInterface,
    LNK_SegmentInterface,
    ATT_SegmentInterface,
    SPT_SegmentInterface
  {
    use StatusTrait, SoftDeletes;

    /**
     * Prepare a date for array / JSON serialization.
     *
     * @param \DateTimeInterface $date
     * @return string
     */
    protected function serializeDate(DateTimeInterface $date)
    {
      return $date->format('Y-m-d H:i:s');
    }

    protected $table = 'notifications';

    public $incrementing = false;

    /**
     * The "type" of the primary key ID.
     *
     * @var string
     */
    protected $keyType = 'string';

    protected $guarded = ['PieceParts'];

    protected $dates = [
      'hdrRDT',
      'hdrRSD',
      'rcsMRD',
      'susSHD',
      'rlsRED',
      'rlsDOI',
      'created_at',
      'updated_at',
      'deleted_at',
      'standby_at',
      'subcontracted_at',
      'scrapped_at',
      'shipped_at',
      'csv_import_autosaved_at',
    ];

    protected $appends = ['is_utas'];

    /**
     * The "booting" method of the model.
     *
     * @return void
     */
    protected static function boot()
    {
      parent::boot();

      // Determine if the user can view all notifications and restrict accordingly.
      if (auth()->check() && Gate::denies('view-all-notifications')) {
        static::addGlobalScope('permission_to_view', function (Builder $builder) {
          $builder->where('plant_code', auth()->user()->location->plant_code);
        });
      }

      /**
       * Update corresponding shop finding record status if the notification status changes.
       */
      self::updating(function ($model) {
        $original = $model->getRawOriginal();

        // Sync status.
        if ($model->status != $original['status']) {
          if (!App::environment('live')) {
            Log::info(
              'Firing status update event. Changing notification ID: ' . $model->id . ' from ' . $original['status'] . ' to ' . $model->status . '.',
            );
          }

          event(new NotificationStatusUpdating($model));
        }

        // Sync planner group.
        if ($model->planner_group != $original['planner_group']) {
          if (!App::environment('live')) {
            Log::info(
              'Firing planner group update event. Changing notification ID: ' . $model->id . ' from ' . $original['planner_group'] . ' to ' . $model->planner_group . '.',
            );
          }

          event(new NotificationPlannerGroupUpdating($model));
        }

        // Sync plant code.
        if ($model->plant_code != $original['plant_code']) {
          if (!App::environment('live')) {
            Log::info(
              'Firing plant code update event. Changing notification ID: ' . $model->id . ' from ' . $original['plant_code'] . ' to ' . $model->plant_code . '.',
            );
          }

          event(new NotificationPlantCodeUpdating($model));
        }
      });
    }

    public function pieceParts(): HasMany
    {
      return $this->hasMany(NotificationPiecePart::class, 'notification_id');
    }

    /**
     * Is this a UTAS part.
     *
     * @return boolean
     */
    public function getIsUtasAttribute()
    {
      $codes = UtasCode::getAllUtasCodes();

      return in_array($this->get_RCS_MPN(), $codes);
    }

    /**
     * Get the author id.
     *
     * @return string
     */
    public function get_ATA_AuthorId()
    {
      return $this->getByKey('ataID');
    }

    /**
     * Get the author version.
     *
     * @return integer
     */
    public function get_ATA_AuthorVersion()
    {
      return $this->getByKey('ataVersion') ? (int)$this->getByKey('ataVersion') : null;
    }

    /**
     * Get the shop findings version.
     *
     * @return integer
     */
    public function get_SF_Version()
    {
      return $this->getByKey('SFVersion') ? (int)$this->getByKey('SFVersion') : null;
    }

    /**
     * Get value by key.
     *
     * @param (string) $key
     * @return mixed
     */
    public function getByKey($key)
    {
      return $this->{$key} ?? null;
    }

    /**
     * Get a list of notifications with utas info, piece part count.
     *
     * @param (mixed) $search
     * @param (string) $status
     * @param (integer) $plantCode
     * @param (string) $dateStart
     * @param (string) $dateEnd
     * @param (string) $orderby
     * @param (string) $order
     * @return Illuminate\Pagination\LengthAwarePaginator $notifications
     */
    public static function getToDoList(
      $search = null,
      $status = null,
      $plantCode = null,
      $dateStart = null,
      $dateEnd = null,
      $orderby,
      $order,
    ) {
      try {
        $dateStart = Carbon::createFromFormat('d/m/Y', $dateStart)->format('Y-m-d 00:00:00') ?? null;
      } catch (\InvalidArgumentException $e) {
        $dateStart = null;
      }

      try {
        $dateEnd = Carbon::createFromFormat('d/m/Y', $dateEnd)->format('Y-m-d 23:59:59') ?? null;
      } catch (\InvalidArgumentException $e) {
        $dateEnd = null;
      }

      // Get Utas Codes in an array then check against it to assign is_utas property.
      $utasCodes = implode("','", UtasCode::getAllUtasCodes());

      //$subSelect = self::getNotificationPiecePartsCount();

      $notifications = DB::table('notifications')
        ->select(
          'notifications.id',
          'notifications.status',
          'notifications.rcsSFI',
          'notifications.rcsMPN',
          'notifications.rcsSER',
          'notifications.hdrROC',
          'notifications.hdrRON',
          'notifications.rcsMRD',
          //'ppc.piece_part_count',
          DB::raw("IF(notifications.rcsMPN IN ('$utasCodes'), 1, NULL) as is_utas"),
        )
        /*->leftJoin(DB::raw("($subSelect) as ppc"), function($join){
            $join->on('ppc.notification_id', '=', 'notifications.id');
        })*/
        ->whereNotExists(function ($query) {
          $query
            ->select('id')
            ->from('shop_findings')
            ->whereRaw('notifications.id = shop_findings.id');
        })
        ->whereNotNull('plant_code')
        ->whereNull('deleted_at');

      // Filter by permission.
      if (auth()->check() && Gate::denies('view-all-notifications')) {
        $notifications = $notifications->where('plant_code', auth()->user()->location->plant_code);
      }

      // Filter by Plant Code.
      if ($plantCode) {
        $notifications = $notifications->where('plant_code', $plantCode);
      }

      if ($status) {
        $notifications = $notifications->where('notifications.status', $status);
      }

      if ($search) {
        $notifications = $notifications->whereNested(function ($query) use ($search) {
          $query
            ->where('notifications.id', 'LIKE', "%$search%")
            ->orWhere('notifications.rcsMPN', 'LIKE', "%$search%")
            ->orWhere('notifications.rcsSER', 'LIKE', "%$search%")
            ->orWhere('notifications.hdrROC', 'LIKE', "%$search%");
        });
      }

      if ($dateStart) {
        $notifications = $notifications->where('notifications.rcsMRD', '>=', $dateStart);
      }

      if ($dateEnd) {
        $notifications = $notifications->where('notifications.rcsMRD', '<=', $dateEnd);
      }

      $notifications = $notifications->orderBy($orderby, $order)->get();

      return $notifications;
    }

    /**
     * Get the piece part count sql for each notification.
     *
     * @return string
     */
    private static function getNotificationPiecePartsCount()
    {
      return "Select notification_piece_parts.notification_id, COUNT(notification_piece_parts.id) as piece_part_count
                from notification_piece_parts
                where notification_piece_parts.deleted_at IS NULL
                group by notification_piece_parts.notification_id";
    }

    /**
     * |--------------------------------
     * | HEADER FUNCTIONS
     * |--------------------------------
     */

    /**
     * Get the Change Code.
     *
     * @return string
     */
    public function get_HDR_CHG()
    {
      return (string)$this->getByKey('hdrCHG');
    }

    /**
     * Get the Reporting Organisation Name.
     *
     * @return string
     */
    public function get_HDR_RON()
    {
      $sapName = (string)$this->getByKey('hdrRON');

      $location = Location::where('sap_location_name', $sapName)->first();

      return $location ? $location->name : (string)$this->getByKey('hdrRON');
    }

    /**
     * Get the Reporting Organisation Cage Code.
     *
     * @return string
     */
    public function get_HDR_ROC()
    {
      return (string)$this->getByKey('hdrROC');
    }

    /**
     * Get the Operator Code.
     *
     * @return string
     */
    public function get_HDR_OPR()
    {
      return (string)$this->getByKey('hdrOPR');
    }

    /**
     * Get the Operator Name.
     *
     * @return string
     */
    public function get_HDR_WHO()
    {
      return (string)$this->getByKey('hdrWHO');
    }

    /**
     * Get the Reporting Period Start Date.
     *
     * @return date
     */
    public function get_HDR_RDT()
    {
      return $this->hdrRDT ? $this->hdrRDT->format('d/m/Y') : null;
    }

    /**
     * Get the Reporting Period End Date.
     *
     * @return date
     */
    public function get_HDR_RSD()
    {
      return $this->hdrRSD ? $this->hdrRSD->format('d/m/Y') : null;
    }

    /**
     * |--------------------------------
     * | AIRFRAME INFORMATION FUNCTIONS
     * |--------------------------------
     */

    /**
     * Get the Airframe Manufacturer Code.
     *
     * @return string
     */
    public function get_AID_MFR()
    {
      // Quick fix to correct bad SAP data missing leading zero.
      if ($this->getByKey('aidMFR') == '5167') {
        return (string)'05167';
      }

      if (in_array((string)$this->getByKey('aidMFR'), AircraftDetail::getPermittedValues())) {
        return (string)$this->getByKey('aidMFR');
      }

      return null;
    }

    /**
     * Get the Airframe Manufacturer Name.
     *
     * @return string
     */
    public function get_AID_MFN()
    {
      return (string)$this->getByKey('aidMFN');
    }

    /**
     * Get the Aircraft Model.
     *
     * @return string
     */
    public function get_AID_AMC()
    {
      return (string)$this->getByKey('aidAMC');
    }

    /**
     * Get the Aircraft Series.
     *
     * @return string
     */
    public function get_AID_ASE()
    {
      return (string)$this->getByKey('aidASE');
    }

    /**
     * Get the Aircraft Manufacturer Serial Number.
     *
     * @return string
     */
    public function get_AID_AIN()
    {
      return (string)$this->getByKey('aidAIN');
    }

    /**
     * Get the Aircraft Registration Number.
     *
     * @return string
     */
    public function get_AID_REG()
    {
      return (string)$this->getByKey('aidREG');
    }

    /**
     * Get the Operator Aircraft Internal Identifier.
     *
     * @return string
     */
    public function get_AID_OIN()
    {
      return (string)$this->getByKey('aidOIN');
    }

    /**
     * Get the Aircraft Cumulative Total Flight Hours.
     *
     * @return float
     */
    public function get_AID_CTH()
    {
      return $this->getByKey('aidCTH') ? (float)$this->getByKey('aidCTH') : null;
    }

    /**
     * Get the Aircraft Cumulative Total Cycles.
     *
     * @return integer
     */
    public function get_AID_CTY()
    {
      return $this->getByKey('aidCTY') ? (int)$this->getByKey('aidCTY') : null;
    }

    /**
     * |--------------------------------
     * | ENGINE INFORMATION FUNCTIONS
     * |--------------------------------
     */

    /**
     * Get the Aircraft Engine Type.
     *
     * @return string
     */
    public function get_EID_AET()
    {
      $shopFinding = ShopFinding::with('ShopFindingsDetail.AID_Segment')->find($this->get_RCS_SFI());

      // If Aircraft Information Segment is saved, use those values to retrieve engine data.
      if ($shopFinding && $shopFinding->ShopFindingsDetail && $shopFinding->ShopFindingsDetail->AID_Segment) {
        $reg = $shopFinding->ShopFindingsDetail->AID_Segment->get_AID_REG();
        $mfn = $shopFinding->ShopFindingsDetail->AID_Segment->get_AID_MFN();

        // See if there is a unique aircraft record in the Database with this reg number.
        $aircraft = AircraftDetail::where('aircraft_fully_qualified_registration_no', $reg)
          ->where('manufacturer_name', $mfn)
          ->get();

        if (count($aircraft) == 1) {
          return substr($aircraft[0]->engine_type, 0, 20);
        }
      } else {
        $reg = $this->get_AID_REG();
      }

      if ($reg) {
        // See if there is a unique aircraft record in the Database with this reg number.
        $aircraft = AircraftDetail::where('aircraft_fully_qualified_registration_no', $reg)->get();

        if (count($aircraft) == 1) {
          return substr($aircraft[0]->engine_type, 0, 20);
        }
      }

      return (string)$this->getByKey('eidAET');
    }


    /**
     * Summary of get_EID_AETO
     * LJMFeb23 MGTSUP-373 blank edits are breaking because the function doesnt exist so have a blank default.
     * @return string
     */
    public function get_EID_AETO()
    {
      return "";
    }


    /**
     * Get the Engine Position Code.
     *
     * @return string
     */
    public function get_EID_EPC()
    {
      $shopFinding = ShopFinding::with('ShopFindingsDetail.AID_Segment')->find($this->get_RCS_SFI());

      // If Aircraft Information Segment is saved, use those values to retrieve engine data.
      if ($shopFinding && $shopFinding->ShopFindingsDetail && $shopFinding->ShopFindingsDetail->AID_Segment) {
        $reg = $shopFinding->ShopFindingsDetail->AID_Segment->get_AID_REG();
        $mfn = $shopFinding->ShopFindingsDetail->AID_Segment->get_AID_MFN();

        // See if there is a unique aircraft record in the Database with this reg number.
        $aircraft = AircraftDetail::where('aircraft_fully_qualified_registration_no', $reg)
          ->where('manufacturer_name', $mfn)
          ->get();

        if (count($aircraft) == 1) {
          return substr($aircraft[0]->engine_position_identifier, 0, 25);
        }
      } else {
        $reg = $this->get_AID_REG();
      }

      if ($reg) {
        // See if there is a unique aircraft record in the Database with this reg number.
        $aircraft = AircraftDetail::where('aircraft_fully_qualified_registration_no', $reg)->get();

        if (count($aircraft) == 1) {
          return substr($aircraft[0]->engine_position_identifier, 0, 25);
        }
      }

      return (string)$this->getByKey('eidEPC');
    }

    /**
     * Get the Aircraft Engine Model.
     *
     * @return string
     */
    public function get_EID_AEM()
    {
      $shopFinding = ShopFinding::with('ShopFindingsDetail.AID_Segment')->find($this->get_RCS_SFI());

      // If Aircraft Information Segment is saved, use those values to retrieve engine data.
      if ($shopFinding && $shopFinding->ShopFindingsDetail && $shopFinding->ShopFindingsDetail->AID_Segment) {
        $reg = $shopFinding->ShopFindingsDetail->AID_Segment->get_AID_REG();
        $mfn = $shopFinding->ShopFindingsDetail->AID_Segment->get_AID_MFN();

        // See if there is a unique aircraft record in the Database with this reg number.
        $aircraft = AircraftDetail::where('aircraft_fully_qualified_registration_no', $reg)
          ->where('manufacturer_name', $mfn)
          ->get();

        if (count($aircraft) == 1) {
          return substr($aircraft[0]->engines_series, 0, 32);
        }
      } else {
        $reg = $this->get_AID_REG();
      }

      if ($reg) {
        // See if there is a unique aircraft record in the Database with this reg number.
        $aircraft = AircraftDetail::where('aircraft_fully_qualified_registration_no', $reg)->get();

        if (count($aircraft) == 1) {
          return substr($aircraft[0]->engines_series, 0, 32);
        }
      }

      return (string)$this->getByKey('eidAEM');
    }


    /**
     * Summary of get_EID_AEMO
     * LJMFeb23 MGTSUP-373 blank edits are breaking because the function doesnt exist so have a blank default.
     * @return string
     */
    public function get_EID_AEMO()
    {
      return "";
    }

    public function get_EID_LJMFILTERINFO()
    {
      // LJMNDEbug old way       return TFWX_get_EID_LJMFILTERINFO();

      // putting this here because if it is in a single php file to be called from then that causes problems being called both from here HTTP pages and the ARTISAN console app calls.

      //is the value in the array of Engine Types?
      $szReturn = "";


      $tfwxEngineDetails = DB::select(" SELECT * FROM engine_details order by engine_type, engines_series; ");
      foreach ($tfwxEngineDetails as $tfwxEngineDetail) {
        $szReturn = $szReturn . $tfwxEngineDetail->engine_type . ":" . $tfwxEngineDetail->engines_series . ":" . $tfwxEngineDetail->engine_manufacturer_code . "|";
      }
      return $szReturn;
    }


    /**
     * Get the Engine Serial Number.
     *
     * @return string
     */
    public function get_EID_EMS()
    {
      return (string)$this->getByKey('eidEMS');
    }

    /**
     * Get the Aircraft Engine Manufacturer Code.
     *
     * @return string
     */
    public function get_EID_MFR()
    {
      $shopFinding = ShopFinding::with('ShopFindingsDetail.AID_Segment')->find($this->get_RCS_SFI());

      // If Aircraft Information Segment is saved, use those values to retrieve engine data.
      if ($shopFinding && $shopFinding->ShopFindingsDetail && $shopFinding->ShopFindingsDetail->AID_Segment) {
        $reg = $shopFinding->ShopFindingsDetail->AID_Segment->get_AID_REG();
        $mfn = $shopFinding->ShopFindingsDetail->AID_Segment->get_AID_MFN();

        // See if there is a unique aircraft record in the Database with this reg number.
        $aircraft = AircraftDetail::where('aircraft_fully_qualified_registration_no', $reg)
          ->where('manufacturer_name', $mfn)
          ->get();

        if (count($aircraft) == 1) {
          return substr($aircraft[0]->engine_manufacturer_code, 0, 5);
        }
      } else {
        $reg = $this->get_AID_REG();
      }

      if ($reg) {
        // See if there is a unique aircraft record in the Database with this reg number.
        $aircraft = AircraftDetail::where('aircraft_fully_qualified_registration_no', $reg)->get();

        if (count($aircraft) == 1) {
          return substr($aircraft[0]->engine_manufacturer_code, 0, 5);
        }
      }

      // Create black list of codes but remove 'ZZZZZ' and 'zzzzz'.
      $cageCodeBlackList = CageCode::getPermittedValues();

      foreach ($cageCodeBlackList as $key => $val) {
        if (in_array($val, ['zzzzz', 'ZZZZZ'])) {
          unset($cageCodeBlackList[$key]);
        }
      }

      if (!in_array((string)$this->getByKey('eidMFR'), $cageCodeBlackList)) {
        return (string)$this->getByKey('eidMFR');
      }

      return null;
    }

    /**
     * Get the Engine Cumulative Hours.
     *
     * @return float
     */
    public function get_EID_ETH()
    {
      return $this->getByKey('eidETH') ? (float)$this->getByKey('eidETH') : null;
    }

    /**
     * Get the Engine Cumulative Cycles.
     *
     * @return integer
     */
    public function get_EID_ETC()
    {
      return $this->getByKey('eidETC') ? (int)$this->getByKey('eidETC') : null;
    }

    /**
     * |--------------------------------
     * | APU INFORMATION FUNCTIONS
     * |--------------------------------
     */

    /**
     * Get the Aircraft APU Type.
     *
     * @return string
     */
    public function get_API_AET()
    {
      return (string)$this->getByKey('apiAET');
    }

    /**
     * Get the APU Serial Number.
     *
     * @return string
     */
    public function get_API_EMS()
    {
      return (string)$this->getByKey('apiEMS');
    }

    /**
     * Get the Aircraft APU Model.
     *
     * @return string
     */
    public function get_API_AEM()
    {
      return (string)$this->getByKey('apiAEM');
    }

    /**
     * Get the Aircraft Engine Manufacturer Code.
     *
     * @return string
     */
    public function get_API_MFR()
    {
      // Quick fix to correct bad SAP data missing leading zero.
      if ($this->getByKey('apiMFR') == '5167') {
        return (string)'05167';
      }

      if (in_array((string)$this->getByKey('apiMFR'), CageCode::getPermittedValues())) {
        return (string)$this->getByKey('apiMFR');
      }

      return null;
    }

    /**
     * Get the APU Cumulative Hours.
     *
     * @return float
     */
    public function get_API_ATH()
    {
      return $this->getByKey('apiATH') ? (float)$this->getByKey('apiATH') : null;
    }

    /**
     * Get the APU Cumulative Cycles.
     *
     * @return integer
     */
    public function get_API_ATC()
    {
      return $this->getByKey('apiATC') ? (int)$this->getByKey('apiATC') : null;
    }

    /**
     * |--------------------------------
     * | RECEIVED LRU FUNCTIONS
     * |--------------------------------
     */

    /**
     * Get the Shop Findings Record Identifier.
     *
     * @return string
     */
    public function get_RCS_SFI()
    {
      return $this->rcsSFI;
    }

    /**
     * Get the Shop Received Date .
     *
     * @return date
     */
    public function get_RCS_MRD()
    {
      return $this->rcsMRD ? $this->rcsMRD->format('d/m/Y') : null;
    }

    /**
     * Get the Received Part Manufacturer Code.
     *
     * @return string
     */
    public function get_RCS_MFR()
    {
      // Quick fix to correct bad SAP data missing leading zero.
      if ($this->getByKey('rcsMFR') == '5167') {
        return (string)'05167';
      }

      if (in_array((string)$this->getByKey('rcsMFR'), CageCode::getPermittedValues())) {
        return (string)$this->getByKey('rcsMFR');
      }

      return null;
    }

    /**
     * Get the Received Manufacturer Full Length Part Number.
     *
     * @return string
     */
    public function get_RCS_MPN()
    {
      return (string)$this->getByKey('rcsMPN');
    }

    /**
     * Get the Received Manufacturer Serial Number.
     *
     * @return string
     */
    public function get_RCS_SER()
    {
      return (string)$this->getByKey('rcsSER');
    }

    /**
     * Get the Supplier Removal Type Code.
     *
     * @return string
     */
    public function get_RCS_RRC()
    {
      return (string)$this->getByKey('rcsRRC');
    }

    /**
     * Get the Failure/Fault Found.
     *
     * @return string
     */
    public function get_RCS_FFC()
    {
      return (string)$this->getByKey('rcsFFC');
    }

    /**
     * Get the Failure/Fault Induced.
     *
     * @return string
     */
    public function get_RCS_FFI()
    {
      return (string)$this->getByKey('rcsFFI');
    }

    /**
     * Get the Failure/Fault Confirms Reason For Removal.
     *
     * @return string
     */
    public function get_RCS_FCR()
    {
      return (string)$this->getByKey('rcsFCR');
    }

    /**
     * Get the Failure/Fault Confirms Aircraft Message.
     *
     * @return string
     */
    public function get_RCS_FAC()
    {
      return (string)$this->getByKey('rcsFAC');
    }

    /**
     * Get the Failure/Fault Confirms Aircraft Part Bite Message.
     *
     * @return string
     */
    public function get_RCS_FBC()
    {
      return (string)$this->getByKey('rcsFBC');
    }

    /**
     * Get the Hardware/Software Failure.
     *
     * @return string
     */
    public function get_RCS_FHS()
    {
      return (string)$this->getByKey('rcsFHS');
    }

    /**
     * Get the Removed Part Manufacturer Name.
     *
     * @return string
     */
    public function get_RCS_MFN()
    {
      return (string)$this->getByKey('rcsMFN');
    }

    /**
     * Get the Received Manufacturer Part Number.
     *
     * @return string
     */
    public function get_RCS_PNR()
    {
      return (string)$this->getByKey('rcsPNR');
    }

    /**
     * Get the Overlength Part Number.
     *
     * @return string
     */
    public function get_RCS_OPN()
    {
      return (string)$this->getByKey('rcsOPN');
    }

    /**
     * Get the Removed Universal Serial Number.
     *
     * @return string
     */
    public function get_RCS_USN()
    {
      return (string)$this->getByKey('rcsUSN');
    }

    /**
     * Get the Supplier Removal Type Text.
     *
     * @return string
     */
    public function get_RCS_RET()
    {
      return (string)$this->getByKey('rcsRET');
    }

    /**
     * Get the Customer Code.
     *
     * @return string
     */
    public function get_RCS_CIC()
    {
      return (string)$this->getByKey('rcsCIC');
    }

    /**
     * Get the Repair Order Identifier.
     *
     * @return string
     */
    public function get_RCS_CPO()
    {
      return (string)$this->getByKey('rcsCPO');
    }

    /**
     * Get the Packing Sheet Number.
     *
     * @return string
     */
    public function get_RCS_PSN()
    {
      return (string)$this->getByKey('rcsPSN');
    }

    /**
     * Get the Work Order Number.
     *
     * @return string
     */
    public function get_RCS_WON()
    {
      return (string)$this->getByKey('rcsWON');
    }

    /**
     * Get the Maintenance Release Authorization Number.
     *
     * @return string
     */
    public function get_RCS_MRN()
    {
      return (string)$this->getByKey('rcsMRN');
    }

    /**
     * Get the Contract Number.
     *
     * @return string
     */
    public function get_RCS_CTN()
    {
      return (string)$this->getByKey('rcsCTN');
    }

    /**
     * Get the Master Carton Number.
     *
     * @return string
     */
    public function get_RCS_BOX()
    {
      return (string)$this->getByKey('rcsBOX');
    }

    /**
     * Get the Received Operator Part Number.
     *
     * @return string
     */
    public function get_RCS_ASN()
    {
      return (string)$this->getByKey('rcsASN');
    }

    /**
     * Get the Received Operator Serial Number.
     *
     * @return string
     */
    public function get_RCS_UCN()
    {
      return (string)$this->getByKey('rcsUCN');
    }

    /**
     * Get the Supplier Code.
     *
     * @return string
     */
    public function get_RCS_SPL()
    {
      return (string)$this->getByKey('rcsSPL');
    }

    /**
     * Get the Removed Universal Serial Tracking Number.
     *
     * @return string
     */
    public function get_RCS_UST()
    {
      return (string)$this->getByKey('rcsUST');
    }

    /**
     * Get the Manufacturer Part Description.
     *
     * @return string
     */
    public function get_RCS_PDT()
    {
      return (string)$this->getByKey('rcsPDT');
    }

    /**
     * Get the Removed Part Modificiation Level.
     *
     * @return string
     */
    public function get_RCS_PML()
    {
      return (string)$this->getByKey('rcsPML');
    }

    /**
     * Get the Shop Findings Code.
     *
     * @return string
     */
    public function get_RCS_SFC()
    {
      return (string)$this->getByKey('rcsSFC');
    }

    /**
     * Get the Related Shop Finding Record Identifier.
     *
     * @return string
     */
    public function get_RCS_RSI()
    {
      return (string)$this->getByKey('rcsRSI');
    }

    /**
     * Get the Repair Location Name.
     *
     * @return string
     */
    public function get_RCS_RLN()
    {
      return (string)$this->getByKey('rcsRLN');
    }

    /**
     * Get the Incoming Inspection Text.
     *
     * @return string
     */
    public function get_RCS_INT()
    {
      return (string)$this->getByKey('rcsINT');
    }

    /**
     * Get the Comment Text.
     *
     * @return string
     */
    public function get_RCS_REM()
    {
      return (string)$this->getByKey('rcsREM');
    }

    /**
     * |--------------------------------
     * | SHOP ACTION DETAILS FUNCTIONS
     * |--------------------------------
     */

    /**
     * Get the Shop Action Text Incoming.
     *
     * @return string
     */
    public function get_SAS_INT()
    {
      return (string)$this->getByKey('sasINT');
    }

    /**
     * Get the Shop Repair Location Code.
     *
     * @return string
     */
    public function get_SAS_SHL()
    {
      return (string)$this->getByKey('sasSHL');
    }

    /**
     * Get the Shop Final Action Indicator.
     *
     * @return boolean
     */
    public function get_SAS_RFI()
    {
      return $this->getByKey('sasRFI');
    }

    /**
     * Get the Mod (S) Incorporated (This Visit) Text.
     *
     * @return string
     */
    public function get_SAS_MAT()
    {
      return (string)$this->getByKey('sasMAT');
    }

    /**
     * Get the Shop Action Code.
     *
     * @return string
     */
    public function get_SAS_SAC()
    {
      return (string)$this->getByKey('sasSAC');
    }

    /**
     * Get the Shop Disclosure Indicator.
     *
     * @return boolean
     */
    public function get_SAS_SDI()
    {
      return $this->getByKey('sasSDI');
    }

    /**
     * Get the Part Status Code.
     *
     * @return string
     */
    public function get_SAS_PSC()
    {
      // These are the codes used by SAP.
      $partStatusCodes = [
        'AI' => 'As Is',
        'AL' => 'Altered',
        'EX' => 'Export',
        'IN' => 'Inspected',
        'MF' => 'Manufactured',
        'MO' => 'Modified',
        'NS' => 'New Surplus',
        'NW' => 'New',
        'OV' => 'Overhauled',
        'PR' => 'Prototype',
        'RB' => 'Rebuilt',
        'RE' => 'Reassembled',
        'RP' => 'Repaired',
        'RT' => 'Retreaded',
        'SV' => 'Serviceable',
        'TS' => 'Tested',
        'UN' => 'Unserviceable',
      ];

      $key = (string)$this->getByKey('sasPSC');

      if (array_key_exists($key, $partStatusCodes)) {
        return $partStatusCodes[$key];
      } else {
        if (array_key_exists($key, PartStatusCode::getDropDownValues(false))) {
          return $key;
        } else {
          if (array_key_exists(strtoupper($key), PartStatusCode::getDropDownValues(false))) {
            return strtoupper($key);
          }
        }
      }

      return null;
    }

    /**
     * Get the Comment Text.
     *
     * @return string
     */
    public function get_SAS_REM()
    {
      return (string)$this->getByKey('sasREM');
    }

    /**
     * |--------------------------------
     * | SHIPPED LRU FUNCTIONS
     * |--------------------------------
     */

    /**
     * Get the Shipped Date.
     *
     * @return date
     */
    public function get_SUS_SHD()
    {
      return $this->susSHD ? $this->susSHD->format('d/m/Y') : null;
    }

    /**
     * Get the Shipped Part Manufacturer Code.
     *
     * @return string
     */
    public function get_SUS_MFR()
    {
      // Quick fix to correct bad SAP data missing leading zero.
      if ($this->getByKey('susMFR') == '5167') {
        return (string)'05167';
      }

      if (in_array((string)$this->getByKey('susMFR'), CageCode::getPermittedValues())) {
        return (string)$this->getByKey('susMFR');
      }

      return null;
    }

    /**
     * Get the Shipped Manufacturer Full Length Part Number.
     *
     * @return string
     */
    public function get_SUS_MPN()
    {
      return (string)$this->getByKey('susMPN');
    }

    /**
     * Get the Shipped Manufacturer Serial Number.
     *
     * @return string
     */
    public function get_SUS_SER()
    {
      return (string)$this->getByKey('susSER');
    }

    /**
     * Get the Shipped Part Manufacturer Name.
     *
     * @return string
     */
    public function get_SUS_MFN()
    {
      return (string)$this->getByKey('susMFN');
    }

    /**
     * Get the Shipped Manufacturer Part Description.
     *
     * @return string
     */
    public function get_SUS_PDT()
    {
      return (string)$this->getByKey('susPDT');
    }

    /**
     * Get the Shipped Manufacturer Part Number.
     *
     * @return string
     */
    public function get_SUS_PNR()
    {
      return (string)$this->getByKey('susPNR');
    }

    /**
     * Get the Overlength Part Number.
     *
     * @return string
     */
    public function get_SUS_OPN()
    {
      return (string)$this->getByKey('susOPN');
    }

    /**
     * Get the Shipped Universal Serial Number.
     *
     * @return string
     */
    public function get_SUS_USN()
    {
      return (string)$this->getByKey('susUSN');
    }

    /**
     * Get the Shipped Operator Part Number.
     *
     * @return string
     */
    public function get_SUS_ASN()
    {
      return (string)$this->getByKey('susASN');
    }

    /**
     * Get the Shipped Operator Serial Number.
     *
     * @return string
     */
    public function get_SUS_UCN()
    {
      return (string)$this->getByKey('susUCN');
    }

    /**
     * Get the Supplier Code.
     *
     * @return string
     */
    public function get_SUS_SPL()
    {
      return (string)$this->getByKey('susSPL');
    }

    /**
     * Get the Shipped Universal Serial Tracking Number.
     *
     * @return string
     */
    public function get_SUS_UST()
    {
      return (string)$this->getByKey('susUST');
    }

    /**
     * Get the Shipped Part Modification Level.
     *
     * @return string
     */
    public function get_SUS_PML()
    {
      return (string)$this->getByKey('susPML');
    }

    /**
     * Get the Shipped Part Status Code.
     *
     * @return string
     */
    public function get_SUS_PSC()
    {
      return (string)$this->getByKey('susPSC');
    }

    /**
     * |--------------------------------
     * | REMOVED LRU FUNCTIONS
     * |--------------------------------
     */

    /**
     * Get the Removed Part Manufacturer Code.
     *
     * @return string
     */
    public function get_RLS_MFR()
    {
      // Quick fix to correct bad SAP data missing leading zero.
      if ($this->getByKey('rlsMFR') == '5167') {
        return (string)'05167';
      }

      if (in_array((string)$this->getByKey('rlsMFR'), CageCode::getPermittedValues())) {
        return (string)$this->getByKey('rlsMFR');
      }

      return null;
    }

    /**
     * Get the Removed Manufacturer Full Length Part Number.
     *
     * @return string
     */
    public function get_RLS_MPN()
    {
      return (string)$this->getByKey('rlsMPN');
    }

    /**
     * Get the Removed Manufacturer Serial Number.
     *
     * @return string
     */
    public function get_RLS_SER()
    {
      return (string)$this->getByKey('rlsSER');
    }

    /**
     * Get the Removal Date.
     *
     * @return date
     */
    public function get_RLS_RED()
    {
      return $this->rlsRED ? $this->rlsRED->format('d/m/Y') : null;
    }

    /**
     * Get the Removal Type Code.
     *
     * @return string
     */
    public function get_RLS_TTY()
    {
      return (string)$this->getByKey('rlsTTY');
    }

    /**
     * Get the Removal Type Text.
     *
     * @return string
     */
    public function get_RLS_RET()
    {
      return (string)$this->getByKey('rlsRET');
    }

    /**
     * Get the Install Date of Removed Part.
     *
     * @return date
     */
    public function get_RLS_DOI()
    {
      return $this->rlsDOI ? $this->rlsDOI->format('d/m/Y') : null;
    }

    /**
     * Get the Removed Part Manufacturer Name.
     *
     * @return string
     */
    public function get_RLS_MFN()
    {
      return (string)$this->getByKey('rlsMFN');
    }

    /**
     * Get the Removed Manufacturer Part Number.
     *
     * @return string
     */
    public function get_RLS_PNR()
    {
      return (string)$this->getByKey('rlsPNR');
    }

    /**
     * Get the Overlength Part Number.
     *
     * @return string
     */
    public function get_RLS_OPN()
    {
      return (string)$this->getByKey('rlsOPN');
    }

    /**
     * Get the Removed Universal Serial Number.
     *
     * @return string
     */
    public function get_RLS_USN()
    {
      return (string)$this->getByKey('rlsUSN');
    }

    /**
     * Get the Removal Reason Text.
     *
     * @return string
     */
    public function get_RLS_RMT()
    {
      return (string)$this->getByKey('rlsRMT');
    }

    /**
     * Get the Engine/APU Position Identifier.
     *
     * @return string
     */
    public function get_RLS_APT()
    {
      return (string)$this->getByKey('rlsAPT');
    }

    /**
     * Get the Part Position Code.
     *
     * @return string
     */
    public function get_RLS_CPI()
    {
      return (string)$this->getByKey('rlsCPI');
    }

    /**
     * Get the Part Position.
     *
     * @return string
     */
    public function get_RLS_CPT()
    {
      return (string)$this->getByKey('rlsCPT');
    }

    /**
     * Get the Removed Part Description.
     *
     * @return string
     */
    public function get_RLS_PDT()
    {
      return (string)$this->getByKey('rlsPDT');
    }

    /**
     * Get the Removed Part Modification Level.
     *
     * @return string
     */
    public function get_RLS_PML()
    {
      return (string)$this->getByKey('rlsPML');
    }

    /**
     * Get the Removed Operator Part Number.
     *
     * @return string
     */
    public function get_RLS_ASN()
    {
      return (string)$this->getByKey('rlsASN');
    }

    /**
     * Get the Removed Operator Serial Number.
     *
     * @return string
     */
    public function get_RLS_UCN()
    {
      return (string)$this->getByKey('rlsUCN');
    }

    /**
     * Get the Supplier Code.
     *
     * @return string
     */
    public function get_RLS_SPL()
    {
      return (string)$this->getByKey('rlsSPL');
    }

    /**
     * Get the Removed Universal Serial Tracking Number.
     *
     * @return string
     */
    public function get_RLS_UST()
    {
      return (string)$this->getByKey('rlsUST');
    }

    /**
     * Get the Removal Reason Code.
     *
     * @return string
     */
    public function get_RLS_RFR()
    {
      return (string)$this->getByKey('rlsRFR');
    }

    /**
     * |--------------------------------
     * | LINKING FIELDS FUNCTIONS
     * |--------------------------------
     */

    /**
     * Get the Removal Tracking Identifier.
     *
     * @return string
     */
    public function get_LNK_RTI()
    {
      return (string)$this->getByKey('lnkRTI');
    }

    /**
     * |--------------------------------
     * | ACCUMULATED TIME FUNCTIONS
     * |--------------------------------
     */

    /**
     * Get the Time/Cycle Reference Code.
     *
     * @return string
     */
    public function get_ATT_TRF()
    {
      return (string)$this->getByKey('attTRF');
    }

    /**
     * Get the Operating Time.
     *
     * @return integer
     */
    public function get_ATT_OTT()
    {
      return $this->getByKey('attOTT') ? (int)$this->getByKey('attOTT') : null;
    }

    /**
     * Get the Operating Cycle Count.
     *
     * @return integer
     */
    public function get_ATT_OPC()
    {
      return $this->getByKey('attOPC') ? (int)$this->getByKey('attOPC') : null;
    }

    /**
     * Get the Operating Day Count.
     *
     * @return integer
     */
    public function get_ATT_ODT()
    {
      return $this->getByKey('attODT') ? (int)$this->getByKey('attODT') : null;
    }

    /**
     * |--------------------------------
     * | SHOP PROCESSING FUNCTIONS
     * |--------------------------------
     */

    /**
     * Get the Shop Total Labor Hours.
     *
     * @return float
     */
    public function get_SPT_MAH()
    {
      return $this->getByKey('sptMAH') ? (float)$this->getByKey('sptMAH') : null;
    }

    /**
     * Get the Shop Flow Time.
     *
     * @return integer
     */
    public function get_SPT_FLW()
    {
      return $this->getByKey('sptFLW') ? (int)$this->getByKey('sptFLW') : null;
    }

    /**
     * Get the Shop Turn Around Time.
     *
     * @return integer
     */
    public function get_SPT_MST()
    {
      return $this->getByKey('sptMST') ? (int)$this->getByKey('sptMST') : null;
    }

    /**
     * |--------------------------------
     * | MISC SEGMENT FUNCTIONS
     * |--------------------------------
     */

    /**
     * Get value from json string.
     *
     * @param (type) $name
     * @param (array) $arguments
     * @return mixed
     */
    public function __call($name, $arguments)
    {
      if (stristr($name, 'get_MISC_')) {
        return null;
      }

      return parent::__call($name, $arguments);
    }

    /**
     * Update the notification from middleware api data.
     *
     * @params \stdClass $data
     * @return void
     */
    public static function updateFromMiddleware($data)
    {
      $notification = self::withTrashed()->find($data->id);

      $attributeKeys = array_keys($notification->getAttributes());

      if ($notification) {
        foreach ($data as $key => $value) {
          if ($key && in_array($key, $attributeKeys) && !empty($value)) {
            // Format the dates correctly.
            if (in_array($key, $notification->getDates())) {
              if ((stristr($value, '-') === false) && intval($value)) {
                $notification->{$key} = Carbon::createFromFormat('Ymd', $value);
              } else {
                if ($value != '0000-00-00') {
                  $notification->{$key} = Carbon::createFromFormat('Y-m-d', $value);
                }
              }
            } else {
              if (TFWXSpec2KCommon::IsCollins($notification->id)) {
                if ($key == 'rcsREM') { // is notification Collins?
                  Log::info(
                    'MGTSUP-974 : Setting Collins Notification ' . $notification->id . ' rlsRMT before the set was: ' . $notification->rlsRMT,
                  );
                  Log::info(
                    'MGTSUP-974 : Setting Collins Notification ' . $notification->id . ' rcsREM before the set was: ' . $notification->rcsREM,
                  );
                  $notification->rlsRMT = $value;
                  Log::info(
                    'MGTSUP-974 : Setting Collins Notification ' . $notification->id . ' rlsRMT to the rcsREM value = ' . $value,
                  );
                } else {
                  if ($key == 'sasINT') { // is notification Collins?
                    // if this is a UTAS/Collins part then we set the sasREM to this value instead.
                    Log::info(
                      'MGTSUP-974 : Setting Collins Notification ' . $notification->id . ' sasREM before the set was: ' . $notification->sasREM,
                    );
                    Log::info(
                      'MGTSUP-974 : Setting Collins Notification ' . $notification->id . ' sasINT before the set was: ' . $notification->sasINT,
                    );
                    $notification->sasREM = $value;
                    Log::info(
                      'MGTSUP-974 : Setting Collins Notification ' . $notification->id . ' sasREM to the sasINT value = ' . $value,
                    );
                  } else {// act as normal.
                    $notification->{$key} = str_replace(['\r\n', '\n', '\r'], "\n", $value); // Preserve new lines.
                  }
                }
              } else {// act as normal
                // MGTSUP-974 log here if we are updating a dodgy notification and if it isnt a collins one.
                if ($key == 'rcsREM') {
                  Log::info(
                    'MGTSUP-974 Setting NON Collins Notification Updating rcsREM from SAP id= : ' . $notification->id . ' prev rcsREM = ' . $notification->rcsREM . ' & prev rlsRMT = ' . $notification->rlsRMT,
                  );
                } else {
                  if ($key == 'sasINT') {
                    Log::info(
                      'MGTSUP-974 Setting NON Collins Notification Updating sasINT from SAP id= : ' . $notification->id . ' prev sasINT = ' . $notification->sasINT,
                    );
                  }
                }
                $notification->{$key} = str_replace(['\r\n', '\n', '\r'], "\n", $value); // Preserve new lines.
              }
            }

            if (($key == 'status') && isset($value)) {
              $notification->{$key} = str_replace(
                ' ',
                '_',
                $value,
              ); // Fix bug in SAP middleware that provides status names with space instead of underscore.
            }

            // Get cage code data from locations table.
            if (($key == 'hdrROC') && isset($value) && isset($data->plant_code)) {
              $notification->{$key} = Location::getFirstCageCode($data->plant_code);
            }

            // Get repair station name from locations table.
            if (($key == 'hdrRON') && isset($value) && isset($data->plant_code)) {
              $notification->{$key} = Location::getReportingOrganisationName($data->plant_code);
            }

            // Save the aircraft details.
            if (($key == 'rcsREM') && isset($value)) {
              // Get array of strings between @@ tags.
              preg_match_all('/@@(.*?)@@/', $value, $match);

              if (!empty($match[1])) {
                // Get last occurrence of Aircraft Reg No.
                $reg = end($match[1]);

                $notification->aidREG = substr($reg, 0, 10);

                // See if there is a unique aircraft record in the Database with this reg number.
                $aircraft = AircraftDetail::where('aircraft_fully_qualified_registration_no', $reg)->get();

                if (count($aircraft) == 1) {
                  $notification->aidAIN = substr($aircraft[0]->aircraft_identification_no, 0, 10);
                  $notification->aidAMC = substr($aircraft[0]->aircraft_model_identifier, 0, 20);
                  $notification->aidASE = substr($aircraft[0]->aircraft_series_identifier, 0, 10);
                  $notification->aidMFN = substr($aircraft[0]->manufacturer_name, 0, 55);
                  $notification->aidMFR = substr($aircraft[0]->manufacturer_code, 0, 5);

                  // Add Engine Information.
                  $notification->eidAET = substr($aircraft[0]->engine_type, 0, 20);
                  $notification->eidAEM = substr($aircraft[0]->engines_series, 0, 32);
                }
              }
            }
          }
        }

        $notification->save();

        // Sync statuses, validation and planner groups of shop findings.
        Artisan::call('spec2kapp:sync_shopfindings', ['shopfindingIds' => [$data->id]]);
      }
    }
  }

