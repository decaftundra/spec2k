<?php

namespace Tests\Unit;

use App\PieceParts\NHS_Segment;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class NHSSegmentTest extends TestCase
{
    /**
     * Check that the returned value of MFR only ever contains uppercase characters.
     *
     * @return void
     */
    public function testGetNHSSegmentMFROnlyContainsUppercaseChars()
    {
        $segment = factory(NHS_Segment::class)->states('all_fields_max_string_length')->make();
        
        $this->assertEquals($segment->get_NHS_MFR(), strtoupper($segment->MFR));
    }
}
