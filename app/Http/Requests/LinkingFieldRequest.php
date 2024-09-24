<?php

namespace App\Http\Requests;

use App\ShopFindings\LNK_Segment;
use Illuminate\Support\Facades\Log;
use App\Interfaces\LNK_SegmentInterface;
use App\Http\Requests\SegmentFormRequest;

class LinkingFieldRequest extends SegmentFormRequest implements LNK_SegmentInterface
{
    protected $segmentName = 'LNK_Segment';
    
    /**
     * Get the Removal Tracking Identifier.
     *
     * @return string
     */
    public function get_LNK_RTI()
    {
        return (string) $this->request->get('RTI');
    }
    
    public function getDates()
    {
        return (new LNK_Segment)->getDates();
    }
}
