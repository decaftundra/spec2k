<?php

namespace Tests\Unit;

use App\ShopFindings\RCS_Segment;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class RCSSegmentTest extends TestCase
{
    /**
     * Check that the returned value of MFR only ever contains uppercase characters.
     *
     * @return void
     */
    public function testGetRCSSegmentMFROnlyContainsUppercaseChars()
    {
        $segment = factory(RCS_Segment::class)->states('all_fields_max_string_length')->make();
        
        $this->assertEquals($segment->get_RCS_MFR(), strtoupper($segment->MFR));
    }
}
