<?php

namespace App\Http\Requests;

use App\ValidationProfiler;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Interfaces\SegmentInterface;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Str;
use Illuminate\Foundation\Http\FormRequest;

/**
 * This validates segments and also caches invalid segments that aren't in the database.
 */
abstract class SegmentFormRequest extends FormRequest implements SegmentInterface
{
    protected $profiler;
    protected $segmentName;
    protected $ignoreId;
    protected $ignoreParameter;
    protected $piecePartDetail;
    
    public function __construct()
    {
        parent::__construct();
        
        $this->setProfiler();
        $this->setIgnoreId();
        $this->setPiecePartDetailId();
    }
    
    public function setProfiler()
    {
        $this->profiler = new ValidationProfiler($this->segmentName, $this, Request::get('rcsSFI'));
    }
    
    public function setIgnoreId()
    {
        $this->ignoreId = $this->ignoreParameter ? Request::get($this->ignoreParameter) : NULL;
    }
    
    public function setPiecePartDetailId()
    {
        $this->piecePartDetailId = Request::route('piece_part_detail_id');
    }
    
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return auth()->check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $rules = $this->profiler->getValidationRules($this->ignoreId);
        $rules['plant_code'] = 'required';
        
        return $rules;
    }
    
    /**
     * Get the form attributes.
     *
     * @return array
     */
    public function attributes()
    {
        return $this->profiler->getFormAttributes();
    }
    
    /**
     * Get the form validation messages.
     *
     * @return array
     */
    public function messages()
    {
        return $this->profiler->getValidationMessages();
    }
    
    /**
     * After validation hooks.
     *
     * @param  \Illuminate\Validation\Validator  $validator
     * @return void
     */
    public function withValidator($validator)
    {
        $conditionallyValidated = $this->profiler->conditionalValidation($validator);
        
        // Log validation errors.
        if ($conditionallyValidated->fails()) {
            Log::error($this->segmentName.' '.$this->method().' validation errors!', $conditionallyValidated->errors()->all());
            Log::error($this->request->all());
            
            if (!$this->isAlreadySaved()) {
                $this->cacheInvalidSegment();
            }
        } else {
            $this->deleteCachedInvalidSegment();
        }
    }
    
    /**
     * Cache the invalid version as work in progress if no valid segment is already in the database.
     *
     * @return void
     */
    protected function cacheInvalidSegment()
    {
        if ($this->isPiecePartSegment() && $this->piecePartDetailId) {
            Cache::forever($this->piecePartDetailId . '.' . $this->segmentName, Request::except('_token'));
        } elseif (!$this->isPiecePartSegment()) {
            Cache::forever($this->request->get('rcsSFI') . '.' . $this->segmentName, Request::except('_token'));
        }
    }
    
    /**
     * Delete the temporary cached segment.
     *
     * @return void
     */
    protected function deleteCachedInvalidSegment()
    {
        if ($this->isPiecePartSegment() && $this->piecePartDetailId) {
            Cache::forget($this->piecePartDetailId . '.' . $this->segmentName);
        } elseif (!$this->isPiecePartSegment()) {
            Cache::forget($this->request->get('rcsSFI') . '.' . $this->segmentName);
        }
    }
    
    /**
     * Is the segment a piece part segment.
     *
     * @return boolean
     */
    protected function isPiecePartSegment()
    {
        return in_array($this->segmentName, ['WPS_Segment', 'NHS_Segment', 'RPS_Segment']);
    }
    
    /**
     * Is the segment a header segment.
     *
     * @return boolean
     */
    protected function isHeaderSegment()
    {
        return $this->segmentName == 'HDR_Segment';
    }
    
    /**
     * Has the segment already been saved.
     *
     * @return integer
     */
    protected function isAlreadySaved()
    {
        $tableName = Str::plural($this->segmentName);
        
        if ($this->isHeaderSegment()) {
            return DB::table($tableName)
                ->where('shop_finding_id', $this->request->get('rcsSFI'))
                ->count();
        } elseif ($this->isPiecePartSegment() && $this->piecePartDetailId) {
            return DB::table(Str::plural($tableName))
                ->where('piece_part_detail_id', $this->piecePartDetailId)
                ->count();
        } elseif (!$this->isPiecePartSegment()) {
            return DB::table($tableName)
                ->leftJoin('shop_findings_details', 'shop_findings_details.id', '=', $tableName.'.shop_findings_detail_id')
                ->leftJoin('shop_findings', 'shop_findings_details.shop_finding_id', '=', 'shop_findings.id')
                ->where('shop_findings.id', $this->request->get('rcsSFI'))
                ->count();
        }
    }
    
    /**
     * Get the request attributes.
     *
     * @return array
     */
    public function getAttributes()
    {
        return $this->request->except(['_token']);
    }
    
    /**
     * Get an array of properties from the segment that are dates.
     *
     * @return array
     */
    public abstract function getDates();
}
