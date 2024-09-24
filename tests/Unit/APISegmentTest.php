<?php

namespace Tests\Unit;

use App\ShopFindings\API_Segment;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class APISegmentTest extends TestCase
{
    /**
     * Check that the returned value of MFR only ever contains uppercase characters.
     *
     * @return void
     */
    public function testGetAPISegmentMFROnlyContainsUppercaseChars()
    {
        $segment = factory(API_Segment::class)->states('all_fields_max_string_length')->make();
        
        $this->assertEquals($segment->get_API_MFR(), strtoupper($segment->MFR));
    }
}
