<?php

namespace Tests\Unit;

use App\ShopFindings\EID_Segment;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class EIDSegmentTest extends TestCase
{
    /**
     * Check that the returned value of MFR only ever contains uppercase characters.
     *
     * @return void
     */
    public function testGetEIDSegmentMFROnlyContainsUppercaseChars()
    {
        $segment = factory(EID_Segment::class)->states('all_fields_max_string_length')->make();
        
        $this->assertEquals($segment->get_EID_MFR(), strtoupper($segment->MFR));
    }
}
