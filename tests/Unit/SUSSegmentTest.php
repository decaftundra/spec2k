<?php

namespace Tests\Unit;

use App\ShopFindings\SUS_Segment;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class SUSSegmentTest extends TestCase
{
    /**
     * Check that the returned value of MFR only ever contains uppercase characters.
     *
     * @return void
     */
    public function testGetSUSSegmentMFROnlyContainsUppercaseChars()
    {
        $segment = factory(SUS_Segment::class)->states('all_fields_max_string_length')->make();
        
        $this->assertEquals($segment->get_SUS_MFR(), strtoupper($segment->MFR));
    }
}
