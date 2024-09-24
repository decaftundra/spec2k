<?php

namespace App\Http\Requests;

use App\ShopFindings\EID_Segment;
use Illuminate\Support\Facades\Log;
use App\Interfaces\EID_SegmentInterface;
use App\Http\Requests\SegmentFormRequest;
use Illuminate\Support\Facades\DB;

class EngineInformationRequest extends SegmentFormRequest implements EID_SegmentInterface
{
    protected $segmentName = 'EID_Segment';

    /**
     * Get the Aircraft Engine Type.
     *
     * @return string
     */
    public function get_EID_AET()
    {
        return (string) $this->request->get('AET');
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
        return (string) $this->request->get('EPC');
    }

    /**
     * Get the Aircraft Engine Model.
     *
     * @return string
     */
    public function get_EID_AEM()
    {
        return (string) $this->request->get('AEM');
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

        // putting this here because if it is in a single php file to be called from then that causes problems being called both from here HTTP pages and the ARTISAN console app calls.

        //is the value in the array of Engine Types?
        $szReturn = "";


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
        return (string) $this->request->get('EMS');
    }

    /**
     * Get the Aircraft Engine Manufacturer Code.
     *
     * @return string
     */
    public function get_EID_MFR()
    {
        return (string) $this->request->get('MFR');
    }

    /**
     * Get the Engine Cumulative Hours.
     *
     * @return float
     */
    public function get_EID_ETH()
    {
        return $this->request->has('ETH') ? (float) $this->request->get('ETH') : NULL;
    }

    /**
     * Get the Engine Cumulative Cycles.
     *
     * @return integer
     */
    public function get_EID_ETC()
    {
        return $this->request->has('ETC') ? (int) $this->request->get('ETC') : NULL;
    }

    public function getDates()
    {
        return (new EID_Segment)->getDates();
    }
}
