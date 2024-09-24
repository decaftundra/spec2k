<?php

namespace App;

use App\Codes\Airline;
use App\Interfaces\HDR_SegmentInterface;
use App\Location;
use App\Segment;
use App\ShopFindings\ShopFinding;
use Carbon\Carbon;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Validator;


class HDR_Segment extends Segment implements HDR_SegmentInterface
{
    /*
    |-------------------------------------------------------------------------------------------------------------------------|
    | HDR - Header                                                                                                            |
    |-------------------------------------------------------------------------------------------------------------------------|
    | CHG | Record Status                   | Change Code                     | Y   | String  | 1/1         | N               |
	| ROC | Reporting Organization Code     | Reporting Organization Code     | Y   | String  | 3/5         | 58960           |
	| RDT | Reporting Period Start Date     | Reporting Period Date           | Y   | Date    | 2001-07-01  |                 |
	| RSD | Reporting Period End Date       | Reporting Period End Date       | Y   | Date    | 2001-07-31  |                 |
	| OPR | Operator Code                   | Operator Code                   | Y   | String  | 3/5         | UAL             |
	| RON | Reporting Organization Name     | Reporting Organization Name     | N   | String  | 1/55        | Honeywell       |
	| WHO | Operator Name                   | Company Name                    | N   | String  | 1/55        | United Airlines |
	|-------------------------------------------------------------------------------------------------------------------------|
    */
    
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'HDR_Segments';
    
    /**
     * All of the relationships to be touched.
     *
     * @var array
     */
    protected $touches = ['ShopFinding', 'PiecePart'];
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['shop_finding_id', 'piece_part_id'];
    
    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = [
        'created_at',
        'updated_at',
        'RDT',
        'RSD'
    ];
    
    /**
     * Delimiter for export page locations drop down.
     *
     * @const string
     */
    const DELIMITER = '|';
    
    /**
     * Get the shop finding record associated with the header.
     */
    public function ShopFinding()
    {
        return $this->belongsTo('App\ShopFindings\ShopFinding');
    }
    
    /**
     * Get the piece part record associated with the header.
     */
    public function PiecePart()
    {
        return $this->belongsTo('App\PieceParts\PiecePart');
    }
    
    /**
     * Get the segment function prefix.
     *
     * @return string
     */
    public function getPrefix()
    {
        return 'get_HDR_';
    }
    
    /**
     * Get an array of segment keys.
     *
     * @return array
     */
    public static function getKeys()
    {
        return [
            'CHG',
        	'ROC',
        	'RDT',
        	'RSD',
        	'OPR',
        	'RON',
        	'WHO'
        ];
    }
    
    /**
     * Get the url for the activities listing page.
     *
     * @return string
     */
    public function getActivityUrl()
    {
        return route('header.edit', $this->getShopFindingId());
    }
    
    /**
     * Get the url title for the activities page.
     *
     * @return string
     */
    public function getActivityUrlTitle()
    {
        return 'View Header Segment';
    }
    
    public static function isMandatory($id)
    {
        $profiler = new ValidationProfiler('HDR_Segment', (new static), $id);
        
        return $profiler->isMandatory();
    }
    
    public static function isValid($id)
    {
        $shopFinding = ShopFinding::with('HDR_Segment')->find($id);
        
        if (!$shopFinding || !$shopFinding->HDR_Segment) return NULL;
        
        $model = $shopFinding->HDR_Segment;
        
        return $model->getIsValid();
    }
    
    public function validate()
    {
        $shopFindingId = $this->getShopFindingId();
        $modelArray = $this->getTreatedAttributes();
        
        $profiler = new ValidationProfiler('HDR_Segment', $this, $shopFindingId);
        
        $validator = Validator::make($modelArray, $profiler->getValidationRules($shopFindingId), $profiler->getValidationMessages(), $profiler->getFormAttributes());
        
        // Add any conditional validation.
        $validatedConditionally = $profiler->conditionalValidation($validator);
        
        if ($validatedConditionally->fails()) {
            $this->validationErrors = $validatedConditionally->errors()->all();
            return false;
        }
        
        return true;
    }
    
    public function getIdentifier()
    {
        return $this->shop_finding_id;
    }
    
    public function getShopFindingId()
    {
        return $this->shop_finding_id;
    }
    
    /**
     * Create or update the segment.
     *
     * @param (array) $data
     * @param (string) $shopFindingId
     * @return void
     */
    public static function createOrUpdateSegment(array $data, string $shopFindingId, $autosave = null)
    {
        $header = HDR_Segment::firstOrNew(['shop_finding_id' => $shopFindingId]);
        $header->CHG = isset($data['CHG']) ? $data['CHG'] : NULL;
        $header->ROC = isset($data['ROC']) ? $data['ROC'] : NULL;
        $header->RON = isset($data['RON']) ? $data['RON'] : NULL;
        $header->OPR = isset($data['OPR']) ? $data['OPR'] : NULL;
        $header->WHO = isset($data['WHO']) ? $data['WHO'] : NULL;
        $header->autosaved_at = $autosave ? Carbon::now() : NULL;
        $header->save();
    }
    
    /**
     * Get the Change Code.
     *
     * @return string
     */
    public function get_HDR_CHG()
    {
        return mb_strlen(trim($this->CHG)) ? (string) trim($this->CHG) : NULL;
    }
    
    /**
     * Get the Reporting Organisation Name.
     *
     * @return string
     */
    public function get_HDR_RON()
    {
        $name = (string) $this->RON;
        
        $location = Location::where('name', $name)->first();
        
        return $location ? $location->name : (string) trim($this->RON);
    }
    
    /**
     * Get the Reporting Organisation Cage Code.
     *
     * @return string
     */
    public function get_HDR_ROC()
    {
        return mb_strlen(trim($this->ROC)) ? (string) strtoupper(trim($this->ROC)) : NULL;
    }
    
    /**
     * Get the Operator Code.
     *
     * @return string
     */
    public function get_HDR_OPR()
    {
        return mb_strlen(trim($this->OPR)) ? (string) strtoupper(trim($this->OPR)) : NULL;
    }
    
    /**
     * Get the Operator Name.
     *
     * @return string
     */
    public function get_HDR_WHO()
    {
        return mb_strlen(trim($this->WHO)) ? (string) trim($this->WHO) : NULL;
    }
    
    /**
     * Get the Reporting Period Start Date.
     *
     * @return date
     */
    public function get_HDR_RDT()
    {
        return $this->RDT ? (string) $this->RDT->format('d/m/Y') : NULL;
    }
    
    /**
     * Get the Reporting Period End Date.
     *
     * @return date
     */
    public function get_HDR_RSD()
    {
        return $this->RSD ? (string) $this->RSD->format('d/m/Y') : NULL;
    }
}
