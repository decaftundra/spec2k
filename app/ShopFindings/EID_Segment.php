<?php

namespace App\ShopFindings;

use App\ValidationProfiler;
use App\ShopFindings\ShopFindingsSegment;
use App\Interfaces\EID_SegmentInterface;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;
use App\Codes\EngineTypeCode;
use App\Codes\EngineModelCode;
use Illuminate\Support\Facades\DB;

class EID_Segment extends ShopFindingsSegment implements EID_SegmentInterface
{
    /*
    |------------------------------------------------------------------------------------------------------------------------------|
    | EID = Engine Information                                                                                                     |
    |------------------------------------------------------------------------------------------------------------------------------|
    | AET | Aircraft Engine Type                  | Aircraft Engine/APU Type                | Y   | String  | 1/20    | PW4000     |
    | AETO | Aircraft Engine Type: Other           | Aircraft Engine/APU Type                | Y   | String  | 1/20    | PW4000     |
	| EPC | Engine Position Code                  | Engine Position Identifier              | Y   | String  | 1/25    | 2          |
	| AEM | Aircraft Engine Model                 | Aircraft Engine/APU Model               | Y   | String  | 1/32    | PW4056     |
	| AEMO | Aircraft Engine Model: Other         | Aircraft Engine/APU Model               | Y   | String  | 1/32    | PW4056     |
	| EMS | Engine Serial Number                  | Engine/APU Module Serial Number         | N   | String  | 1/20    | PCE-FA0006 |
	| MFR | Aircraft Engine Manufacturer Code     | Manufacturer Code                       | N   | String  | 5/5     | 77445      |
	| ETH | Engine Cumulative Hours               | Engine Cumulative Total Flight Hours    | N   | Decimal | 9,2     |            |
	| ETC | Engine Cumulative Cycles              | Engine Cumulative Total Cycles          | N   | Integer | 1/9     |            |
	|------------------------------------------------------------------------------------------------------------------------------|
	*/

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'EID_Segments';

    /**
     * Get the segment function prefix.
     *
     * @return string
     */
    public function getPrefix()
    {
        return 'get_EID_';
    }

    /**
     * Get an array of segment keys.
     *
     * @return array
     */
    public static function getKeys()
    {
        return [
            'AET',
            'AETO',
        	'EPC',
        	'AEM',
        	'AEMO',
        	'EMS',
        	'MFR',
        	'ETH',
        	'ETC'
        ];
    }

    /**
     * Get the url for the activities listing page.
     *
     * @return string
     */
    public function getActivityUrl()
    {
        return route('engine-information.edit', $this->getShopFindingId());
    }

    /**
     * Get the url title for the activities page.
     *
     * @return string
     */
    public function getActivityUrlTitle()
    {
        return 'View Engine Information Segment';
    }

    public static function isMandatory($id)
    {
        $profiler = new ValidationProfiler('EID_Segment', (new static), $id);

        return $profiler->isMandatory();
    }

    public static function isValid($id)
    {
        $shopFinding = ShopFinding::with('ShopFindingsDetail.EID_Segment')->find($id);

        $model = $shopFinding->ShopFindingsDetail->EID_Segment ?? NULL;

        return is_null($model) ? NULL : $model->getIsValid();
    }

    public function validate()
    {
        $shopFindingId = $this->getShopFindingId();
        $modelArray = $this->getTreatedAttributes();

        $profiler = new ValidationProfiler('EID_Segment', $this, $shopFindingId);

        $validator = Validator::make($modelArray, $profiler->getValidationRules($shopFindingId), $profiler->getValidationMessages(), $profiler->getFormAttributes());

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
     * @param (string) $shopFindingsDetailId
     * @return void
     */
    public static function createOrUpdateSegment(array $data, string $shopFindingsDetailId, $autosave = null)
    {
        $EID_Segment = EID_Segment::firstOrNew(['shop_findings_detail_id' => $shopFindingsDetailId]);

        if(isset($data['AET']))
        {
             $EID_Segment->AET = $data['AET'];
        }
        else{
            $EID_Segment->AET = $data['AETO'];
        }

        if(isset($data['AEM']))
        {
            $EID_Segment->AEM = $data['AEM'];
        }
        else{
            $EID_Segment->AEM = $data['AEMO'];
        }

        $EID_Segment->EMS = isset($data['EMS']) ? $data['EMS'] : NULL;
        $EID_Segment->EPC = isset($data['EPC']) ? $data['EPC'] : NULL;
        $EID_Segment->MFR = isset($data['MFR']) ? $data['MFR'] : NULL;
        $EID_Segment->ETH = isset($data['ETH']) ? $data['ETH'] : NULL;
        $EID_Segment->ETC = isset($data['ETC']) ? $data['ETC'] : NULL;
        $EID_Segment->autosaved_at = $autosave ? Carbon::now() : NULL;
        $EID_Segment->save();
    }

	/**
     * Get the Aircraft Engine Type.
     *
     * @return string
     */
    public function get_EID_AET()
    {
        //Do some Fuzy Matching here?
        return mb_strlen(trim($this->AET)) ? (string) strtolower(trim($this->AET)) : NULL;
    }

    	/**
     * Get the Aircraft Engine Type.
     *
     * @return string
     */
    public function get_EID_AETO()
    {
        //is the value in the array of Engine Types?
        $engineTypeCodes = EngineTypeCode::getPermittedValues();
        if(in_array(trim(strtolower($this->AET)), $engineTypeCodes, true))
        {
            return NULL;
        }
        else
        {
             return mb_strlen(trim($this->AET)) ? (string) trim($this->AET) : NULL;
        }

    }


    /**
     * Get the Engine Position Code.
     *
     * @return string
     */
    public function get_EID_EPC()
    {
        return mb_strlen(trim($this->EPC)) ? (string) trim($this->EPC) : NULL;
    }

    /**
     * Get the Aircraft Engine Model.
     *
     * @return string
     */
    public function get_EID_AEM()
    {
        return mb_strlen(trim($this->AEM)) ? (string) strtolower(trim($this->AEM)) : NULL;
    }

    public function get_EID_AEMO()
    {
        //is the value in the array of Engine Types?
        $engineModelCodes = EngineModelCode::getPermittedValues();
        if(in_array(trim(strtolower($this->AEM)), $engineModelCodes, true))
        {
            return NULL;
        }
        else
        {
            return mb_strlen(trim($this->AEM)) ? (string) trim($this->AEM) : NULL;
        }

    }

    public function get_EID_LJMFILTERINFO()
    {
        //is the value in the array of Engine Types?
        $szReturn = "";

        // putting this here because if it is in a single php file to be called from then that causes problems being called both from here HTTP pages and the ARTISAN console app calls.


        $tfwxEngineDetails = DB::select(" SELECT * FROM engine_details order by engine_type, engines_series; ");
        foreach ($tfwxEngineDetails as $tfwxEngineDetail)
        {
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
        return mb_strlen(trim($this->EMS)) ? (string) trim($this->EMS) : NULL;
    }

    /**
     * Get the Aircraft Engine Manufacturer Code.
     *
     * @return string
     */
    public function get_EID_MFR()
    {
        return mb_strlen(trim($this->MFR)) ? (string) strtoupper(trim($this->MFR)) : NULL;
    }

    /**
     * Get the Engine Cumulative Hours.
     *
     * @return float
     */
    public function get_EID_ETH()
    {
        return mb_strlen(trim($this->ETH)) ? (float) $this->ETH : NULL;
    }

    /**
     * Get the Engine Cumulative Cycles.
     *
     * @return integer
     */
    public function get_EID_ETC()
    {
        return mb_strlen(trim($this->ETC)) ? (int) $this->ETC : NULL;
    }
}
