<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Notification;
use App\ValidationProfiler;
use App\PieceParts\PiecePart;
use App\PieceParts\WPS_Segment;
use App\PieceParts\NHS_Segment;
use App\PieceParts\RPS_Segment;
use App\ShopFindings\ShopFinding;
use App\NotificationPiecePart;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class PiecePartsTest extends TestCase
{
    /**
     * Test for a 200 response from the piece parts index.
     *
     * @return void
     */
    public function testPiecePartsIndex()
    {
        $notification = $this->getRandomNotificationWithMultiplePieceParts();
        
        $this->actingAs($this->user)
            ->call('GET', route('piece-parts.index', $notification->rcsSFI))
            ->assertStatus(200);
    }
    
    /**
     * Test that a user can't access the piece parts of a notification from another location.
     *
     * @return void
     */
    public function testUserCantAccessPiecePartsIndexForNotificationAtOtherLocation()
    {
        //$this->withoutExceptionHandling();
        
        $notification = $this->getRandomNotificationWithMultiplePiecePartsFromOtherLocation();
        
        $notification = Notification::where('rcsSFI', $notification->rcsSFI)->first();
        
        $this->actingAs($this->user)
            ->call('GET', route('piece-parts.index', $notification->rcsSFI))
            ->assertStatus(403);
    }
    
    /**
     * Test that admin can access the piece parts of a notification from another location.
     *
     * @return void
     */
    public function testAdminCanAccessPiecePartsIndexForNotificationAtOtherLocation()
    {
        $notification = $this->getRandomNotificationWithMultiplePiecePartsFromOtherLocation($this->adminUser);
        
        $this->actingAs($this->adminUser)
            ->call('GET', route('piece-parts.index', $notification->rcsSFI))
            ->assertStatus(200);
    }
    
    /**
     * Create a valid shopfinding record with a random amount of piece parts.
     * Assert that the piece parts index returns a 200 response, the record is valid and shows the correct number of piece parts.
     *
     * @return void
     */
    public function testShopFindingPiecePartsIndex()
    {
        $this->withoutExceptionHandling();
        
        $noOfPieceParts = mt_rand(1, 25);
        $this->createSingleShopFindingAndPieceParts($noOfPieceParts, $this->user);
        
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
            ->first();
        
        if (!$shopFinding->isValid()) {
            mydd($shopFinding->getValidationReport());
            
            mydd($shopFinding);
        }
        
        // Even though there should be a warning, this should not invalidate the shop finding.
        $this->assertTrue((bool) $shopFinding->isValid());
        
        $response = $this->actingAs($this->user)
            ->call('GET', route('piece-parts.index', $shopFinding->id))
            ->assertStatus(200)
            ->assertSee($noOfPieceParts . ' piece parts found.');
    }
    
    /**
     * Create a record that is invalid and should trigger a piece part fail code warning.
     *
     * @return void
     */
    public function testPiecePartsFailedCodeWarning()
    {
        $noOfPieceParts = mt_rand(1, 25);
        
        $shopFinding = $this->createSingleShopFindingAndPiecePartsThatShouldTriggerFailedCodeWarning($noOfPieceParts, $this->user);
        
        $shopFinding = ShopFinding::first();
        
        // Debug.
        if (!$shopFinding->isValid()) {
            mydd($shopFinding->toArray());
            mydd($shopFinding->getValidationReport());
        }
        
        // Even though there should be a warning, this should not invalidate the shop finding.
        $this->assertTrue((bool) $shopFinding->isValid());
        
        $this->actingAs($this->user)
            ->call('GET', route('piece-parts.index', $shopFinding->id))
            ->assertStatus(200)
            ->assertSee($noOfPieceParts . ' piece parts found.')
            ->assertSee(PiecePart::$warnings[PiecePart::FAILED]);
    }
    
    /**
     * Create a record that is invalid and should trigger a piece part fail code warning.
     *
     * @return void
     */
    public function testPiecePartsNotFailedCodeWarning()
    {
        $noOfPieceParts = mt_rand(1, 25);
        $shopFinding = $this->createSingleShopFindingAndPiecePartsThatShouldTriggerNotFailedCodeWarning($noOfPieceParts, $this->user);
        
        $shopFinding = ShopFinding::first();
        
        // Debug.
        if (!$shopFinding->isValid()) {
            mydd($shopFinding->toArray());
            mydd($shopFinding->getValidationReport());
        }
        
        // Even though a warning is triggered the shopfinding should still be valid.
        $this->assertTrue((bool) $shopFinding->isValid());
        
        $this->actingAs($this->user)
            ->call('GET', route('piece-parts.index', $shopFinding->id))
            ->assertStatus(200)
            ->assertSee($noOfPieceParts . ' piece parts found.')
            ->assertSee(PiecePart::$warnings[PiecePart::NOTFAILED]);
    }
    
    /**
     * Assert the session has errors if an empty form is submitted.
     *
     * @return void
     */
    public function testInvalidWPSFormSubmit()
    {
        $piecePart = $this->actingAs($this->user)->getRandomNotificationPiecePart();
        
        $WPS_Segment = factory(WPS_Segment::class)->make();
        
        $attributes = [
            'rcsSFI' => $piecePart->get_WPS_SFI(),
            'plant_code' => Notification::findOrFail($piecePart->get_WPS_SFI())->plant_code,
            'SFI' => $piecePart->get_WPS_SFI(),
            'PPI' => $WPS_Segment->get_WPS_PPI(),
        ];
        
        $response = $this->actingAs($this->user)
            ->call('POST', route('worked-piece-part.update', [$piecePart->get_WPS_SFI(), $piecePart->get_WPS_PPI()]), $attributes);
        
        $response->assertSessionHasErrors(['PFC', 'MPN', 'PDT']);
    }
    
    /**
     * Test that we get a 200 status from the WPS_Segment form.
     *
     * @return void
     */
    public function testWPS_SegmentForm()
    {
        $piecePart = $this->actingAs($this->user)->getRandomNotificationPiecePart();
        
        $this->actingAs($this->user)
            ->call('GET', route('worked-piece-part.edit', [$piecePart->get_WPS_SFI(), $piecePart->get_WPS_PPI()]))
            ->assertStatus(200);
    }
    
    /**
     * Test edit WPS_Segment.
     *
     * @return void
     */
    public function testEditWPS_Segment()
    {
        $piecePart = $this->actingAs($this->user)->getRandomNotificationPiecePart();
        
        $WPS_Segment = factory(WPS_Segment::class)->make();
        
        $attributes = [
            'rcsSFI' => $piecePart->get_WPS_SFI(),
            'plant_code' => Notification::findOrFail($piecePart->get_WPS_SFI())->plant_code,
            'SFI' => $piecePart->get_WPS_SFI(),
            'PPI' => $WPS_Segment->get_WPS_PPI(),
            'PFC' => $WPS_Segment->get_WPS_PFC(),
            'MFR' => $WPS_Segment->get_WPS_MFR(),
            'MFN' => $WPS_Segment->get_WPS_MFN(),
            'MPN' => $WPS_Segment->get_WPS_MPN(),
            'SER' => $WPS_Segment->get_WPS_SER(),
            'FDE' => $WPS_Segment->get_WPS_FDE(),
            'PNR' => $WPS_Segment->get_WPS_PNR(),
            'OPN' => $WPS_Segment->get_WPS_OPN(),
            'USN' => $WPS_Segment->get_WPS_USN(),
            'PDT' => $WPS_Segment->get_WPS_PDT(),
            'GEL' => $WPS_Segment->get_WPS_GEL(),
            'MRD' => $WPS_Segment->get_WPS_MRD(),
            'ASN' => $WPS_Segment->get_WPS_ASN(),
            'UCN' => $WPS_Segment->get_WPS_UCN(),
            'SPL' => $WPS_Segment->get_WPS_SPL(),
            'UST' => $WPS_Segment->get_WPS_UST(),
        ];
        
        $response = $this->actingAs($this->user)
            ->call('POST', route('worked-piece-part.update', [$piecePart->get_WPS_SFI(), $piecePart->get_WPS_PPI()]), $attributes);
            
        $response->assertStatus(302);
        
        $this->get($response->headers->get('Location'))
            ->assertSee('Worked Piece Part saved successfully!');
        
        unset($attributes['rcsSFI']); // Not recorded in DB.
        unset($attributes['MRD']); // Dates are sometimes 1 second out.
        unset($attributes['PPI']); // We are currently generating our own ids so not relevant.
        unset($attributes['plant_code']); // Not recorded in DB.
        
        $this->assertDatabaseHas('WPS_Segments', $attributes);
    }
    
    /**
     * Assert the session has errors if an empty form is submitted.
     *
     * @return void
     */
    public function testInvalidNHSFormSubmit()
    {
        $piecePart = $this->actingAs($this->user)->getRandomNotificationPiecePart();
        
        $attributes = [
            'rcsSFI' => $piecePart->get_WPS_SFI(),
            'plant_code' => Notification::findOrFail($piecePart->get_WPS_SFI())->plant_code,
        ];
        
        $response = $this->actingAs($this->user)
            ->call('POST', route('next-higher-assembly.update', [$piecePart->get_WPS_SFI(), $piecePart->get_WPS_PPI()]), $attributes);
        
        $response->assertSessionHasErrors(['MFR', 'MPN', 'SER']);
    }
    
    /**
     * Test edit NHS_Segment.
     *
     * @return void
     */
    public function testEditNHS_Segment()
    {
        $piecePart = $this->actingAs($this->user)->getRandomNotificationPiecePart();
        
        $NHS_Segment = factory(NHS_Segment::class)->make();
        
        $attributes = [
            'rcsSFI' => $piecePart->get_WPS_SFI(),
            'plant_code' => Notification::findOrFail($piecePart->get_WPS_SFI())->plant_code,
            'MFR' => $NHS_Segment->get_NHS_MFR(),
            'MPN' => $NHS_Segment->get_NHS_MPN(),
            'SER' => $NHS_Segment->get_NHS_SER(),
            'MFN' => $NHS_Segment->get_NHS_MFN(),
            'PNR' => $NHS_Segment->get_NHS_PNR(),
            'OPN' => $NHS_Segment->get_NHS_OPN(),
            'USN' => $NHS_Segment->get_NHS_USN(),
            'PDT' => $NHS_Segment->get_NHS_PDT(),
            'ASN' => $NHS_Segment->get_NHS_ASN(),
            'UCN' => $NHS_Segment->get_NHS_UCN(),
            'SPL' => $NHS_Segment->get_NHS_SPL(),
            'UST' => $NHS_Segment->get_NHS_UST(),
            'NPN' => $NHS_Segment->get_NHS_NPN(),
        ];
        
        $response = $this->actingAs($this->user)
            ->call('POST', route('next-higher-assembly.update', [$piecePart->get_WPS_SFI(), $piecePart->get_WPS_PPI()]), $attributes);
            
        $response->assertStatus(302);
        
        $this->get($response->headers->get('Location'))
            ->assertSee('Next Higher Assembly saved successfully!');
        
        unset($attributes['rcsSFI']); // Not recorded in DB.
        unset($attributes['plant_code']); // Not recorded in DB.
        
        $this->assertDatabaseHas('NHS_Segments', $attributes);
    }
    
    /**
     * Assert the session has errors if an empty form is submitted.
     *
     * @return void
     */
    public function testInvalidRPSFormSubmit()
    {
        $piecePart = $this->actingAs($this->user)->getRandomNotificationPiecePart();
        
        $attributes = [
            'rcsSFI' => $piecePart->get_WPS_SFI(),
            'plant_code' => Notification::findOrFail($piecePart->get_WPS_SFI())->plant_code,
        ];
        
        $response = $this->actingAs($this->user)
            ->call('POST', route('replaced-piece-part.update', [$piecePart->get_WPS_SFI(), $piecePart->get_WPS_PPI()]), $attributes);
        
        $response->assertSessionHasErrors(['MPN']);
    }
    
    /**
     * Test edit RPS_Segment.
     *
     * @return void
     */
    public function testEditRPS_Segment()
    {
        $piecePart = $this->actingAs($this->user)->getRandomNotificationPiecePart();
        
        $RPS_Segment = factory(RPS_Segment::class)->make();
        
        $attributes = [
            'rcsSFI' => $piecePart->get_WPS_SFI(),
            'plant_code' => Notification::findOrFail($piecePart->get_WPS_SFI())->plant_code,
            'MPN' => $RPS_Segment->get_RPS_MPN(),
            'MFR' => $RPS_Segment->get_RPS_MFR(),
            'MFN' => $RPS_Segment->get_RPS_MFN(),
            'SER' => $RPS_Segment->get_RPS_SER(),
            'PNR' => $RPS_Segment->get_RPS_PNR(),
            'OPN' => $RPS_Segment->get_RPS_OPN(),
            'USN' => $RPS_Segment->get_RPS_USN(),
            'ASN' => $RPS_Segment->get_RPS_ASN(),
            'UCN' => $RPS_Segment->get_RPS_UCN(),
            'SPL' => $RPS_Segment->get_RPS_SPL(),
            'UST' => $RPS_Segment->get_RPS_UST(),
            'PDT' => $RPS_Segment->get_RPS_PDT(),
        ];
        
        $response = $this->actingAs($this->user)
            ->call('POST', route('replaced-piece-part.update', [$piecePart->get_WPS_SFI(), $piecePart->get_WPS_PPI()]), $attributes);
            
        $response->assertStatus(302);
        
        $this->get($response->headers->get('Location'))
            ->assertSee('Replaced Piece Part saved successfully!');
        
        unset($attributes['rcsSFI']); // Not recorded in DB.
        unset($attributes['plant_code']); // Not recorded in DB.
        
        $this->assertDatabaseHas('RPS_Segments', $attributes);
    }
    
    /**
     * Assert that batch saving multiple Piece Parts saves valid items to the database.
     *
     * @return void
     */
    public function testBatchPiecePartsSave()
    {
        $notificationInfoWithMultiplePieceParts = $this->getRandomNotificationWithMultiplePieceParts($this->user);
        
        $notificationId = $notificationInfoWithMultiplePieceParts->wpsSFI;
        $noOfPieceParts = $notificationInfoWithMultiplePieceParts->count;
        
        $pieceParts = NotificationPiecePart::where('wpsSFI', $notificationId)->get();
        $piecePartIds = $pieceParts->pluck('wpsPPI')->toArray();
        
        $WPS_Segments = factory(WPS_Segment::class, $noOfPieceParts)->make();
        
        $attributes = [];
        
        $count = 0;
        
        foreach ($WPS_Segments as $segment) {
            $attributes[$piecePartIds[$count]]['SFI'] = $notificationId;
            $attributes[$piecePartIds[$count]]['PPI'] = $piecePartIds[$count];
            $attributes[$piecePartIds[$count]]['PFC'] = $segment->get_WPS_PFC();
            $attributes[$piecePartIds[$count]]['MPN'] = $segment->get_WPS_MPN();
            $attributes[$piecePartIds[$count]]['MFR'] = $segment->get_WPS_MFR();
            $attributes[$piecePartIds[$count]]['SER'] = $segment->get_WPS_SER();
            $attributes[$piecePartIds[$count]]['MFN'] = $segment->get_WPS_MFN();
            $attributes[$piecePartIds[$count]]['FDE'] = $segment->get_WPS_FDE();
            $attributes[$piecePartIds[$count]]['PNR'] = $segment->get_WPS_PNR();
            $attributes[$piecePartIds[$count]]['USN'] = $segment->get_WPS_USN();
            $attributes[$piecePartIds[$count]]['OPN'] = $segment->get_WPS_OPN();
            $attributes[$piecePartIds[$count]]['PDT'] = $segment->get_WPS_PDT();
            $attributes[$piecePartIds[$count]]['GEL'] = $segment->get_WPS_GEL();
            $attributes[$piecePartIds[$count]]['MRD'] = $segment->get_WPS_MRD();
            $attributes[$piecePartIds[$count]]['ASN'] = $segment->get_WPS_ASN();
            $attributes[$piecePartIds[$count]]['UCN'] = $segment->get_WPS_UCN();
            $attributes[$piecePartIds[$count]]['SPL'] = $segment->get_WPS_SPL();
            $attributes[$piecePartIds[$count]]['UST'] = $segment->get_WPS_UST();
            
            $count++;
        }
        
        $wpsprofiler = new ValidationProfiler('WPS_Segment', $WPS_Segments->first(), $attributes[$piecePartIds[0]]['SFI']);
        
        foreach ($attributes as $k => $atts) {
            $validator = Validator::make($atts, $wpsprofiler->getValidationRules($atts['PPI']));
            $validatedConditionally = $wpsprofiler->conditionalValidation($validator);
        
            $valid = $validatedConditionally->fails() ? false : true;
            
            if (!$valid) {
                mydd('Woah there, invalid wps segment in test!');
                mydd($atts,1);
            }
        }
        
        $response = $this->actingAs($this->user)
            ->call('POST', route('piece-parts.update', $notificationId), $attributes);
            
        $errors = session('errorsArray');
    
        /*if (!empty($errors)) {
            mydd($errors);
            mydd($attributes);
            //die('Killed test.');
        }*/
            
        $response->assertStatus(302);
        
        // We won't know if all the NHS and RPS Segments are valid so assert response accordingly.
        
        if (empty($errors)) {
            $this->get($response->headers->get('Location'))
                ->assertSee('All Piece Parts saved successfully!');
        } else {
            $this->get($response->headers->get('Location'))
                ->assertSee('Some Piece Parts contained errors, please see below.');
        }
        
        foreach ($attributes as $k => $att) {
            unset($attributes[$k]['MRD']); // unset date field;
        }
        
        foreach ($attributes as $k => $atts) {
            $this->assertDatabaseHas('WPS_Segments', $atts);
        }
        
        $nhsprofiler = new ValidationProfiler('NHS_Segment', $pieceParts->first(), $pieceParts->first()->notification_id);
        
        foreach ($pieceParts->toArray() as $k => $att1) {
            $NHS_Array = [];
            $NHS_Array['MFR'] = $att1['nhsMFR'];
            $NHS_Array['MPN'] = $att1['nhsMPN'];
            $NHS_Array['SER'] = $att1['nhsSER'];
            $NHS_Array['MFN'] = $att1['nhsMFN'];
            $NHS_Array['PNR'] = $att1['nhsPNR'];
            $NHS_Array['OPN'] = $att1['nhsOPN'];
            $NHS_Array['USN'] = $att1['nhsUSN'];
            $NHS_Array['PDT'] = $att1['nhsPDT'];
            $NHS_Array['ASN'] = $att1['nhsASN'];
            $NHS_Array['UCN'] = $att1['nhsUCN'];
            $NHS_Array['SPL'] = $att1['nhsSPL'];
            $NHS_Array['UST'] = $att1['nhsUST'];
            $NHS_Array['NPN'] = $att1['nhsNPN'];
            
            $validator = Validator::make($NHS_Array, $nhsprofiler->getValidationRules($att1['id']));
            $validatedConditionally = $nhsprofiler->conditionalValidation($validator);
            $valid = $validatedConditionally->fails() ? false : true;
            
            if ($valid) {
                $this->assertDatabaseHas('NHS_Segments', ['piece_part_detail_id' => $att1['wpsPPI'], 'MFR' => $att1['nhsMFR'], 'SER' => $att1['nhsSER']]);
            } else {
                $this->assertDatabaseMissing('NHS_Segments', ['piece_part_detail_id' => $att1['wpsPPI'], 'MFR' => $att1['nhsMFR'], 'SER' => $att1['nhsSER']]);
            }
        }
        
        $rpsprofiler = new ValidationProfiler('RPS_Segment', $pieceParts->first(), $pieceParts->first()->notification_id);
        
        foreach ($pieceParts->toArray() as $k => $att2) {
            $RPS_Array = [];
            $RPS_Array['MPN'] = $att2['rpsMPN'];
            $RPS_Array['MFR'] = $att2['rpsMFR'];
            $RPS_Array['MFN'] = $att2['rpsMFN'];
            $RPS_Array['SER'] = $att2['rpsSER'];
            $RPS_Array['PNR'] = $att2['rpsPNR'];
            $RPS_Array['OPN'] = $att2['rpsOPN'];
            $RPS_Array['USN'] = $att2['rpsUSN'];
            $RPS_Array['ASN'] = $att2['rpsASN'];
            $RPS_Array['UCN'] = $att2['rpsUCN'];
            $RPS_Array['SPL'] = $att2['rpsSPL'];
            $RPS_Array['UST'] = $att2['rpsUST'];
            $RPS_Array['PDT'] = $att2['rpsPDT'];
            
            $validator = Validator::make($RPS_Array, $rpsprofiler->getValidationRules($att2['id']));
            $validatedConditionally = $rpsprofiler->conditionalValidation($validator);
            $valid = $validatedConditionally->fails() ? false : true;
            
            if ($valid) {
                $this->assertDatabaseHas('RPS_Segments', ['piece_part_detail_id' => $att2['wpsPPI'], 'MPN' => $att2['rpsMPN']]);
            } else {
                $this->assertDatabaseMissing('RPS_Segments', ['piece_part_detail_id' => $att2['wpsPPI'], 'MPN' => $att2['rpsMPN']]);
            }
        }
    }
    
    /**
     * Test batch save of WPS Segments with some invalid ones in there.
     *
     * @return void
     */
    public function testBatchInvalidWPSUpdate()
    {
        $notificationInfoWithMultiplePieceParts = $this->getRandomNotificationWithMultiplePieceParts($this->user);
        
        $notificationId = $notificationInfoWithMultiplePieceParts->wpsSFI;
        $noOfPieceParts = $notificationInfoWithMultiplePieceParts->count;
        
        $pieceParts = NotificationPiecePart::where('wpsSFI', $notificationId)->get();
        $piecePartIds = $pieceParts->pluck('wpsPPI')->toArray();
        
        $WPS_Segments = factory(WPS_Segment::class, $noOfPieceParts)->make();
        
        $attributes = [];
        
        $count = 0;
        
        foreach ($WPS_Segments as $segment) {
            
            $attributes[$piecePartIds[$count]]['SFI'] = $notificationId;
            $attributes[$piecePartIds[$count]]['PPI'] = $piecePartIds[$count];
            
            if (($count > 1) && ($count % 2 == 0)) {
                $attributes[$piecePartIds[$count]]['PFC'] = $segment->get_WPS_PFC();
                $attributes[$piecePartIds[$count]]['MPN'] = $segment->get_WPS_MPN();
            }
            
            $attributes[$piecePartIds[$count]]['MFR'] = $segment->get_WPS_MFR();
            $attributes[$piecePartIds[$count]]['SER'] = $segment->get_WPS_SER();
            $attributes[$piecePartIds[$count]]['MFN'] = $segment->get_WPS_MFN();
            $attributes[$piecePartIds[$count]]['FDE'] = $segment->get_WPS_FDE();
            $attributes[$piecePartIds[$count]]['PNR'] = $segment->get_WPS_PNR();
            $attributes[$piecePartIds[$count]]['USN'] = $segment->get_WPS_USN();
            $attributes[$piecePartIds[$count]]['OPN'] = $segment->get_WPS_OPN();
            
            if (($count > 1) && ($count % 2 == 0)) {
                $attributes[$piecePartIds[$count]]['PDT'] = $segment->get_WPS_PDT();
            }
            
            $attributes[$piecePartIds[$count]]['GEL'] = $segment->get_WPS_GEL();
            $attributes[$piecePartIds[$count]]['MRD'] = $segment->get_WPS_MRD();
            $attributes[$piecePartIds[$count]]['ASN'] = $segment->get_WPS_ASN();
            $attributes[$piecePartIds[$count]]['UCN'] = $segment->get_WPS_UCN();
            $attributes[$piecePartIds[$count]]['SPL'] = $segment->get_WPS_SPL();
            $attributes[$piecePartIds[$count]]['UST'] = $segment->get_WPS_UST();
            
            $count++;
        }
        
        $response = $this->actingAs($this->user)
            ->call('POST', route('piece-parts.update', $notificationId), $attributes);
            
        $response->assertStatus(302);
        
        $this->get($response->headers->get('Location'))
            ->assertSee('Some Piece Parts contained errors, please see below.');
        
        $wpsprofiler = new ValidationProfiler('WPS_Segment', $WPS_Segments->first(), $attributes[$piecePartIds[0]]['SFI']);
        
        foreach ($attributes as $k => $atts) {
            $validator = Validator::make($atts, $wpsprofiler->getValidationRules($atts['PPI']));
            $validatedConditionally = $wpsprofiler->conditionalValidation($validator);
        
            $valid = $validatedConditionally->fails() ? false : true;
            
            unset($atts['MRD']); // unset date field;
            
            if ($valid) {
                $this->assertDatabaseHas('WPS_Segments', $atts);
            } else {
                $this->assertDatabaseMissing('WPS_Segments', $atts);
            }
        }
        
        $nhsprofiler = new ValidationProfiler('NHS_Segment', $pieceParts->first(), $pieceParts->first()->notification_id);
        
        foreach ($pieceParts->toArray() as $k => $att1) {
            $NHS_Array = [];
            $NHS_Array['MFR'] = $att1['nhsMFR'];
            $NHS_Array['MPN'] = $att1['nhsMPN'];
            $NHS_Array['SER'] = $att1['nhsSER'];
            $NHS_Array['MFN'] = $att1['nhsMFN'];
            $NHS_Array['PNR'] = $att1['nhsPNR'];
            $NHS_Array['OPN'] = $att1['nhsOPN'];
            $NHS_Array['USN'] = $att1['nhsUSN'];
            $NHS_Array['PDT'] = $att1['nhsPDT'];
            $NHS_Array['ASN'] = $att1['nhsASN'];
            $NHS_Array['UCN'] = $att1['nhsUCN'];
            $NHS_Array['SPL'] = $att1['nhsSPL'];
            $NHS_Array['UST'] = $att1['nhsUST'];
            $NHS_Array['NPN'] = $att1['nhsNPN'];
            
            $validator = Validator::make($NHS_Array, $nhsprofiler->getValidationRules($att1['id']));
            $validatedConditionally = $nhsprofiler->conditionalValidation($validator);
        
            $valid = $validatedConditionally->fails() ? false : true;
            
            if ($valid) {
                $this->assertDatabaseHas('NHS_Segments', ['piece_part_detail_id' => $att1['wpsPPI'], 'MFR' => $att1['nhsMFR'], 'SER' => $att1['nhsSER']]);
            } else {
                $this->assertDatabaseMissing('NHS_Segments', ['piece_part_detail_id' => $att1['wpsPPI'], 'MFR' => $att1['nhsMFR'], 'SER' => $att1['nhsSER']]);
            } 
        }
        
        $rpsprofiler = new ValidationProfiler('RPS_Segment', $pieceParts->first(), $pieceParts->first()->notification_id);
        
        foreach ($pieceParts->toArray() as $k => $att2) {
            $RPS_Array = [];
            $RPS_Array['MPN'] = $att2['rpsMPN'];
            $RPS_Array['MFR'] = $att2['rpsMFR'];
            $RPS_Array['MFN'] = $att2['rpsMFN'];
            $RPS_Array['SER'] = $att2['rpsSER'];
            $RPS_Array['PNR'] = $att2['rpsPNR'];
            $RPS_Array['OPN'] = $att2['rpsOPN'];
            $RPS_Array['USN'] = $att2['rpsUSN'];
            $RPS_Array['ASN'] = $att2['rpsASN'];
            $RPS_Array['UCN'] = $att2['rpsUCN'];
            $RPS_Array['SPL'] = $att2['rpsSPL'];
            $RPS_Array['UST'] = $att2['rpsUST'];
            $RPS_Array['PDT'] = $att2['rpsPDT'];
            
            $validator = Validator::make($RPS_Array, $rpsprofiler->getValidationRules($att2['id']));
            $validatedConditionally = $rpsprofiler->conditionalValidation($validator);
        
            $valid = $validatedConditionally->fails() ? false : true;
            
            if ($valid) {
                $this->assertDatabaseHas('RPS_Segments', ['piece_part_detail_id' => $att2['wpsPPI'], 'MPN' => $att2['rpsMPN']]);
            } else {
                $this->assertDatabaseMissing('RPS_Segments', ['piece_part_detail_id' => $att2['wpsPPI'], 'MPN' => $att2['rpsMPN']]);
            }
        }
        
        // Make sure the missing WPS Segments are noted in the validation report.
        $shopFinding = ShopFinding::find($notificationId);
        
        $this->assertTrue(is_string(stristr($shopFinding->getValidationReport(), 'Piece Part Details: Not all mandatory Piece Part Detail segments are saved yet')));
    }
    
    /**
     * Get a random notification that has piece parts associated.
     *
     * @return App\NotificationPiecePart
     */
    public function getRandomNotificationPiecePart()
    {
        return NotificationPiecePart::whereHas('Notification', function($query){
            $query->where('plant_code', auth()->user()->location->plant_code);

        })
        ->whereNotNull('wpsPPI')
        ->whereNotNull('nhsMPN')
        ->whereNotNull('rpsMPN')
        ->inRandomOrder()
        ->first();
    }
    
    /**
     * Get the notification id and number of piece parts of a random notification with multiple piece parts.
     *
     * @return stdClass
     */
    public function getRandomNotificationWithMultiplePieceParts($user = NULL)
    {
        if (!$user) {
            $user = $this->user;
        }
        
        return DB::table('notifications')
            ->select(
                'notification_piece_parts.wpsSFI',
                DB::raw("COUNT(notification_piece_parts.wpsPPI) as count"),
                'notifications.rcsSFI'
            )
            ->leftJoin('notification_piece_parts', 'notification_piece_parts.wpsSFI', '=', 'notifications.rcsSFI')
            ->where('notifications.plant_code', $user->location->plant_code)
            ->whereNotNull('notification_piece_parts.wpsPPI')
            ->whereNotNull('notification_piece_parts.nhsMFR')
            ->whereNotNull('notification_piece_parts.nhsSER')
            ->whereNotNull('notification_piece_parts.rpsMPN')
            ->groupBy('notification_piece_parts.wpsSFI')
            ->having('count', '>', 1)
            ->having('count', '<', 30)
            ->inRandomOrder()
            ->first();
    }
    
    /**
     * Get the notification id and number of piece parts of a random notification with multiple piece parts.
     *
     * @return stdClass
     */
    public function getRandomNotificationWithMultiplePiecePartsFromOtherLocation($user = NULL)
    {
        if (!$user) {
            $user = $this->user;
        }
        
        return DB::table('notifications')
            ->select(
                'notification_piece_parts.wpsSFI',
                DB::raw("COUNT(notification_piece_parts.wpsPPI) as count"),
                'notifications.rcsSFI'
            )
            ->leftJoin('notification_piece_parts', 'notification_piece_parts.wpsSFI', '=', 'notifications.rcsSFI')
            ->where('notifications.plant_code', '!=', $user->location->plant_code)
            ->whereNotNull('notification_piece_parts.wpsPPI')
            ->whereNotNull('notification_piece_parts.nhsMFR')
            ->whereNotNull('notification_piece_parts.nhsSER')
            ->whereNotNull('notification_piece_parts.rpsMPN')
            ->groupBy('notification_piece_parts.wpsSFI')
            ->having('count', '>', 1)
            ->having('count', '<', 30)
            ->inRandomOrder()
            ->first();
    }
    
    /**
     * Test that the segment can be deleted.
     *
     * @return void
     */
    public function testDeleteWPS_Segment()
    {
        $this->createSingleShopFindingAndPiecePartsWithAllSegments(1, $this->adminUser);
        
        $segment = WPS_Segment::inRandomOrder()->first();
        
        $segmentId = $segment->PPI;
        
        $this->actingAs($this->adminUser)
            ->ajaxPost(route('worked-piece-part.destroy', $segmentId))
            ->assertStatus(200);
            
        $this->assertDatabaseMissing('WPS_Segments', ['PPI' => $segmentId]);
    }
    
    /**
     * Test that the segment can be deleted.
     *
     * @return void
     */
    public function testDeleteNHS_Segment()
    {
        $this->createSingleShopFindingAndPiecePartsWithAllSegments(1, $this->adminUser);
        
        $segment = NHS_Segment::inRandomOrder()->first();
        
        $segmentId = $segment->id;
        
        $this->actingAs($this->adminUser)
            ->ajaxPost(route('next-higher-assembly.destroy', $segmentId))
            ->assertStatus(200);
            
        $this->assertDatabaseMissing('NHS_Segments', ['id' => $segmentId]);
    }
    
    /**
     * Test that the segment can be deleted.
     *
     * @return void
     */
    public function testDeleteRPS_Segment()
    {
        $this->createSingleShopFindingAndPiecePartsWithAllSegments(1, $this->adminUser);
        
        $segment = RPS_Segment::inRandomOrder()->first();
        
        $segmentId = $segment->id;
        
        $this->actingAs($this->adminUser)
            ->ajaxPost(route('replaced-piece-part.destroy', $segmentId))
            ->assertStatus(200);
            
        $this->assertDatabaseMissing('RPS_Segments', ['id' => $segmentId]);
    }
}