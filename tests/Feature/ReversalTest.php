<?php

namespace Tests\Feature;

use App\Events\NotificationPiecePartReversal;
use App\Notification;
use App\NotificationPiecePart;
use App\PieceParts\WPS_Segment;
use App\ValidationProfiler;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Validator;
use Tests\TestCase;

class ReversalTest extends TestCase
{
    /**
     * Test reversal events are dispatched.
     *
     * @return void
     */
    public function testReversalEventsAreFired()
    {
        Event::fake();
        
        Artisan::call('spec2kapp:update_notifications_and_piece_parts');
        
        $this->assertEquals('', Artisan::output());
        
        $reversals = NotificationPiecePart::whereNotNull('reversal_id')->get();
        
        // Assert reversal events where fired.
        foreach ($reversals as $reversal) {
            Event::assertDispatched(NotificationPiecePartReversal::class, function($e) use ($reversal) {
                return $e->notificationPiecePart->id = $reversal->id;
            });
        }
    }
    
    /**
     * Test that the expected piece parts are removed.
     *
     * @return void
     */
    public function testReversalPiecePartsAreRemoved()
    {
        Artisan::call('spec2kapp:update_notifications_and_piece_parts');
        
        $this->assertEquals('', Artisan::output());
        
        $deleted = NotificationPiecePart::onlyTrashed()->pluck('wpsMPN', 'id')->toArray();
        
        /*
        000350411821	000045317955	49608235590008	31470900	(VM) O-RING, NITRILE		1.000	EA	0121002021	0009		X	49609052620007
        000350411821	000045317955	49608235590005	31576526	PLATE, CAUTION		1.000	EA	0121002021	0006		X	49609052620004
        000350411821	000045317955	49608235590009	19915909-3	GAUGE	73554	1.000	EA	0121002021	0010		X	49609052620008
        000350411821	000045317955	49608235590007	31576578-4	PLATE,DATA		1.000	EA	0121002021	0008		X	49609052620006
        000350411821	000045317955	49608235590003	33440018-7	CODE14 RUPTURE DISC ASSY		2.000	EA	0121002021	0004		X	49609177600001
        000350411821	000045317955	49608235590001	35240130-17	CODE25 FILL FTG ASSY		1.000	EA	0121002021	0002		X	49609052620001
        000350411821	000045317955	49608235590004	MS28774-011	RETAINER,PACKING BACKUP		2.000	EA	0121002021	0005		X	49609052620003
        000350411821	000045317955	49608235590002	MS28775-015	PACKING,PREFORMED		2.000	EA	0121002021	0003		X	49609052620002
        000350411821	000045317955	49608235590006	NAS1611-011	PACKING,PREFORMED		2.000	EA	0121002021	0007		X	49609052620005
        000350411821	000045317955	49608235590010	NAS1611-025A	(VM) PACKING, PREFORMED		2.000	EA	0121002021	0011		X	49609052620009
        */
        
        $expected = [
            '49608235590008' =>	'31470900',
            '49608235590005' =>	'31576526',
            '49608235590009' =>	'19915909-3',
            '49608235590007' =>	'31576578-4',
            '49608235590003' =>	'33440018-7',
            '49608235590001' =>	'35240130-17',
            '49608235590004' =>	'MS28774-011',
            '49608235590002' =>	'MS28775-015',
            '49608235590006' =>	'NAS1611-011',
            '49608235590010' =>	'NAS1611-025A',
            
            '49609052620007' => '31470900',
            '49609052620004' => '31576526',
            '49609052620008' => '19915909-3',
            '49609052620006' => '31576578-4',
            '49609177600001' => '33440018-7',
            '49609052620001' => '35240130-17',
            '49609052620003' => 'MS28774-011',
            '49609052620002' => 'MS28775-015',
            '49609052620005' => 'NAS1611-011',
            '49609052620009' => 'NAS1611-025A'
        ];
        
        foreach ($expected as $k => $v) {
            $this->assertTrue(array_key_exists($k, $deleted));
            $this->assertTrue(in_array($v, $deleted));
        }
    }
    
    /**
     * Test number of piece parts displays correctly in to do list.
     *
     * @params
     * @return
     */
    public function testReversalPiecePartCount()
    {
        $this->markTestSkipped('Piece part count is now omitted form the list.');
        
        /*
        000350411821	000045317955	49609052620007	31470900	(VM) O-RING, NITRILE		1.000	EA	0121002021	0009		
        000350411821	000045317955	49608233670008	31470900	(VM) O-RING, NITRILE		1.000	EA	0121002021	0009		
        000350411821	000045317955	49609052620004	31576526	PLATE, CAUTION		1.000	EA	0121002021	0006		
        000350411821	000045317955	49608233670005	31576526	PLATE, CAUTION		1.000	EA	0121002021	0006		
        000350411821	000045317955	49609052620008	19915909-3	GAUGE	73554	1.000	EA	0121002021	0010		
        000350411821	000045317955	49608233670009	19915909-3	GAUGE	73554	1.000	EA	0121002021	0010		
        000350411821	000045317955	49609052620006	31576578-4	PLATE,DATA		1.000	EA	0121002021	0008		
        000350411821	000045317955	49608233670007	31576578-4	PLATE,DATA		1.000	EA	0121002021	0008		
        000350411821	000045317955	49609177600001	33440018-7	CODE14 RUPTURE DISC ASSY		2.000	EA	0121002021	0004		
        000350411821	000045317955	49608233670003	33440018-7	CODE14 RUPTURE DISC ASSY		2.000	EA	0121002021	0004		
        000350411821	000045317955	49609052620001	35240130-17	CODE25 FILL FTG ASSY		1.000	EA	0121002021	0002		
        000350411821	000045317955	49608233670001	35240130-17	CODE25 FILL FTG ASSY		1.000	EA	0121002021	0002		
        000350411821	000045317955	49609052620003	MS28774-011	RETAINER,PACKING BACKUP		2.000	EA	0121002021	0005		
        000350411821	000045317955	49608233670004	MS28774-011	RETAINER,PACKING BACKUP		2.000	EA	0121002021	0005		
        000350411821	000045317955	49609052620002	MS28775-015	PACKING,PREFORMED		2.000	EA	0121002021	0003		
        000350411821	000045317955	49608233670002	MS28775-015	PACKING,PREFORMED		2.000	EA	0121002021	0003		
        000350411821	000045317955	49609052620005	NAS1611-011	PACKING,PREFORMED		2.000	EA	0121002021	0007		
        000350411821	000045317955	49608233670006	NAS1611-011	PACKING,PREFORMED		2.000	EA	0121002021	0007		
        000350411821	000045317955	49609052620009	NAS1611-025A	(VM) PACKING, PREFORMED		2.000	EA	0121002021	0011		
        000350411821	000045317955	49608233670010	NAS1611-025A	(VM) PACKING, PREFORMED		2.000	EA	0121002021	0011		
        000350411821	000045317955	49608235590008	31470900	(VM) O-RING, NITRILE		1.000	EA	0121002021	0009		X	49609052620007
        000350411821	000045317955	49608235590005	31576526	PLATE, CAUTION		1.000	EA	0121002021	0006		X	49609052620004
        000350411821	000045317955	49608235590009	19915909-3	GAUGE	73554	1.000	EA	0121002021	0010		X	49609052620008
        000350411821	000045317955	49608235590007	31576578-4	PLATE,DATA		1.000	EA	0121002021	0008		X	49609052620006
        000350411821	000045317955	49608235590003	33440018-7	CODE14 RUPTURE DISC ASSY		2.000	EA	0121002021	0004		X	49609177600001
        000350411821	000045317955	49608235590001	35240130-17	CODE25 FILL FTG ASSY		1.000	EA	0121002021	0002		X	49609052620001
        000350411821	000045317955	49608235590004	MS28774-011	RETAINER,PACKING BACKUP		2.000	EA	0121002021	0005		X	49609052620003
        000350411821	000045317955	49608235590002	MS28775-015	PACKING,PREFORMED		2.000	EA	0121002021	0003		X	49609052620002
        000350411821	000045317955	49608235590006	NAS1611-011	PACKING,PREFORMED		2.000	EA	0121002021	0007		X	49609052620005
        000350411821	000045317955	49608235590010	NAS1611-025A	(VM) PACKING, PREFORMED		2.000	EA	0121002021	0011		X	49609052620009
        */
        
        Artisan::call('spec2kapp:update_notifications_and_piece_parts');
        
        $this->assertEquals('', Artisan::output());
        
        $url = route('notifications.index') . '?roc=All&search=000350411821';
        
        $this->actingAs($this->dataAdminUser)
            ->call('GET', $url)
            ->assertStatus(200)
            ->assertSee('Displaying 1 to 1 of 1 notifications.')
            ->assertSee('000350411821')
            ->assertSee('<td class="piece-part-count">10</td>', false);
    }
    
    /**
     * Test number of piece parts displays correctly in in progress list.
     * Test that some piece parts are reversed.
     *
     * @return void
     */
    public function testInProgressReversalPiecePartCount()
    {
        $this->markTestSkipped('Piece part count is now omitted form the list.');
        
        $notification = $this->getEditableNotificationWithPieceParts($this->dataAdminUser, 12);
        
        // Create some piece part records from existing notification.
        $originalPiecePartCount = $notification->PieceParts->count();
        
        foreach ($notification->PieceParts as $piecePart) {
            $WPS_Segment = factory(WPS_Segment::class)->make();
            
            $attributes = [
                'rcsSFI' => $piecePart->get_WPS_SFI(),
                'plant_code' => Notification::findOrFail($piecePart->get_WPS_SFI())->plant_code,
                'SFI' => $piecePart->get_WPS_SFI(),
                'PPI' => $piecePart->get_WPS_PPI(),
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
                
            $attributes['piece_part_detail_id'] = $attributes['PPI'];
        
            unset($attributes['rcsSFI']); // Not recorded in DB.
            unset($attributes['MRD']); // Dates are sometimes 1 second out.
            unset($attributes['plant_code']); // Not recorded in DB.
            
            $this->assertDatabaseHas('WPS_Segments', $attributes);
        }
        
        // Check all the piece parts have been created.
        $this->assertEquals($originalPiecePartCount, WPS_Segment::where('SFI', $notification->id)->count());
        
        // Create some piece part reversals.

        $randomPiecePartsToReverse = $notification->PieceParts->random(3);
        
        $this->assertEquals(3, $randomPiecePartsToReverse->count());
        
        foreach ($randomPiecePartsToReverse as $pp) {
            factory(NotificationPiecePart::class)->create([
                'notification_id' => $pp->notification_id,
                'wpsSFI' => $pp->notification_id,
                'reversal_id' => $pp->wpsPPI
            ]);
        }
        
        // Trigger the events to remove the reversals.
        
        $allPiecePartsReversalIds = NotificationPiecePart::where('notification_id', $notification->id)->whereNotNull('reversal_id')->pluck('id')->toArray();
        
        //mydd('reversals: ' . count($allPiecePartsReversalIds));
        
        $toBeReversed = NotificationPiecePart::where('notification_id', $notification->id)->whereNotNull('reversal_id')->pluck('reversal_id')->toArray();
        
        //mydd('to  be reversed: ' . count($toBeReversed));
        
        Artisan::call('spec2kapp:sync_reversals', ['piecePartIds' => $allPiecePartsReversalIds]);
        
        $this->assertEquals('', Artisan::output());
        
        // Check the correct parts have been removed and the piece part count is correct.
        
        $reversed = WPS_Segment::onlyTrashed()->where('SFI', $notification->id)->get();
        
        if ($reversed->count() < 3) {
            mydd('Should be reversed:');
            mydd(WPS_Segment::withTrashed()->whereIn('PPI', $toBeReversed)->get()->toArray());
        }
        
        $this->assertEquals(3, $reversed->count());
        
        $this->assertEquals($originalPiecePartCount - 3, WPS_Segment::where('SFI', $notification->id)->count());
        
        $this->actingAs($this->dataAdminUser)
            ->call('GET', route('datasets.index') . '?search=' . $notification->id . '&pc=All')
            ->assertStatus(200)
            ->assertSee("Displaying 1 to 1 of 1 datasets.")
            ->assertSee('<td class="piece-part-count">' . ($originalPiecePartCount - 3) . '</td>', false);
            
        // Check the piece part count on the piece part index page as well.
        $this->actingAs($this->dataAdminUser)
            ->call('GET', route('piece-parts.index', $notification->id))
            ->assertStatus(200)
            ->assertSee(($originalPiecePartCount - 3) .  ' piece parts found.');
    }
    
    /**
     * Test all piece parts are reversed and new ones can be saved.
     *
     * @return void
     */
    /*
    public function testReversalAndReSave()
    {
        $notification = $this->getEditableNotificationWithPieceParts($this->dataAdminUser, 12);
        
        // Create some piece part records from existing notification.
        $originalPiecePartCount = $notification->PieceParts->count();
        
        foreach ($notification->PieceParts as $piecePart) {
            $WPS_Segment = factory(WPS_Segment::class)->make();
            
            $attributes = [
                'rcsSFI' => $piecePart->get_WPS_SFI(),
                'plant_code' => Notification::findOrFail($piecePart->get_WPS_SFI())->plant_code,
                'SFI' => $piecePart->get_WPS_SFI(),
                'PPI' => $piecePart->get_WPS_PPI(),
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
                
            $attributes['piece_part_detail_id'] = $attributes['PPI'];
        
            unset($attributes['rcsSFI']); // Not recorded in DB.
            unset($attributes['MRD']); // Dates are sometimes 1 second out.
            unset($attributes['plant_code']); // Not recorded in DB.
            
            $this->assertDatabaseHas('WPS_Segments', $attributes);
        }
        
        // Check all the piece parts have been created.
        $this->assertEquals($originalPiecePartCount, WPS_Segment::where('SFI', $notification->id)->count());
        
        // Create some piece part reversals.

        $randomPiecePartsToReverse = $notification->PieceParts;
        
        foreach ($randomPiecePartsToReverse as $pp) {
            factory(NotificationPiecePart::class)->create([
                'notification_id' => $pp->notification_id,
                'wpsSFI' => $pp->notification_id,
                'reversal_id' => $pp->wpsPPI
            ]);
        }
        
        // Trigger the events to remove the reversals.
        
        $allPiecePartsReversalIds = NotificationPiecePart::where('notification_id', $notification->id)->whereNotNull('reversal_id')->pluck('id')->toArray();
        
        $toBeReversed = NotificationPiecePart::where('notification_id', $notification->id)->whereNotNull('reversal_id')->pluck('reversal_id')->toArray();
        
        Artisan::call('spec2kapp:sync_reversals', ['piecePartIds' => $allPiecePartsReversalIds]);
        
        $this->assertEquals('', Artisan::output());
        
        // Check the correct parts have been removed and the piece part count is correct.
        
        $reversed = WPS_Segment::onlyTrashed()->where('SFI', $notification->id)->get();
        
        $this->assertEquals($originalPiecePartCount, $reversed->count());
        
        $this->assertEquals(0, WPS_Segment::where('SFI', $notification->id)->count());
        
        $this->actingAs($this->dataAdminUser)
            ->call('GET', route('datasets.index') . '?search=' . $notification->id . '&pc=All')
            ->assertStatus(200)
            ->assertSee("Displaying 1 to 1 of 1 datasets.")
            ->assertSee('<td class="piece-part-count">' . $originalPiecePartCount . '</td>', false);
            
        // Check the piece part count on the piece part index page as well.
        $this->actingAs($this->dataAdminUser)
            ->call('GET', route('piece-parts.index', $notification->id))
            ->assertStatus(200)
            ->assertSee($originalPiecePartCount .  ' piece parts found.');
            
        // Fetch the notification again and save the replacements.
        
        $notification = Notification::with('PieceParts')->find($notification->id);
        
        foreach ($notification->PieceParts as $piecePart) {
            $WPS_Segment = factory(WPS_Segment::class)->make();
            
            $attributes = [
                'rcsSFI' => $piecePart->get_WPS_SFI(),
                'plant_code' => $notification->plant_code,
                'SFI' => $piecePart->get_WPS_SFI(),
                'PPI' => $piecePart->get_WPS_PPI(),
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
                
            $attributes['piece_part_detail_id'] = $attributes['PPI'];
        
            unset($attributes['rcsSFI']); // Not recorded in DB.
            unset($attributes['MRD']); // Dates are sometimes 1 second out.
            unset($attributes['plant_code']); // Not recorded in DB.
            
            $this->assertDatabaseHas('WPS_Segments', $attributes);
        }
        
        // Check all the piece parts have been created.
        $this->assertEquals($originalPiecePartCount, WPS_Segment::where('SFI', $notification->id)->count());
        
        $this->actingAs($this->dataAdminUser)
            ->call('GET', route('datasets.index') . '?search=' . $notification->id . '&pc=All')
            ->assertStatus(200)
            ->assertSee("Displaying 1 to 1 of 1 datasets.")
            ->assertSee('<td class="piece-part-count">' . $originalPiecePartCount . '</td>', false);
            
        // Check the piece part count on the piece part index page as well.
        $this->actingAs($this->dataAdminUser)
            ->call('GET', route('piece-parts.index', $notification->id))
            ->assertStatus(200)
            ->assertSee($originalPiecePartCount .  ' piece parts found.');
    }
    */
    
    /**
     * Test number of piece parts displays correctly in the deleted and standby lists.
     *
     * @return void
     */
    public function testDeletedListReversalPiecePartCount()
    {
        $this->markTestSkipped('Piece part count is now omitted form the list.');
        
        $notification = $this->getEditableNotificationWithPieceParts($this->dataAdminUser, 12);
        
        // Create some piece part records from existing notification.
        $originalPiecePartCount = $notification->PieceParts->count();
        
        foreach ($notification->PieceParts as $piecePart) {
            $WPS_Segment = factory(WPS_Segment::class)->make();
            
            $attributes = [
                'rcsSFI' => $piecePart->get_WPS_SFI(),
                'plant_code' => Notification::findOrFail($piecePart->get_WPS_SFI())->plant_code,
                'SFI' => $piecePart->get_WPS_SFI(),
                'PPI' => $piecePart->get_WPS_PPI(),
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
                
            $attributes['piece_part_detail_id'] = $attributes['PPI'];
        
            unset($attributes['rcsSFI']); // Not recorded in DB.
            unset($attributes['MRD']); // Dates are sometimes 1 second out.
            unset($attributes['plant_code']); // Not recorded in DB.
            
            $this->assertDatabaseHas('WPS_Segments', $attributes);
        }
        
        // Check all the piece parts have been created.
        $this->assertEquals($originalPiecePartCount, WPS_Segment::where('SFI', $notification->id)->count());
        
        // Create some piece part reversals.

        $randomPiecePartsToReverse = $notification->PieceParts->random(3);
        
        $this->assertEquals(3, $randomPiecePartsToReverse->count());
        
        foreach ($randomPiecePartsToReverse as $pp) {
            factory(NotificationPiecePart::class)->create([
                'notification_id' => $pp->notification_id,
                'wpsSFI' => $pp->notification_id,
                'reversal_id' => $pp->wpsPPI
            ]);
        }
        
        // Trigger the events to remove the reversals.
        
        $allPiecePartsReversalIds = NotificationPiecePart::where('notification_id', $notification->id)->whereNotNull('reversal_id')->pluck('id')->toArray();
        
        $toBeReversed = NotificationPiecePart::where('notification_id', $notification->id)->whereNotNull('reversal_id')->pluck('reversal_id')->toArray();
        
        Artisan::call('spec2kapp:sync_reversals', ['piecePartIds' => $allPiecePartsReversalIds]);
        
        $this->assertEquals('', Artisan::output());
        
        // Check the correct parts have been removed and the piece part count is correct.
        
        $reversed = WPS_Segment::onlyTrashed()->where('SFI', $notification->id)->get();
        
        $this->assertEquals(3, $reversed->count());
        
        $this->assertEquals($originalPiecePartCount - 3, WPS_Segment::where('SFI', $notification->id)->count());
        
        // Delete the notification and check it shows in the deleted list.
        
        $response = $this->actingAs($this->dataAdminUser)->ajaxPost(route('status.delete'), ['id' => $notification->id]);
        
        $response->assertStatus(200)->assertJson(['success' => true]);
            
        $this->actingAs($this->dataAdminUser)
            ->call('GET', route('deleted.index') . '?search=' . $notification->id . '&pc=All')
            ->assertSee($notification->id)
            ->assertSee("Displaying 1 to 1 of 1 datasets.")
            ->assertSee('<td class="piece-part-count">' . ($originalPiecePartCount - 3) . '</td>', false)
            ->assertStatus(200);
            
        // Restore and then test standby.
        
        $response = $this->actingAs($this->dataAdminUser)->ajaxPost(route('status.restore'), ['id' => $notification->id]);
        
        $response->assertStatus(200)->assertJson(['success' => true]);
        
        $response = $this->actingAs($this->dataAdminUser)->ajaxPost(route('status.put-on-standby'), ['id' => $notification->id]);
        
        $response->assertStatus(200)->assertJson(['success' => true]);
        
        $this->actingAs($this->dataAdminUser)
            ->call('GET', route('standby.index') . '?search=' . $notification->id . '&pc=All')
            ->assertSee($notification->id)
            ->assertSee("Displaying 1 to 1 of 1 datasets.")
            ->assertSee('<td class="piece-part-count">' . ($originalPiecePartCount - 3) . '</td>', false)
            ->assertStatus(200);
            
        $response = $this->actingAs($this->dataAdminUser)->ajaxPost(route('status.remove-on-standby'), ['id' => $notification->id]);
        
        $response->assertStatus(200)->assertJson(['success' => true]);
        
        // Check export list.
        
        $postVars = [
            'validity' => 'all',
            'location' => 'all',
            'status' => [
                'in_progress',
                'subcontracted',
                'complete_scrapped',
                'complete_shipped'
            ],
            'notification_ids' => (string) $notification->id
        ];
        
        $this->actingAs($this->dataAdminUser)
            ->call('POST', route('reports.export'), $postVars)
            ->assertSee('Reports to export')
            ->assertSee("Displaying 1 to 1 of 1 datasets.")
            ->assertSee('<td>' . $notification->id . '</td>', false)
            ->assertSee('<td class="piece-part-count">' . ($originalPiecePartCount - 3) . '</td>', false)
            ->assertStatus(200);
    }
    
    /**
     * Test batch save and reversals.
     *
     * @return void
     */
    public function testBatchSavePartialReversalAndBatchSaveAgain()
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
            die('Killed test.');
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
        
        //mydd($pieceParts->toArray());
        
        $this->actingAs($this->dataAdminUser)
            ->call('GET', route('datasets.index') . '?search=' . $notificationId . '&pc=All')
            ->assertStatus(200)
            ->assertSee("Displaying 1 to 1 of 1 datasets.");
            //->assertSee('<td class="piece-part-count">' . $noOfPieceParts . '</td>', false);
            
        // Check the piece part count on the piece part index page as well.
        $response = $this->actingAs($this->dataAdminUser)
            ->call('GET', route('piece-parts.index', $notificationId))
            ->assertStatus(200)
            ->assertSee($noOfPieceParts .  ' piece parts found.');
            
        // Create some piece part reversals.
        
        $notification = Notification::with('pieceParts')->find($notificationId);
        
        //mydd($noOfPieceParts);
        
        //mydd(count($notification->PieceParts));
        
        $randomPiecePartsToReverse = $notification->PieceParts->random(3);
        
        $this->assertEquals(3, $randomPiecePartsToReverse->count());
        
        foreach ($randomPiecePartsToReverse as $pp) {
            factory(NotificationPiecePart::class)->create([
                'notification_id' => $pp->notification_id,
                'wpsSFI' => $pp->notification_id,
                'reversal_id' => $pp->wpsPPI
            ]);
        }
        
        //mydd($noOfPieceParts);
        
        //mydd(count($notification->PieceParts));
        
        //mydd(WPS_Segment::where('SFI', $notification->id)->count());
        
        // Trigger the events to remove the reversals.
        
        $allPiecePartsReversalIds = NotificationPiecePart::where('notification_id', $notification->id)->whereNotNull('reversal_id')->pluck('id')->toArray();
        
        $toBeReversed = NotificationPiecePart::where('notification_id', $notification->id)->whereNotNull('reversal_id')->pluck('reversal_id')->toArray();
        
        //mydd($allPiecePartsReversalIds);
        
        //mydd($toBeReversed);
        
        Artisan::call('spec2kapp:sync_reversals', ['piecePartIds' => $allPiecePartsReversalIds]);
        
        $this->assertEquals('', Artisan::output());
        
        // Check the correct parts have been removed and the piece part count is correct.
        
        $reversed = WPS_Segment::onlyTrashed()->where('SFI', $notification->id)->get();
        
        $this->assertEquals(3, $reversed->count());
        
        $this->assertEquals(($noOfPieceParts - 3), WPS_Segment::where('SFI', $notification->id)->count());
        
        $this->actingAs($this->dataAdminUser)
            ->call('GET', route('datasets.index') . '?search=' . $notification->id . '&pc=All')
            ->assertStatus(200)
            ->assertSee("Displaying 1 to 1 of 1 datasets.");
            //->assertSee('<td class="piece-part-count">' . ($noOfPieceParts - 3) . '</td>', false);
            
        // Check the piece part count on the piece part index page as well.
        
        $response = $this->actingAs($this->dataAdminUser)
            ->call('GET', route('piece-parts.index', $notification->id))
            ->assertStatus(200)
            ->assertSee(($noOfPieceParts - 3) .  ' piece parts found.');
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
            ->groupBy('notification_piece_parts.wpsSFI')
            ->having('count', '>', 5)
            ->having('count', '<', 30)
            ->inRandomOrder()
            ->first();
    }
}

/*

000350411821	000045317955	49609052620007	31470900	(VM) O-RING, NITRILE		1.000	EA	0121002021	0009		
000350411821	000045317955	49608233670008	31470900	(VM) O-RING, NITRILE		1.000	EA	0121002021	0009		
000350411821	000045317955	49609052620004	31576526	PLATE, CAUTION		1.000	EA	0121002021	0006		
000350411821	000045317955	49608233670005	31576526	PLATE, CAUTION		1.000	EA	0121002021	0006		
000350411821	000045317955	49609052620008	19915909-3	GAUGE	73554	1.000	EA	0121002021	0010		
000350411821	000045317955	49608233670009	19915909-3	GAUGE	73554	1.000	EA	0121002021	0010		
000350411821	000045317955	49609052620006	31576578-4	PLATE,DATA		1.000	EA	0121002021	0008		
000350411821	000045317955	49608233670007	31576578-4	PLATE,DATA		1.000	EA	0121002021	0008		
000350411821	000045317955	49609177600001	33440018-7	CODE14 RUPTURE DISC ASSY		2.000	EA	0121002021	0004		
000350411821	000045317955	49608233670003	33440018-7	CODE14 RUPTURE DISC ASSY		2.000	EA	0121002021	0004		
000350411821	000045317955	49609052620001	35240130-17	CODE25 FILL FTG ASSY		1.000	EA	0121002021	0002		
000350411821	000045317955	49608233670001	35240130-17	CODE25 FILL FTG ASSY		1.000	EA	0121002021	0002		
000350411821	000045317955	49609052620003	MS28774-011	RETAINER,PACKING BACKUP		2.000	EA	0121002021	0005		
000350411821	000045317955	49608233670004	MS28774-011	RETAINER,PACKING BACKUP		2.000	EA	0121002021	0005		
000350411821	000045317955	49609052620002	MS28775-015	PACKING,PREFORMED		2.000	EA	0121002021	0003		
000350411821	000045317955	49608233670002	MS28775-015	PACKING,PREFORMED		2.000	EA	0121002021	0003		
000350411821	000045317955	49609052620005	NAS1611-011	PACKING,PREFORMED		2.000	EA	0121002021	0007		
000350411821	000045317955	49608233670006	NAS1611-011	PACKING,PREFORMED		2.000	EA	0121002021	0007		
000350411821	000045317955	49609052620009	NAS1611-025A	(VM) PACKING, PREFORMED		2.000	EA	0121002021	0011		
000350411821	000045317955	49608233670010	NAS1611-025A	(VM) PACKING, PREFORMED		2.000	EA	0121002021	0011		

000350411821	000045317955	49608235590008	31470900	(VM) O-RING, NITRILE		1.000	EA	0121002021	0009		X	49609052620007
000350411821	000045317955	49608235590005	31576526	PLATE, CAUTION		1.000	EA	0121002021	0006		X	49609052620004
000350411821	000045317955	49608235590009	19915909-3	GAUGE	73554	1.000	EA	0121002021	0010		X	49609052620008
000350411821	000045317955	49608235590007	31576578-4	PLATE,DATA		1.000	EA	0121002021	0008		X	49609052620006
000350411821	000045317955	49608235590003	33440018-7	CODE14 RUPTURE DISC ASSY		2.000	EA	0121002021	0004		X	49609177600001
000350411821	000045317955	49608235590001	35240130-17	CODE25 FILL FTG ASSY		1.000	EA	0121002021	0002		X	49609052620001
000350411821	000045317955	49608235590004	MS28774-011	RETAINER,PACKING BACKUP		2.000	EA	0121002021	0005		X	49609052620003
000350411821	000045317955	49608235590002	MS28775-015	PACKING,PREFORMED		2.000	EA	0121002021	0003		X	49609052620002
000350411821	000045317955	49608235590006	NAS1611-011	PACKING,PREFORMED		2.000	EA	0121002021	0007		X	49609052620005
000350411821	000045317955	49608235590010	NAS1611-025A	(VM) PACKING, PREFORMED		2.000	EA	0121002021	0011		X	49609052620009



[49608233670001] => 35240130-17
[49608233670002] => MS28775-015
[49608233670003] => 33440018-7
[49608233670004] => MS28774-011
[49608233670005] => 31576526
[49608233670006] => NAS1611-011
[49608233670007] => 31576578-4
[49608233670008] => 31470900
[49608233670009] => 19915909-3
[49608235590010] => NAS1611-025A
[49609052620001] => 35240130-17
[49609052620002] => MS28775-015
[49609052620003] => MS28774-011
[49609052620004] => 31576526
[49609052620005] => NAS1611-011
[49609052620006] => 31576578-4
[49609052620007] => 31470900
[49609052620008] => 19915909-3
[49609052620009] => NAS1611-025A
[49609177600001] => 33440018-7

*/