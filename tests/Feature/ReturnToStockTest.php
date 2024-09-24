<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ReturnToStockTest extends TestCase
{
    /**
     * Test that a status update on complete_shipped and complete_scrapped doesn't update the status dates.
     */
    public function testReturnToStock()
    {
        $shipped = $this->createShopFindingsAndPieceParts(1, 'complete_shipped', 'valid');
        $scrapped = $this->createShopFindingsAndPieceParts(1, 'complete_scrapped', 'valid');
        
        $currentShippedDate = $shipped[0]->shipped_at;
        $currentScrappedDate = $scrapped[0]->scrapped_at;
        
        $statusDate = '2099-01-01 00:00:00';
        
        $shipped[0]->setStatus('complete_shipped', $statusDate);
        $scrapped[0]->setStatus('complete_scrapped', $statusDate);
        
        $this->assertTrue($shipped[0]->shipped_at->format('Y-m-d H:i:s') == $currentShippedDate->format('Y-m-d H:i:s'));
        $this->assertTrue($scrapped[0]->scrapped_at->format('Y-m-d H:i:s') == $currentScrappedDate->format('Y-m-d H:i:s'));
    }
}
