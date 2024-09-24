<?php

namespace Tests\Unit;

use App\ShopFindings\RLS_Segment;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class RLSSegmentTest extends TestCase
{
    /**
     * Check that the returned value of MFR only ever contains uppercase characters.
     *
     * @return void
     */
    public function testGetRLSSegmentMFROnlyContainsUppercaseChars()
    {
        $segment = factory(RLS_Segment::class)->states('all_fields_max_string_length')->make();
        
        $this->assertEquals($segment->get_RLS_MFR(), strtoupper($segment->MFR));
    }
}
