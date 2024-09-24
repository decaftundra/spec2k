<?php

namespace App\PieceParts;

use App\Interfaces\RPS_SegmentInterface;
use App\NotificationPiecePart;
use App\PieceParts\PiecePartSegment;
use App\ValidationProfiler;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Validator;

class RPS_Segment extends PiecePartSegment implements RPS_SegmentInterface
{
    /*
    |-----------------------------------------------------------------------------------------------------------------------------------------------|
    | RPS = Replaced Piece Part                                                                                                                     |
    |-----------------------------------------------------------------------------------------------------------------------------------------------|
	| MPN | Replaced Piece Part Manufacturer Full Length Part Number    | Manufacturer Full Length Part Number    | MPN     | Y   | String  | 1/32  |
	| MFR | Replaced Piece Part Vendor Code                             | Manufacturer Code                       | MFR     | N   | String  | 5/5   |
	| MFN | Replaced Piece Part Vendor Name                             | Manufacturer Name                       | MFN     | N   | String  | 1/55  |
	| SER | Replaced Vendor Piece Part Serial Number                    | Part Serial Number                      | SER     | N   | String  | 1/15  |
	| PNR | Replaced Vendor Piece Part Number                           | Part Number                             | PNR     | N   | String  | 1/15  |
	| OPN | Overlength Part Number                                      | Overlength Part Number                  | OPN     | N   | String  | 16/32 |
	| USN | Replaced Piece Part Universal Serial Number                 | Universal Serial Number                 | USN     | N   | String  | 6/20  |
	| ASN | Replaced Operator Piece Part Number                         | Airline Stock Number                    | ASN     | N   | String  | 1/32  |
	| UCN | Replaced Operator Piece Part Serial Number                  | Unique Component Identification Number  | UCN     | N   | String  | 1/15  |
	| SPL | Supplier Code                                               | Supplier Code                           | SPL     | N   | String  | 5/5   |
	| UST | Replaced Piece Part Universal Serial Tracking Number        | Universal Serial Tracking Number        | UST     | N   | String  | 6/20  |
	| PDT | Replaced Vendor Piece Part Description                      | Part Description                        | PDT     | N   | String  | 1/100 |
	|-----------------------------------------------------------------------------------------------------------------------------------------------|
    */
    
    use SoftDeletes;
    
    protected $dates = ['deleted_at'];
    
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'RPS_Segments';
    
    /**
     * Get the segment function prefix.
     *
     * @return string
     */
    public function getPrefix()
    {
        return 'get_RPS_';
    }
    
    /**
     * Get an array of segment keys.
     *
     * @return array
     */
    public static function getKeys()
    {
        return [
            'MPN',
        	'MFR',
        	'MFN',
        	'SER',
        	'PNR',
        	'OPN',
        	'USN',
        	'ASN',
        	'UCN',
        	'SPL',
        	'UST',
        	'PDT'
        ];
    }
    
    /**
     * Get the url for the activities listing page.
     *
     * @return string
     */
    public function getActivityUrl()
    {
        return route('replaced-piece-part.edit', [$this->getShopFindingId(), $this->getPiecePartDetailId()]);
    }
    
    /**
     * Get the url title for the activities page.
     *
     * @return string
     */
    public function getActivityUrlTitle()
    {
        return 'View Replaced Piece Part Segment';
    }
    
    public static function isMandatory($id)
    {
        $piecePartDetail = PiecePartDetail::with(['PiecePart', 'RPS_Segment'])->find($id);
        
        if (is_null($piecePartDetail) || is_null($piecePartDetail->WPS_Segment)) {
            $shopFindingId = NotificationPiecePart::find($id)->notification_id;
        } else {
            $shopFindingId = $piecePartDetail->PiecePart->shop_finding_id;
        }
        
        $profiler = new ValidationProfiler('RPS_Segment', (new static), $shopFindingId);
        
        return $profiler->isMandatory();
    }
	
	/**
	 * Find if the segment is valid statically using the piece part detail id.
	 *
	 * @param (int) $id
	 * @return null | bool
	 */
    public static function isValid($id)
    {
        $piecePartDetail = PiecePartDetail::with(['PiecePart', 'RPS_Segment'])->find($id);
        
        $model = $piecePartDetail->RPS_Segment ?? NULL;
        
        return is_null($model) ? NULL : $model->getIsValid();
    }
    
    public function validate()
    {
        $shopFindingId = $this->getShopFindingId();
        $piecePartDetailId = $this->getPiecePartDetailId();
        
        $modelArray = $this->getTreatedAttributes();
        
        $profiler = new ValidationProfiler('RPS_Segment', $this, $shopFindingId);
        
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
        $RPS_Segment = RPS_Segment::withTrashed()->firstOrNew(['piece_part_detail_id' => $piecePartDetailId]);
        $RPS_Segment->MPN = isset($data['MPN']) ? $data['MPN'] : NULL;
        $RPS_Segment->MFR = isset($data['MFR']) ? $data['MFR'] : NULL;
        $RPS_Segment->MFN = isset($data['MFN']) ? $data['MFN'] : NULL;
        $RPS_Segment->PNR = isset($data['PNR']) ? $data['PNR'] : NULL;
        $RPS_Segment->OPN = isset($data['OPN']) ? $data['OPN'] : NULL;
        $RPS_Segment->SER = isset($data['SER']) ? $data['SER'] : NULL;
        $RPS_Segment->USN = isset($data['USN']) ? $data['USN'] : NULL;
        $RPS_Segment->ASN = isset($data['ASN']) ? $data['ASN'] : NULL;
        $RPS_Segment->UCN = isset($data['UCN']) ? $data['UCN'] : NULL;
        $RPS_Segment->SPL = isset($data['SPL']) ? $data['SPL'] : NULL;
        $RPS_Segment->UST = isset($data['UST']) ? $data['UST'] : NULL;
        $RPS_Segment->PDT = isset($data['PDT']) ? $data['PDT'] : NULL;
        $RPS_Segment->autosaved_at = $autosave ? Carbon::now() : NULL;
        $RPS_Segment->save();
    }
    
    /**
     * Get the Replaced Piece Part Manufacturer Full Length Part Number.
     *
     * @return string
     */
    public function get_RPS_MPN()
    {
        return mb_strlen(trim($this->MPN)) ? (string) trim($this->MPN) : NULL;
    }
    
    /**
     * Get the Replaced Piece Part Vendor Code.
     *
     * @return string
     */
    public function get_RPS_MFR()
    {
        return mb_strlen(trim($this->MFR)) ? (string) strtoupper(trim($this->MFR)) : NULL;
    }
    
    /**
     * Get the Replaced Piece Part Vendor Name.
     *
     * @return string
     */
    public function get_RPS_MFN()
    {
        return mb_strlen(trim($this->MFN)) ? (string) trim($this->MFN) : NULL;
    }
    
    /**
     * Get the Replaced Vendor Piece Part Serial Number.
     *
     * @return string
     */
    public function get_RPS_SER()
    {
        return mb_strlen(trim($this->SER)) ? (string) trim($this->SER) : NULL;
    }
    
    /**
     * Get the Replaced Vendor Piece Part Number.
     *
     * @return string
     */
    public function get_RPS_PNR()
    {
        return mb_strlen(trim($this->PNR)) ? (string) trim($this->PNR) : NULL;
    }
    
    /**
     * Get the Overlength Part Number.
     *
     * @return string
     */
    public function get_RPS_OPN()
    {
        return mb_strlen(trim($this->OPN)) ? (string) trim($this->OPN) : NULL;
    }
    
    /**
     * Get the Replaced Piece Part Universal Serial Number.
     *
     * @return string
     */
    public function get_RPS_USN()
    {
        return mb_strlen(trim($this->USN)) ? (string) trim($this->USN) : NULL;
    }
    
    /**
     * Get the Replaced Operator Piece Part Number.
     *
     * @return string
     */
    public function get_RPS_ASN()
    {
        return mb_strlen(trim($this->ASN)) ? (string) trim($this->ASN) : NULL;
    }
    
    /**
     * Get the Replaced Operator Piece Part Serial Number.
     *
     * @return string
     */
    public function get_RPS_UCN()
    {
        return mb_strlen(trim($this->UCN)) ? (string) trim($this->UCN) : NULL;
    }
    
    /**
     * Get the Supplier Code.
     *
     * @return string
     */
    public function get_RPS_SPL()
    {
        return mb_strlen(trim($this->SPL)) ? (string) trim($this->SPL) : NULL;
    }
    
    /**
     * Get the Replaced Piece Part Universal Serial Tracking Number.
     *
     * @return string
     */
    public function get_RPS_UST()
    {
        return mb_strlen(trim($this->UST)) ? (string) trim($this->UST) : NULL;
    }
    
    /**
     * Get the Replaced Vendor Piece Part Description.
     *
     * @return string
     */
    public function get_RPS_PDT()
    {
        return mb_strlen(trim($this->PDT)) ? (string) trim($this->PDT) : NULL;
    }
}
