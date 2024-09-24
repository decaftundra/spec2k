<?php

namespace Tests\Unit;

use App\PieceParts\RPS_Segment;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class RPSSegmentTest extends TestCase
{
    /**
     * Check that the returned value of MFR only ever contains uppercase characters.
     *
     * @return void
     */
    public function testGetRPSSegmentMFROnlyContainsUppercaseChars()
    {
        $segment = factory(RPS_Segment::class)->states('all_fields_max_string_length')->make();
        
        $this->assertEquals($segment->get_RPS_MFR(), strtoupper($segment->MFR));
    }
}