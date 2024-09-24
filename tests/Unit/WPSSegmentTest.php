<?php

namespace Tests\Unit;

use App\PieceParts\WPS_Segment;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class WPSSegmentTest extends TestCase
{
    /**
     * Check that the returned value of MFR only ever contains uppercase characters.
     *
     * @return void
     */
    public function testGetWPSSegmentMFROnlyContainsUppercaseChars()
    {
        $segment = factory(WPS_Segment::class)->states('all_fields_max_string_length')->make();
        
        $this->assertEquals($segment->get_WPS_MFR(), strtoupper($segment->MFR));
    }
}
