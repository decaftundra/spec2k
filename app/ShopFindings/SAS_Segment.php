<?php

namespace App\ShopFindings;

use App\ValidationProfiler;
use App\Interfaces\SAS_SegmentInterface;
use App\ShopFindings\ShopFindingsSegment;
use Carbon\Carbon;
use Carbon\Traits\Test;
use Hamcrest\BaseDescription;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Laravel\Dusk\Browser;
use Tests\Feature\DeletedTest;

class SAS_Segment extends ShopFindingsSegment implements SAS_SegmentInterface
{
    /*
    |--------------------------------------------------------------------------------------------------------------------------------|
    | SAS = Shop Action Details                                                                                                      |
    |--------------------------------------------------------------------------------------------------------------------------------|
	| INT | Shop Action Text Incoming                   | Inspection/Shop Action Text     | Y   | String      | 1/5000  |            |
	| SHL | Shop Repair Location Code                   | Shop Repair Facility Code       | Y   | String      | 1/3     | R1         |
	| RFI | Shop Final Action Indicator                 | Repair Final Action Indicator   | Y   | Boolean     | 1       |            |
	| MAT | Mod (S) Incorporated (This Visit) Text      | Manufacturer Authority Text     | N   | String      | 1/40    |            |
	| SAC | Shop Action Code                            | Shop Action Code                | N   | String      | 1/5     | RPLC       |
	| SDI | Shop Disclosure Indicator                   | Shop Disclosure Indicator       | N   | Boolean     | 0       |            |
	| PSC | Part Status Code                            | Part Status Code                | N   | String      | 1/16    | Overhauled |
	| REM | Comment Text                                | Remarks Text                    | N   | String      | 1/1000  |            |
	|--------------------------------------------------------------------------------------------------------------------------------|
    */

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'SAS_Segments';

    /**
     * Get the segment function prefix.
     *
     * @return string
     */
    public function getPrefix()
    {
        return 'get_SAS_';
    }

    /**
     * Get an array of segment keys.
     *
     * @return array
     */
    public static function getKeys()
    {
        return [
            'INT',
        	'SHL',
        	'RFI',
        	'MAT',
        	'SAC',
        	'SDI',
        	'PSC',
        	'REM'
        ];
    }

    /**
     * Get the url for the activities listing page.
     *
     * @return string
     */
    public function getActivityUrl()
    {
        return route('shop-action-details.edit', $this->getShopFindingId());
    }

    /**
     * Get the url title for the activities page.
     *
     * @return string
     */
    public function getActivityUrlTitle()
    {
        return 'View Shop Action Details Segment';
    }

    public static function isMandatory($id)
    {
        $profiler = new ValidationProfiler('SAS_Segment', (new static), $id);

        return $profiler->isMandatory();
    }

    public static function isValid($id)
    {
        $shopFinding = ShopFinding::with('ShopFindingsDetail.SAS_Segment')->find($id);

        $model = $shopFinding->ShopFindingsDetail->SAS_Segment ?? NULL;

        return is_null($model) ? NULL : $model->getIsValid();
    }

    public function validate()
    {
        $shopFindingId = $this->getShopFindingId();
        $modelArray = $this->getTreatedAttributes();

        $profiler = new ValidationProfiler('SAS_Segment', $this, $shopFindingId);

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
        $SAS_Segment = SAS_Segment::firstOrNew(['shop_findings_detail_id' => $shopFindingsDetailId]);
        $SAS_Segment->INT = isset($data['INT']) ? $data['INT'] : NULL;
        $SAS_Segment->SHL = isset($data['SHL']) ? $data['SHL'] : NULL;
        $SAS_Segment->MAT = isset($data['MAT']) ? $data['MAT'] : NULL;
        $SAS_Segment->PSC = isset($data['PSC']) ? $data['PSC'] : NULL;
        $SAS_Segment->SAC = isset($data['SAC']) ? $data['SAC'] : NULL;
        $SAS_Segment->RFI = isset($data['RFI']) ? $data['RFI'] : NULL;
        $SAS_Segment->SDI = isset($data['SDI']) ? $data['SDI'] : NULL;
        $SAS_Segment->REM = isset($data['REM']) ? $data['REM'] : NULL;
        $SAS_Segment->autosaved_at = $autosave ? Carbon::now() : NULL;
        $SAS_Segment->save();


        // LJMMar23 MGTSUP-367
        if ($SAS_Segment->SAC == 'SCRP')
        {
            // we need to decide what date are we putting in here
            if (app()->runningInConsole())
            {// from console
                // do nothing, from teh phone call to do with MGTSUP-504 Fred doesnt thin that SCRP will ever come in here from teh command line.
                // if it does then we need to know what the scrapped date should be set to?

                // his note in the reference Excel documnt (attached to MGTSUP-504) says: we know that MAAP-SAP is not providing any scrap date. Records will stay 'in progress' until we sort it out.
                $shopFindingsDetail = ShopFindingsDetail::firstOrCreate(['id' => $shopFindingsDetailId]);
                $shopFinding = ShopFinding::firstOrCreate(['id' => $shopFindingsDetail->shop_finding_id]);
                $shopFinding->status = 'in_progress';
                $shopFinding->save();

            }
            else
            {// from browser
                $shopFindingsDetail = ShopFindingsDetail::firstOrCreate(['id' => $shopFindingsDetailId]);
                $shopFinding = ShopFinding::firstOrCreate(['id' => $shopFindingsDetail->shop_finding_id]);



                // MGTSUP-504 email from frederic 13-06-23 says the following shouldnt happen so keep it the same as above for now.
                //$shopFinding->scrapped_at = Carbon::now();
                //$shopFinding->shipped_at = NULL;
                //$shopFinding->deleted_at = NULL;
                //$shopFinding->subcontracted_at = NULL;
                //$shopFinding->status = 'complete_scrapped';
                $shopFinding->status = 'in_progress'; // instead.



                $shopFinding->save();

            }
        }







        // LJMMay23 MGTSUP-438
        elseif ($SAS_Segment->SAC == 'MSUB')
        {
            Log::info("LJMDEBUG MSUB from console.");

            // LJM then update the table:
            $shopFindingsDetail = ShopFindingsDetail::firstOrCreate(['id' => $shopFindingsDetailId]);
            $shopFinding = ShopFinding::firstOrCreate(['id' => $shopFindingsDetail->shop_finding_id]);
            $shopFinding->scrapped_at = NULL;
            $shopFinding->shipped_at = NULL;
            $shopFinding->deleted_at = NULL;
            $shopFinding->subcontracted_at = Carbon::now();
            $shopFinding->status = 'subcontracted';
            $shopFinding->save();
        }






    }

    /**
     * Get the Shop Action Text Incoming.
     *
     * @return string
     */
    public function get_SAS_INT()
    {
        return mb_strlen(trim($this->INT)) ? (string) trim($this->INT) : NULL;
    }

    /**
     * Get the Shop Repair Location Code.
     *
     * @return string
     */
    public function get_SAS_SHL()
    {
        return mb_strlen(trim($this->SHL)) ? (string) trim($this->SHL) : NULL;
    }

    /**
     * Get the Shop Final Action Indicator.
     *
     * @return should be boolean but will be a string (see comment)
     */
    public function get_SAS_RFI()
    {
        return mb_strlen(trim($this->RFI)) ? (string) trim($this->RFI) : NULL;
    }

    /**
     * Get the Mod (S) Incorporated (This Visit) Text.
     *
     * @return string
     */
    public function get_SAS_MAT()
    {
        return mb_strlen(trim($this->MAT)) ? (string) trim($this->MAT) : NULL;
    }

    /**
     * Get the Shop Action Code.
     *
     * @return string
     */
    public function get_SAS_SAC()
    {
        return mb_strlen(trim($this->SAC)) ? (string) trim($this->SAC) : NULL;
    }

    /**
     * Get the Shop Disclosure Indicator.
     *
     * @return should be boolean but will be a string (see comment)
     */
    public function get_SAS_SDI()
    {
        return mb_strlen(trim($this->SDI)) ? (string) trim($this->SDI) : NULL;
    }

    /**
     * Get the Part Status Code.
     *
     * @return string
     */
    public function get_SAS_PSC()
    {
        return mb_strlen(trim($this->PSC)) ? (string) trim($this->PSC) : NULL;
    }

    /**
     * Get the Comment Text.
     *
     * @return string
     */
    public function get_SAS_REM()
    {
        return mb_strlen(trim($this->REM)) ? (string) trim($this->REM) : NULL;
    }
}
