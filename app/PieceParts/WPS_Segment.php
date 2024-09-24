<?php

namespace App\PieceParts;

use App\Codes\PrimaryPiecePartFailureIndicator;
use App\Interfaces\WPS_SegmentInterface;
use App\NotificationPiecePart;
use App\PieceParts\PiecePartDetail;
use App\PieceParts\PiecePartSegment;
use App\ValidationProfiler;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class WPS_Segment extends PiecePartSegment implements WPS_SegmentInterface
{
    /*
    |---------------------------------------------------------------------------------------------------------------------------------------------|
    | WPS = Worked Piece Part                                                                                                                     |
    |---------------------------------------------------------------------------------------------------------------------------------------------|
	| SFI | Shop Finding Record Identifier                          | Shop Findings Record Identifier         | Y   | String  | 1/50          |   |
	| PPI | Piece Part Record Identifier                            | Piece Part Record Identifier            | Y   | String  | 1/50          |   |
	| PFC | Primary Piece Part Failure Indicator                    | Primary Piece Part Failure Indicator    | Y   | String  | 1/1           | Y |
	| MFR | Failed Piece Part Vendor Code                           | Manufacturer Code                       | N   | String  | 5/5           |   |
	| MFN | Failed Piece Part Vendor Name                           | Manufacturer Name                       | N   | String  | 1/55          |   |
	| MPN | Failed Piece Part Manufacturer Full Length Part Number  | Manufacturer Full Length Part Number    | N   | String  | 1/32          |   |
	| SER | Failed Piece Part Serial Number                         | Part Serial Number                      | N   | String  | 1/15          |   |
	| FDE | Piece Part Failure Description                          | Piece Part Failure Description          | N   | String  | 1/1000        |   |
	| PNR | Vendor Piece Part Number                                | Part Number                             | N   | String  | 1/15          |   |
	| OPN | Overlength Part Number                                  | Overlength Part Number                  | N   | String  | 16/32         |   |
	| USN | Piece Part Universal Serial Number                      | Universal Serial Number                 | N   | String  | 6/20          |   |
	| PDT | Failed Piece Part Description                           | Part Description                        | N   | String  | 1/100         |   |
	| GEL | Piece Part Reference Designator Symbol                  | Geographic and/or Electrical Location   | N   | String  | 1/30          |   |
	| MRD | Received Date                                           | Material Receipt Date                   | N   | Date    | YYYY-MM-DD    |   |
	| ASN | Operator Piece Part Number                              | Airline Stock Number                    | N   | String  | 1/32          |   |
	| UCN | Operator Piece Part Serial Number                       | Unique Component Identification Number  | N   | String  | 1/15          |   |
	| SPL | Supplier Code                                           | Supplier Code                           | N   | String  | 5/5           |   |
	| UST | Piece Part Universal Serial Tracking Number             | Universal Serial Tracking Number        | N   | String  | 6/20          |   |
	|---------------------------------------------------------------------------------------------------------------------------------------------|
	*/
	
	use SoftDeletes;
    
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'WPS_Segments';
    
    protected $primaryKey = 'PPI';
    public $incrementing = false;
    
    /**
     * The "type" of the primary key ID.
     *
     * @var string
     */
    protected $keyType = 'string';
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['piece_part_detail_id', 'PPI'];
    
    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
        'MRD'
    ];
    
    /**
     * Get the segment function prefix.
     *
     * @return string
     */
    public function getPrefix()
    {
        return 'get_WPS_';
    }
    
    /**
     * Get an array of segment keys.
     *
     * @return array
     */
    public static function getKeys()
    {
        return [
            'SFI',
        	'PPI',
        	'PFC',
        	'MFR',
        	'MFN',
        	'MPN',
        	'SER',
        	'FDE',
        	'PNR',
        	'OPN',
        	'USN',
        	'PDT',
        	'GEL',
        	'MRD',
        	'ASN',
        	'UCN',
        	'SPL',
        	'UST'
        ];
    }
    
    /**
     * Get the url for the activities listing page.
     *
     * @return string
     */
    public function getActivityUrl()
    {
        return route('worked-piece-part.edit', [$this->getShopFindingId(), $this->getPiecePartDetailId()]);
    }
    
    /**
     * Get the url title for the activities page.
     *
     * @return string
     */
    public function getActivityUrlTitle()
    {
        return 'View Worked Piece Part Segment';
    }
    
    /**
     * Is the segment mandatory.
     * NOTE: The $id parameter is the Piece Part Detail ID not the Shop Finding ID.
     *
     * @param (string) $id
     * @return
     */
    public static function isMandatory($id)
    {
        $piecePartDetail = PiecePartDetail::with(['PiecePart', 'WPS_Segment'])->find($id);
        
        if (is_null($piecePartDetail) || is_null($piecePartDetail->WPS_Segment)) {
            $shopFindingId = NotificationPiecePart::find($id)->notification_id;
        } else {
            $shopFindingId = $piecePartDetail->PiecePart->shop_finding_id;
        }
        
        $profiler = new ValidationProfiler('WPS_Segment', (new static), $shopFindingId);
        
        return $profiler->isMandatory();
    }
    
    /**
     * Get the primary key of the WPS_Segment.
     *
     * @return integer
     */
    public function getIdAttribute()
    {
        return $this->PPI;
    }
    
    /**
     * Is the segment valid.
     *
     * @param (string) $id - Piece Part Detail ID
     * @return bool
     */
    public static function isValid($id)
    {
        $piecePartDetail = PiecePartDetail::with(['PiecePart', 'RPS_Segment'])->find($id);
        
        $model = $piecePartDetail->WPS_Segment ?? NULL;
        
        return is_null($model) ? NULL : $model->getIsValid();
    }
    
    public function validate()
    {
        $shopFindingId = $this->getShopFindingId();
        $piecePartDetailId = $this->getPiecePartDetailId();
        
        $modelArray = $this->getTreatedAttributes();
        
        $profiler = new ValidationProfiler('WPS_Segment', $this, $shopFindingId);
        
        $modelArray['PPI'] = (string) $modelArray['PPI']; // Convert PPI to string.
        
        $validator = Validator::make($modelArray, $profiler->getValidationRules($piecePartDetailId), $profiler->getValidationMessages(), $profiler->getFormAttributes());
        
        $validatedConditionally = $profiler->conditionalValidation($validator);
        
        if ($validatedConditionally->fails()) {
            $this->validationErrors = $validatedConditionally->errors()->all();
            return false;
        }
        
        return true;
    }
    
    /**
     * Create or update the segment.
     *
     * @param (array) $data
     * @param (string) $piecePartDetailId
     * @return void
     */
    public static function createOrUpdateSegment(array $data, string $piecePartDetailId, $autosave = null)
    {
        $WPS_Segment = WPS_Segment::withTrashed()->firstOrNew(['piece_part_detail_id' => $piecePartDetailId, 'PPI' => $data['PPI']]);
        $WPS_Segment->SFI = isset($data['SFI']) ? $data['SFI'] : NULL;
        $WPS_Segment->PFC = isset($data['PFC']) ? $data['PFC'] : NULL;
        $WPS_Segment->MPN = isset($data['MPN']) ? $data['MPN'] : NULL;
        $WPS_Segment->MFR = isset($data['MFR']) ? $data['MFR'] : NULL;
        $WPS_Segment->SER = isset($data['SER']) ? $data['SER'] : NULL;
        $WPS_Segment->MFN = isset($data['MFN']) ? $data['MFN'] : NULL;
        $WPS_Segment->FDE = isset($data['FDE']) ? $data['FDE'] : NULL;
        $WPS_Segment->PNR = isset($data['PNR']) ? $data['PNR'] : NULL;
        $WPS_Segment->USN = isset($data['USN']) ? $data['USN'] : NULL;
        $WPS_Segment->OPN = isset($data['OPN']) ? $data['OPN'] : NULL;
        $WPS_Segment->PDT = isset($data['PDT']) ? $data['PDT'] : NULL;
        $WPS_Segment->GEL = isset($data['GEL']) ? $data['GEL'] : NULL;
        $WPS_Segment->MRD = isset($data['MRD']) && $data['MRD'] ? Carbon::createFromFormat('d/m/Y', $data['MRD']) : NULL;
        $WPS_Segment->ASN = isset($data['ASN']) ? $data['ASN'] : NULL;
        $WPS_Segment->UCN = isset($data['UCN']) ? $data['UCN'] : NULL;
        $WPS_Segment->SPL = isset($data['SPL']) ? $data['SPL'] : NULL;
        $WPS_Segment->UST = isset($data['UST']) ? $data['UST'] : NULL;
        $WPS_Segment->autosaved_at = $autosave ? Carbon::now() : NULL;
        $WPS_Segment->save();
    }
	
	/**
     * Get the Shop Finding Record Identifier.
     *
     * @return string
     */
    public function get_WPS_SFI()
    {
        return mb_strlen(trim($this->SFI)) ? (string) trim($this->SFI) : NULL;
    }
    
    /**
     * Get the Piece Part Record Identifier.
     *
     * @return string
     */
    public function get_WPS_PPI()
    {
        return isset($this->attributes['PPI']) && $this->attributes['PPI'] ? (string) $this->attributes['PPI'] : NULL;
    }
    
    /**
     * Get the Primary Piece Part Failure Indicator.
     *
     * @return string
     */
    public function get_WPS_PFC()
    {
        return mb_strlen(trim($this->PFC)) ? (string) trim($this->PFC) : NULL;
    }
    
    /**
     * Get the Failed Piece Part Vendor Code.
     *
     * @return string
     */
    public function get_WPS_MFR()
    {
        return mb_strlen(trim($this->MFR)) ? (string) strtoupper(trim($this->MFR)) : NULL;
    }
    
    /**
     * Get the Failed Piece Part Vendor Name.
     *
     * @return string
     */
    public function get_WPS_MFN()
    {
        return mb_strlen(trim($this->MFN)) ? (string) trim($this->MFN) : NULL;
    }
    
    /**
     * Get the Failed Piece Part Manufacturer Full Length Part Number.
     *
     * @return string
     */
    public function get_WPS_MPN()
    {
        return mb_strlen(trim($this->MPN)) ? (string) trim($this->MPN) : NULL;
    }
    
    /**
     * Get the Failed Piece Part Serial Number.
     *
     * @return string
     */
    public function get_WPS_SER()
    {
        return mb_strlen(trim($this->SER)) ? (string) trim($this->SER) : NULL;
    }
    
    /**
     * Get the Piece Part Failure Description.
     *
     * @return string
     */
    public function get_WPS_FDE()
    {
        return mb_strlen(trim($this->FDE)) ? (string) trim($this->FDE) : NULL;
    }
    
    /**
     * Get the Vendor Piece Part Number.
     *
     * @return string
     */
    public function get_WPS_PNR()
    {
        return mb_strlen(trim($this->PNR)) ? (string) trim($this->PNR) : NULL;
    }
    
    /**
     * Get the Overlength Part Number.
     *
     * @return string
     */
    public function get_WPS_OPN()
    {
        return mb_strlen(trim($this->OPN)) ? (string) trim($this->OPN) : NULL;
    }
    
    /**
     * Get the Piece Part Universal Serial Number.
     *
     * @return string
     */
    public function get_WPS_USN()
    {
        return mb_strlen(trim($this->USN)) ? (string) trim($this->USN) : NULL;
    }
    
    /**
     * Get the Failed Piece Part Description.
     *
     * @return string
     */
    public function get_WPS_PDT()
    {
        return mb_strlen(trim($this->PDT)) ? (string) trim($this->PDT) : NULL;
    }
    
    /**
     * Get the Piece Part Reference Designator Symbol.
     *
     * @return string
     */
    public function get_WPS_GEL()
    {
        return mb_strlen(trim($this->GEL)) ? (string) trim($this->GEL) : NULL;
    }
    
    /**
     * Get the Received Date.
     *
     * @return date
     */
    public function get_WPS_MRD()
    {
        return $this->MRD ? $this->MRD->format('d/m/Y') : NULL;
    }
    
    /**
     * Get the Operator Piece Part Number.
     *
     * @return string
     */
    public function get_WPS_ASN()
    {
        return mb_strlen(trim($this->ASN)) ? (string) trim($this->ASN) : NULL;
    }
    
    /**
     * Get the Operator Piece Part Serial Number.
     *
     * @return string
     */
    public function get_WPS_UCN()
    {
        return mb_strlen(trim($this->UCN)) ? (string) trim($this->UCN) : NULL;
    }
    
    /**
     * Get the Supplier Code.
     *
     * @return string
     */
    public function get_WPS_SPL()
    {
        return mb_strlen(trim($this->SPL)) ? (string) trim($this->SPL) : NULL;
    }
    
    /**
     * Get the Piece Part Universal Serial Tracking Number.
     *
     * @return string
     */
    public function get_WPS_UST()
    {
        return mb_strlen(trim($this->UST)) ? (string) trim($this->UST) : NULL;
    }
}
