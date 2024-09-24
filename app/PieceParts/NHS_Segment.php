<?php

namespace App\PieceParts;

use App\Interfaces\NHS_SegmentInterface;
use App\NotificationPiecePart;
use App\PieceParts\PiecePartSegment;
use App\ValidationProfiler;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Validator;

class NHS_Segment extends PiecePartSegment implements NHS_SegmentInterface
{
    /*
    |---------------------------------------------------------------------------------------------------------------------------------------------------------|
    | NHS = Next Higher Assembly                                                                                                                              |
    |---------------------------------------------------------------------------------------------------------------------------------------------------------|
	| MFR | Failed Piece Part Next Higher Assembly Part Manufacturer Code   | Manufacturer Code                                       | Y   | String  | 5/5   |
	| MPN | Next Higher Assembly Manufacturer Full Length Part Number       | Manufacturer Full Length Part Number                    | Y   | String  | 1/32  |
	| SER | Failed Piece Part Next Higher Assembly Serial Number            | Part Serial Number                                      | Y   | String  | 1/15  |
	| MFN | Failed Piece Part Next Higher Assembly Part Manufacturer Name   | Manufacturer Name                                       | N   | String  | 1/55  |
	| PNR | Failed Piece Part Next Higher Assembly Part Number              | Part Number                                             | N   | String  | 1/15  |
	| OPN | Overlength Part Number                                          | Overlength Part Number                                  | N   | String  | 16/32 |
	| USN | Failed Piece Part Universal Serial Number                       | Universal Serial Number                                 | N   | String  | 6/20  |
	| PDT | Failed Piece Part Next Higher Assembly Part Name                | Part Description                                        | N   | String  | 1/100 |
	| ASN | Failed Piece Part Next Higher Assembly Operator Part Number     | Airline Stock Number                                    | N   | String  | 1/32  |
	| UCN | Failed Piece Part Next Higher Assembly Operator Serial Number   | Unique Component Identification Number                  | N   | String  | 1/15  |
	| SPL | Supplier Code                                                   | Supplier Code                                           | N   | String  | 5/5   |
	| UST | Failed Piece Part NHA Universal Serial Tracking Number          | Universal Serial Tracking Number                        | N   | String  | 6/20  |
	| NPN | Failed Piece Part Next Higher Assembly NHA Part Number          | Failed Piece Part Next Higher Assembly NHA Part Number  | N   | String  | 1/32  |
	|---------------------------------------------------------------------------------------------------------------------------------------------------------|
    */
    
    use SoftDeletes;
    
    protected $dates = ['deleted_at'];
    
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'NHS_Segments';
    
    /**
     * Get the segment function prefix.
     *
     * @return string
     */
    public function getPrefix()
    {
        return 'get_NHS_';
    }
    
    /**
     * Get an array of segment keys.
     *
     * @return array
     */
    public static function getKeys()
    {
        return [
            'MFR',
        	'MPN',
        	'SER',
        	'MFN',
        	'PNR',
        	'OPN',
        	'USN',
        	'PDT',
        	'ASN',
        	'UCN',
        	'SPL',
        	'UST',
        	'NPN'
        ];
    }
    
    /**
     * Get the url for the activities listing page.
     *
     * @return string
     */
    public function getActivityUrl()
    {
        return route('next-higher-assembly.edit', [$this->getShopFindingId(), $this->getPiecePartDetailId()]);
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
        
        $profiler = new ValidationProfiler('NHS_Segment', (new static), $shopFindingId);
        
        return $profiler->isMandatory();
    }
	
    public static function isValid($id)
    {
        $piecePartDetail = PiecePartDetail::with(['PiecePart', 'RPS_Segment'])->find($id);
        
        $model = $piecePartDetail->NHS_Segment ?? NULL;
        
        return is_null($model) ? NULL : $model->getIsValid();
    }
    
    public function validate()
    {
        $shopFindingId = $this->getShopFindingId();
        $piecePartDetailId = $this->getPiecePartDetailId();
        
        $modelArray = $this->getTreatedAttributes();
        
        $profiler = new ValidationProfiler('NHS_Segment', $this, $shopFindingId);
        
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
        $NHS_Segment = NHS_Segment::withTrashed()->firstOrNew(['piece_part_detail_id' => $piecePartDetailId]);
        $NHS_Segment->MFR = isset($data['MFR']) ? $data['MFR'] : NULL;
        $NHS_Segment->SER = isset($data['SER']) ? $data['SER'] : NULL;
        $NHS_Segment->MPN = isset($data['MPN']) ? $data['MPN'] : NULL;
        $NHS_Segment->MFN = isset($data['MFN']) ? $data['MFN'] : NULL;
        $NHS_Segment->PNR = isset($data['PNR']) ? $data['PNR'] : NULL;
        $NHS_Segment->OPN = isset($data['OPN']) ? $data['OPN'] : NULL;
        $NHS_Segment->USN = isset($data['USN']) ? $data['USN'] : NULL;
        $NHS_Segment->PDT = isset($data['PDT']) ? $data['PDT'] : NULL;
        $NHS_Segment->ASN = isset($data['ASN']) ? $data['ASN'] : NULL;
        $NHS_Segment->UCN = isset($data['UCN']) ? $data['UCN'] : NULL;
        $NHS_Segment->SPL = isset($data['SPL']) ? $data['SPL'] : NULL;
        $NHS_Segment->UST = isset($data['UST']) ? $data['UST'] : NULL;
        $NHS_Segment->NPN = isset($data['NPN']) ? $data['NPN'] : NULL;
        $NHS_Segment->autosaved_at = $autosave ? Carbon::now() : NULL;
        $NHS_Segment->save();
    }
    
    /**
     * Get the Failed Piece Part Next Higher Assembly Part Manufacturer Code.
     *
     * @return string
     */
    public function get_NHS_MFR()
    {
        return mb_strlen(trim($this->MFR)) ? (string) strtoupper(trim($this->MFR)) : NULL;
    }
    
    /**
     * Get the Next Higher Assembly Manufacturer Full Length Part Number.
     *
     * @return string
     */
    public function get_NHS_MPN()
    {
        return mb_strlen(trim($this->MPN)) ? (string) trim($this->MPN) : NULL;
    }
    
    /**
     * Get the Failed Piece Part Next Higher Assembly Serial Number.
     *
     * @return string
     */
    public function get_NHS_SER()
    {
        return mb_strlen(trim($this->SER)) ? (string) trim($this->SER) : NULL;
    }
    
    /**
     * Get the Failed Piece Part Next Higher Assembly Part Manufacturer Name.
     *
     * @return string
     */
    public function get_NHS_MFN()
    {
        return mb_strlen(trim($this->MFN)) ? (string) trim($this->MFN) : NULL;
    }
    
    /**
     * Get the Failed Piece Part Next Higher Assembly Part Number.
     *
     * @return string
     */
    public function get_NHS_PNR()
    {
        return mb_strlen(trim($this->PNR)) ? (string) trim($this->PNR) : NULL;
    }
    
    /**
     * Get the Overlength Part Number.
     *
     * @return string
     */
    public function get_NHS_OPN()
    {
        return mb_strlen(trim($this->OPN)) ? (string) trim($this->OPN) : NULL;
    }
    
    /**
     * Get the Failed Piece Part Universal Serial Number.
     *
     * @return string
     */
    public function get_NHS_USN()
    {
        return mb_strlen(trim($this->USN)) ? (string) trim($this->USN) : NULL;
    }
    
    /**
     * Get the Failed Piece Part Next Higher Assembly Part Name.
     *
     * @return string
     */
    public function get_NHS_PDT()
    {
        return mb_strlen(trim($this->PDT)) ? (string) trim($this->PDT) : NULL;
    }
    
    /**
     * Get the Failed Piece Part Next Higher Assembly Operator Part Number.
     *
     * @return string
     */
    public function get_NHS_ASN()
    {
        return mb_strlen(trim($this->ASN)) ? (string) trim($this->ASN) : NULL;
    }
    
    /**
     * Get the Failed Piece Part Next Higher Assembly Operator Serial Number.
     *
     * @return string
     */
    public function get_NHS_UCN()
    {
        return mb_strlen(trim($this->UCN)) ? (string) trim($this->UCN) : NULL;
    }
    
    /**
     * Get the Supplier Code.
     *
     * @return string
     */
    public function get_NHS_SPL()
    {
        return mb_strlen(trim($this->SPL)) ? (string) trim($this->SPL) : NULL;
    }
    
    /**
     * Get the Failed Piece Part NHA Universal Serial Tracking Number.
     *
     * @return string
     */
    public function get_NHS_UST()
    {
        return mb_strlen(trim($this->UST)) ? (string) trim($this->UST) : NULL;
    }
    
    /**
     * Get the Failed Piece Part Next Higher Assembly NHA Part Number.
     *
     * @return string
     */
    public function get_NHS_NPN()
    {
        return mb_strlen(trim($this->NPN)) ? (string) trim($this->NPN) : NULL;
    }
}
