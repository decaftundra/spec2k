<?php

namespace Tests\Feature;

use App\HDR_Segment;
use App\Notification;
use App\NotificationPiecePart;
use App\PieceParts\NHS_Segment;
use App\PieceParts\PiecePart;
use App\PieceParts\PiecePartSegment;
use App\PieceParts\PiecePartDetail;
use App\PieceParts\RPS_Segment;
use App\PieceParts\WPS_Segment;
use App\ShopFindings\AID_Segment;
use App\ShopFindings\API_Segment;
use App\ShopFindings\ATT_Segment;
use App\ShopFindings\EID_Segment;
use App\ShopFindings\LNK_Segment;
use App\ShopFindings\Misc_Segment;
use App\ShopFindings\RCS_Segment;
use App\ShopFindings\RLS_Segment;
use App\ShopFindings\SAS_Segment;
use App\ShopFindings\ShopFinding;
use App\ShopFindings\ShopFindingsDetail;
use App\ShopFindings\SPT_Segment;
use App\ShopFindings\SUS_Segment;
use Carbon\Carbon;
use Tests\TestCase;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AutosaveTest extends TestCase
{
    use WithFaker;
    
    /**
     * Test the autosave command runs without any errors.
     *
     * @return void
     */
    public function testAutosaveCommand()
    {
        // Run the autosave.
        Artisan::call('spec2kapp:autosave_valid_segments');
        
        // There could possibly be output after first run of autosave.
        
        $notificationId = $this->faker->unique()->numberBetween(100000, 9999999999);
        
        // Create a valid notification with all segments.
        $notification = factory(Notification::class, 1)
            ->states('all_segments')
            ->create([
            'id' => $notificationId,
            'rcsSFI' => $notificationId,
            'status' => 'complete_shipped',
            'shipped_at' => Carbon::now()
        ]);
            
        $noOfPieceParts = mt_rand(1, 5);
        
        $counter = 0;
        
        while ($noOfPieceParts > 0) {
            $ppi = $this->faker->unique()->numberBetween(1, 9999999999);
            
            $notificationPieceParts = factory(NotificationPiecePart::class, 1)
                ->states('all_segments')
                ->create([
                'id' => $ppi,
                'notification_id' => $notification->first()->id,
                'wpsSFI' => $notification->first()->id,
                'wpsPPI' => $ppi
            ]);
            
            $noOfPieceParts--;
            $counter++;
        }
        
        $notification = Notification::with('pieceParts')->find($notification->first()->id);
        
        // Create a shopfinding record with just a header from that notification. No point checking the header as we don't save the dates.
        $this->actingAs($this->adminUser)
            ->call('GET', route('header.edit', $notification->get_RCS_SFI()))
            ->assertStatus(200);
            
        $header = factory(HDR_Segment::class)->make();
            
        $attributes = [
            'rcsSFI' => $notification->get_RCS_SFI(),
            'plant_code' => $notification->plant_code,
            'CHG' => $header->get_HDR_CHG(),
            'ROC' => $header->get_HDR_ROC(),
            'OPR' => $header->get_HDR_OPR(),
            'RON' => $header->get_HDR_RON(),
            'WHO' => $header->get_HDR_WHO(),
        ];
        
        $response = $this->actingAs($this->adminUser)
            ->call('POST', route('header.update', $notification->get_RCS_SFI()), $attributes);
        
        $this->followRedirects($response)->assertStatus(200);
        
        // Run the autosave again.
        Artisan::call('spec2kapp:autosave_valid_segments');
        
        $this->assertEquals('', trim(Artisan::output()));
        
        $shopFinding = ShopFinding::with('HDR_Segment')
            ->with('ShopFindingsDetail.RCS_Segment')
            ->with('ShopFindingsDetail.SAS_Segment')
            ->with('ShopFindingsDetail.SUS_Segment')
            ->with('ShopFindingsDetail.RLS_Segment')
            ->with('ShopFindingsDetail.LNK_Segment')
            ->with('ShopFindingsDetail.AID_Segment')
            ->with('ShopFindingsDetail.EID_Segment')
            ->with('ShopFindingsDetail.API_Segment')
            ->with('ShopFindingsDetail.ATT_Segment')
            ->with('ShopFindingsDetail.SPT_Segment')
            ->with('PiecePart.PiecePartDetails.WPS_Segment')
            ->with('PiecePart.PiecePartDetails.NHS_Segment')
            ->with('PiecePart.PiecePartDetails.RPS_Segment')
            ->findOrFail($notification->get_RCS_SFI());
        
        // Check the segments exist and shop finding is flagged as valid.
        $this->assertEquals($shopFinding->is_valid, 1);
        
        // Debug in case of failure.
        if (
            !$shopFinding->ShopFindingsDetail ||
            !$shopFinding->HDR_Segment ||
            !$shopFinding->ShopFindingsDetail->RCS_Segment ||
            !$shopFinding->ShopFindingsDetail->SAS_Segment ||
            !$shopFinding->ShopFindingsDetail->SUS_Segment ||
            !$shopFinding->ShopFindingsDetail->RLS_Segment ||
            !$shopFinding->ShopFindingsDetail->LNK_Segment ||
            !$shopFinding->ShopFindingsDetail->AID_Segment ||
            !$shopFinding->ShopFindingsDetail->EID_Segment ||
            !$shopFinding->ShopFindingsDetail->API_Segment ||
            !$shopFinding->ShopFindingsDetail->ATT_Segment ||
            !$shopFinding->ShopFindingsDetail->SPT_Segment ||
            !$shopFinding->PiecePart->PiecePartDetails
        ) {
            mydd($notification->toArray());
            mydd($shopFinding->toArray());
        }
        
        $this->assertEquals($shopFinding->ShopFindingsDetail->RCS_Segment->is_valid, 1);
        $this->assertFalse($shopFinding->ShopFindingsDetail->RCS_Segment->autosaved_at == NULL);
        
        $this->assertEquals($shopFinding->ShopFindingsDetail->SAS_Segment->is_valid, 1);
        $this->assertFalse($shopFinding->ShopFindingsDetail->SAS_Segment->autosaved_at == NULL);
        
        $this->assertEquals($shopFinding->ShopFindingsDetail->SUS_Segment->is_valid, 1);
        $this->assertFalse($shopFinding->ShopFindingsDetail->SUS_Segment->autosaved_at == NULL);
        
        $this->assertEquals($shopFinding->ShopFindingsDetail->RLS_Segment->is_valid, 1);
        $this->assertFalse($shopFinding->ShopFindingsDetail->RLS_Segment->autosaved_at == NULL);
        
        $this->assertEquals($shopFinding->ShopFindingsDetail->LNK_Segment->is_valid, 1);
        $this->assertFalse($shopFinding->ShopFindingsDetail->LNK_Segment->autosaved_at == NULL);
        
        $this->assertEquals($shopFinding->ShopFindingsDetail->AID_Segment->is_valid, 1);
        $this->assertFalse($shopFinding->ShopFindingsDetail->AID_Segment->autosaved_at == NULL);
        
        $this->assertEquals($shopFinding->ShopFindingsDetail->EID_Segment->is_valid, 1);
        $this->assertFalse($shopFinding->ShopFindingsDetail->EID_Segment->autosaved_at == NULL);
        
        $this->assertEquals($shopFinding->ShopFindingsDetail->API_Segment->is_valid, 1);
        $this->assertFalse($shopFinding->ShopFindingsDetail->API_Segment->autosaved_at == NULL);
        
        $this->assertEquals($shopFinding->ShopFindingsDetail->ATT_Segment->is_valid, 1);
        $this->assertFalse($shopFinding->ShopFindingsDetail->ATT_Segment->autosaved_at == NULL);
        
        $this->assertEquals($shopFinding->ShopFindingsDetail->SPT_Segment->is_valid, 1);
        $this->assertFalse($shopFinding->ShopFindingsDetail->SPT_Segment->autosaved_at == NULL);
        
        // Piece Parts...
        foreach ($shopFinding->PiecePart->PiecePartDetails as $piecePartDetails) {
            
            // Debug in case of failure.
            if (
                !$piecePartDetails->WPS_Segment ||
                !$piecePartDetails->NHS_Segment ||
                !$piecePartDetails->RPS_Segment
            ) {
                mydd($notification->toArray());
                mydd($shopFinding->toArray());
            }
            
            $this->assertEquals($piecePartDetails->WPS_Segment->is_valid, 1);
            $this->assertFalse($piecePartDetails->WPS_Segment->autosaved_at == NULL);
            
            $this->assertEquals($piecePartDetails->NHS_Segment->is_valid, 1);
            $this->assertFalse($piecePartDetails->NHS_Segment->autosaved_at == NULL);
            
            $this->assertEquals($piecePartDetails->RPS_Segment->is_valid, 1);
            $this->assertFalse($piecePartDetails->RPS_Segment->autosaved_at == NULL);
        }
    }
    
    // TO DO: test that engine information is autosaved if aircraft reg is given in SAP data.
    
    /**
     * Test that a notification imported from csv is autosaved.
     *
     * @return void
     */
    public function testAutosaveCsvImport()
    {
        $notificationId = $this->faker->unique()->numberBetween(100000, 9999999999);
        
        // Create a valid notification with all segments.
        $notification = factory(Notification::class, 1)
            ->states('all_segments')
            ->create([
            'id' => $notificationId,
            'rcsSFI' => $notificationId,
            'status' => 'complete_shipped',
            'shipped_at' => Carbon::now(),
            'is_csv_import' => true
        ]);
            
        $noOfPieceParts = mt_rand(1, 5);
        
        $counter = 0;
        
        while ($noOfPieceParts > 0) {
            $ppi = $this->faker->unique()->numberBetween(1, 9999999999);
            
            $notificationPieceParts = factory(NotificationPiecePart::class, 1)
                ->states('all_segments')
                ->create([
                'id' => $ppi,
                'notification_id' => $notification->first()->id,
                'wpsSFI' => $notification->first()->id,
                'wpsPPI' => $ppi
            ]);
            
            $noOfPieceParts--;
            $counter++;
        }
        
        $notification = Notification::with('pieceParts')->find($notification->first()->id);
        
        // Run the autosave again.
        Artisan::call('spec2kapp:autosave_csv_imports');
        
        $this->assertEquals('', trim(Artisan::output()));
        
        $shopFinding = ShopFinding::with('HDR_Segment')
            ->with('ShopFindingsDetail.RCS_Segment')
            ->with('ShopFindingsDetail.SAS_Segment')
            ->with('ShopFindingsDetail.SUS_Segment')
            ->with('ShopFindingsDetail.RLS_Segment')
            ->with('ShopFindingsDetail.LNK_Segment')
            ->with('ShopFindingsDetail.AID_Segment')
            ->with('ShopFindingsDetail.EID_Segment')
            ->with('ShopFindingsDetail.API_Segment')
            ->with('ShopFindingsDetail.ATT_Segment')
            ->with('ShopFindingsDetail.SPT_Segment')
            ->with('PiecePart.PiecePartDetails.WPS_Segment')
            ->with('PiecePart.PiecePartDetails.NHS_Segment')
            ->with('PiecePart.PiecePartDetails.RPS_Segment')
            ->findOrFail($notification->get_RCS_SFI());
        
        // Check the segments exist and shop finding is flagged as valid.
        $this->assertEquals($shopFinding->is_valid, 1);
        
        // Debug in case of failure.
        if (
            !$shopFinding->ShopFindingsDetail ||
            !$shopFinding->HDR_Segment ||
            !$shopFinding->ShopFindingsDetail->RCS_Segment ||
            !$shopFinding->ShopFindingsDetail->SAS_Segment ||
            !$shopFinding->ShopFindingsDetail->SUS_Segment ||
            !$shopFinding->ShopFindingsDetail->RLS_Segment ||
            !$shopFinding->ShopFindingsDetail->LNK_Segment ||
            !$shopFinding->ShopFindingsDetail->AID_Segment ||
            !$shopFinding->ShopFindingsDetail->EID_Segment ||
            !$shopFinding->ShopFindingsDetail->API_Segment ||
            !$shopFinding->ShopFindingsDetail->ATT_Segment ||
            !$shopFinding->ShopFindingsDetail->SPT_Segment ||
            !$shopFinding->PiecePart->PiecePartDetails
        ) {
            mydd($notification->toArray());
            mydd($shopFinding->toArray());
        }
        
        $this->assertEquals($shopFinding->ShopFindingsDetail->RCS_Segment->is_valid, 1);
        $this->assertFalse($shopFinding->ShopFindingsDetail->RCS_Segment->autosaved_at == NULL);
        
        $this->assertEquals($shopFinding->ShopFindingsDetail->SAS_Segment->is_valid, 1);
        $this->assertFalse($shopFinding->ShopFindingsDetail->SAS_Segment->autosaved_at == NULL);
        
        $this->assertEquals($shopFinding->ShopFindingsDetail->SUS_Segment->is_valid, 1);
        $this->assertFalse($shopFinding->ShopFindingsDetail->SUS_Segment->autosaved_at == NULL);
        
        $this->assertEquals($shopFinding->ShopFindingsDetail->RLS_Segment->is_valid, 1);
        $this->assertFalse($shopFinding->ShopFindingsDetail->RLS_Segment->autosaved_at == NULL);
        
        $this->assertEquals($shopFinding->ShopFindingsDetail->LNK_Segment->is_valid, 1);
        $this->assertFalse($shopFinding->ShopFindingsDetail->LNK_Segment->autosaved_at == NULL);
        
        $this->assertEquals($shopFinding->ShopFindingsDetail->AID_Segment->is_valid, 1);
        $this->assertFalse($shopFinding->ShopFindingsDetail->AID_Segment->autosaved_at == NULL);
        
        $this->assertEquals($shopFinding->ShopFindingsDetail->EID_Segment->is_valid, 1);
        $this->assertFalse($shopFinding->ShopFindingsDetail->EID_Segment->autosaved_at == NULL);
        
        $this->assertEquals($shopFinding->ShopFindingsDetail->API_Segment->is_valid, 1);
        $this->assertFalse($shopFinding->ShopFindingsDetail->API_Segment->autosaved_at == NULL);
        
        $this->assertEquals($shopFinding->ShopFindingsDetail->ATT_Segment->is_valid, 1);
        $this->assertFalse($shopFinding->ShopFindingsDetail->ATT_Segment->autosaved_at == NULL);
        
        $this->assertEquals($shopFinding->ShopFindingsDetail->SPT_Segment->is_valid, 1);
        $this->assertFalse($shopFinding->ShopFindingsDetail->SPT_Segment->autosaved_at == NULL);
        
        // Piece Parts...
        foreach ($shopFinding->PiecePart->PiecePartDetails as $piecePartDetails) {
            
            // Debug in case of failure.
            if (
                !$piecePartDetails->WPS_Segment ||
                !$piecePartDetails->NHS_Segment ||
                !$piecePartDetails->RPS_Segment
            ) {
                mydd($notification->toArray());
                mydd($shopFinding->toArray());
            }
            
            $this->assertEquals($piecePartDetails->WPS_Segment->is_valid, 1);
            $this->assertFalse($piecePartDetails->WPS_Segment->autosaved_at == NULL);
            
            $this->assertEquals($piecePartDetails->NHS_Segment->is_valid, 1);
            $this->assertFalse($piecePartDetails->NHS_Segment->autosaved_at == NULL);
            
            $this->assertEquals($piecePartDetails->RPS_Segment->is_valid, 1);
            $this->assertFalse($piecePartDetails->RPS_Segment->autosaved_at == NULL);
        }
    }
}
