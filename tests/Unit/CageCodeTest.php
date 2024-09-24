<?php

namespace Tests\Unit;

use App\CageCode;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CageCodeTest extends TestCase
{
    /**
     * Test that the getPermittedValues function returns all the cage codes in upper and lower case.
     *
     * @return void
     */
    public function testGetPermittedCageCodeValues()
    {
        $cageCodesArray = CageCode::getPermittedValues();
        
        $cageCodes = CageCode::pluck('cage_code')->toArray();
        $uppercaseCodes = array_map('strtoupper', $cageCodes);
        $lowercaseCodes = array_map('strtolower', $cageCodes);
        $unknownCodes = ['ZZZZZ', 'zzzzz'];
        
        $codes = array_merge($uppercaseCodes, $lowercaseCodes, $unknownCodes);
        
        $this->assertEquals($cageCodesArray, $codes);
    }
}
