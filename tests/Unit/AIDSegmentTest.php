<?php

namespace Tests\Unit;

use App\ShopFindings\AID_Segment;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AIDSegmentTest extends TestCase
{
    /**
     * Check that the returned value of MFR only ever contains uppercase characters.
     *
     * @return void
     */
    public function testGetAIDSegmentMFROnlyContainsUppercaseChars()
    {
        $segment = factory(AID_Segment::class)->states('all_fields_max_string_length')->make();
        
        $this->assertEquals($segment->get_AID_MFR(), strtoupper($segment->MFR));
    }
}
