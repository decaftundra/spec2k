<?php

namespace Tests;

use Cache;
use App\User;
use App\Location;
use App\UtasCode;
use Carbon\Carbon;
use App\HDR_Segment;
use App\Codes\RcsFailureCode;
use App\PieceParts\PiecePart;
use App\PieceParts\WPS_Segment;
use App\PieceParts\NHS_Segment;
use App\PieceParts\RPS_Segment;
use App\ShopFindings\AID_Segment;
use App\ShopFindings\EID_Segment;
use App\ShopFindings\API_Segment;
use App\ShopFindings\RCS_Segment;
use App\ShopFindings\SAS_Segment;
use App\ShopFindings\SUS_Segment;
use App\ShopFindings\RLS_Segment;
use App\ShopFindings\LNK_Segment;
use App\ShopFindings\ATT_Segment;
use App\ShopFindings\SPT_Segment;
use App\ShopFindings\MISC_Segment;
use App\ShopFindings\ShopFinding;
use Illuminate\Support\Facades\DB;
use App\PieceParts\PiecePartDetail;
use Illuminate\Support\Facades\Schema;
use App\ShopFindings\ShopFindingsDetail;
use App\Notification;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

//use XmlStructureTrait;

abstract class TestCase extends BaseTestCase
{
    use WithFaker, CreatesApplication, DatabaseTransactions;
    
    protected $dataAdminUser;
    protected $siteAdminUser;
    protected $adminUser;
    protected $user;
    protected $inactiveUser;
    
    protected static $setUpRun = false;
    
    public function setUp() :void
    {
        parent::setUp();
        
        /*
        |-----------------------------------------------------------------------------------------------
        | If you get a 'Base table or view not found' error:
        |-----------------------------------------------------------------------------------------------
        | 1. Import a local dump of the database into the TEST database.
        | 2. Run composer dump-autoload.
        | 3. Uncomment the two lines below and run a single test.
        |
        */
        
        //\Artisan::call('migrate:fresh');
        //\Artisan::call('migrate:refresh', ['--seed' => true]);
        
        if (!static::$setUpRun) {
            \Artisan::call('migrate:refresh', ['--seed' => true]);
            static::$setUpRun = true;
        }
        
        $this->dataAdminUser = User::dataAdmins()->firstOrFail();
        $this->siteAdminUser = User::siteAdmins()->firstOrFail();
        $this->adminUser = User::admins()->firstOrFail();
        $this->user = User::users()->firstOrFail();
        $this->inactiveUser = User::inactives()->firstOrFail();
    }
    
    public function tearDown() :void
    {
        parent::tearDown();
    }
    
    /**
     * Make ajax POST request
     */
    protected function ajaxPost($uri, array $data = [])
    {
        return $this->post($uri, $data, array('HTTP_X-Requested-With' => 'XMLHttpRequest'));
    }

    /**
     * Make ajax GET request
     */
    protected function ajaxGet($uri, array $data = [])
    {
        return $this->get($uri, array('HTTP_X-Requested-With' => 'XMLHttpRequest'));
    }
    
    /**
     * Make ajax DELETE request
     */
    protected function ajaxDelete($uri, array $data = [])
    {
        return $this->delete($uri, array('HTTP_X-Requested-With' => 'XMLHttpRequest'));
    }
    
    /**
     * Get the notifications and cache them.
     *
     * @return Illuminate\Support\Collection $notifications
     */
    protected function getNotifications()
    {
        return Notification::orderBy('rcsSFI', 'asc')->get();
    }
    
    /**
     * Get a notification that the specified user can edit.
     *
     * @param \App\User $user
     * @return \App\Notification
     */
    protected function getEditableNotification($user = NULL)
    {
        if (!$user) {
            $user = $this->user;
        }
        
        return Notification::where('plant_code', $user->location->plant_code)
            ->inRandomOrder()
            ->first();
    }
    
    /**
     * Get a notification that the specified user can edit.
     *
     * @param \App\User $user
     * @return \App\Notification
     */
    protected function getEditableNotificationWithPieceParts($user = NULL, $minPieceParts = 1)
    {
        if (!$user) {
            $user = $this->user;
        }
        
        return Notification::with('pieceParts')
            ->whereHas('pieceParts', function($query) use ($minPieceParts) {
                $query->whereNull('reversal_id');
            }, '>=', $minPieceParts)
            ->where('plant_code', $user->location->plant_code)
            ->inRandomOrder()
            ->first();
    }
    
    /**
     * Get a collins notification that the specified user can edit.
     *
     * @param \App\User $user
     * @return \App\Notification
     */
    protected function getEditableCollinsNotification($user = NULL)
    {
        if (!$user) {
            $user = $this->user;
        }
        
        $utasParts = UtasCode::getAllUtasCodes();
        
        return Notification::where('plant_code', $user->location->plant_code)
            ->whereIn('rcsMPN', $utasParts)
            ->inRandomOrder()
            ->first();
    }
    
    /**
     * Get a notification from a different location to the user.
     *
     * @param (type) $name
     * @return
     */
    protected function getNotificationNotInUsersLocation($user = NULL)
    {
        if (!$user) {
            $user = $this->user;
        }
        
        return Notification::where('plant_code', '!=', $user->location->plant_code)
            ->inRandomOrder()
            ->first();
    }
    
    /**
     * Get shopfindings and cache them.
     *
     * @return Illuminate\Support\Collection $shopFindings
     */
    protected function getShopFindings()
    {
        if (Cache::has('shopFindings')) {
            $shopFindings = Cache::get('shopFindings');
        } else {
            $shopFindings = ShopFinding::orderBy('id', 'asc')->get();
            
            Cache::put('shopFindings', $shopFindings, 60);
        }
        
        return $shopFindings;
    }
    
    /**
     * Create a given number shop findings with random number of piece parts.
     *
     * @param  (int) $numberOfShopFindings
     * @return \Illuminate\Database\Eloquent\Collection $ShopFindings
     */
    protected function createShopFindingsWithPieceParts($numberOfShopFindings = 1, $user = NULL)
    {
        if (!$user) {
            $user = $this->user;
        }
        
        ShopFinding::flushEventListeners(); // Prevents activities being recorded during seeding.
        
        $ShopFindings = factory(ShopFinding::class, $numberOfShopFindings)->create(['plant_code' => $user->location->plant_code])
        ->each(function($sf) use ($user) {
            
            // Random number of piece parts.
            $noOfPieceParts = mt_rand(0, 10);
            
            $sf->HDR_Segment()->save(factory(HDR_Segment::class)->make([
                'shop_finding_id' => (string) $sf->id,
                'RON' => $user->location->name
            ]));
            
            $sf->ShopFindingsDetail()->saveMany(
                factory(ShopFindingsDetail::class, 1)->create(['shop_finding_id' => (string) $sf->id])
                ->each(function($sfd) use($sf) {
                    $sfd->RCS_Segment()->save(
                        factory(RCS_Segment::class)->make(['shop_findings_detail_id' => $sfd->id, 'SFI' => (string) $sf->id])
                    );
                    
                    $sfd->SAS_Segment()->save(
                        factory(SAS_Segment::class)->make(['shop_findings_detail_id' => $sfd->id])
                    );
                    
                    if (mt_rand(0,1)) {
                        $sfd->AID_Segment()->save(
                            factory(AID_Segment::class)->make(['shop_findings_detail_id' => $sfd->id])
                        );
                    }
                    
                    if (mt_rand(0,1)) {
                        $sfd->EID_Segment()->save(
                            factory(EID_Segment::class)->make(['shop_findings_detail_id' => $sfd->id])
                        );
                    }
                    
                    if (mt_rand(0,1)) {
                        $sfd->API_Segment()->save(
                            factory(API_Segment::class)->make(['shop_findings_detail_id' => $sfd->id])
                        );
                    }
                    
                    if (mt_rand(0,1)) {
                        $sfd->SUS_Segment()->save(
                            factory(SUS_Segment::class)->make(['shop_findings_detail_id' => $sfd->id])
                        );
                    }
                    
                    if (mt_rand(0,1)) {
                        $sfd->RLS_Segment()->save(
                            factory(RLS_Segment::class)->make(['shop_findings_detail_id' => $sfd->id])
                        );
                    }
                    
                    if (mt_rand(0,1)) {
                        $sfd->LNK_Segment()->save(
                            factory(LNK_Segment::class)->make(['shop_findings_detail_id' => $sfd->id])
                        );
                    }
                    
                    if (mt_rand(0,1)) {
                        $sfd->ATT_Segment()->save(
                            factory(ATT_Segment::class)->make(['shop_findings_detail_id' => $sfd->id])
                        );
                    }
                    
                    if (mt_rand(0,1)) {
                        $sfd->SPT_Segment()->save(
                            factory(SPT_Segment::class)->make(['shop_findings_detail_id' => $sfd->id])
                        );
                    }
                })
            );
            
            if ($noOfPieceParts) {
                $sf->PiecePart()->saveMany(
                    factory(PiecePart::class, 1)->create(['shop_finding_id' => $sf->id])
                    ->each(function($pp) use($noOfPieceParts, $sf) {
                        $pp->PiecePartDetails()->saveMany(
                            factory(PiecePartDetail::class, $noOfPieceParts)->create(['piece_part_id' => $pp->id])
                            ->each(function($ppd) use ($sf) {
                                $ppd->WPS_Segment()->save(
                                    factory(WPS_Segment::class)->make([
                                        'piece_part_detail_id' => $ppd->id,
                                        'SFI' => $sf->id,
                                        'PPI' => $ppd->id
                                    ])
                                );
                                        
                                if (mt_rand(0,1)) {
                                    $ppd->NHS_Segment()->save(
                                        factory(NHS_Segment::class)->make(['piece_part_detail_id' => $ppd->id])
                                    );
                                }
                                        
                                if (mt_rand(0,1)) {
                                    $ppd->RPS_Segment()->save(
                                        factory(RPS_Segment::class)->make(['piece_part_detail_id' => $ppd->id])
                                    );
                                }
                            })
                        );
                    })
                );
            }
        });
        
        ShopFinding::boot();
        
        return $ShopFindings;
    }
    
    /**
     * Create a given number of valid and complete shopfindings with random numbers of piece parts.
     *
     * @param  (int) $numberOfShopFindings
     * @return \Illuminate\Database\Eloquent\Collection $ShopFindings
     */
    protected function createValidAndCompleteShopFindingsWithPieceParts($numberOfShopFindings = 1, $user = NULL)
    {
        if (!$user) {
            $user = $this->user;
        }
        
        ShopFinding::flushEventListeners(); // Prevents activities being recorded during seeding.
        
        $ShopFindings = factory(ShopFinding::class, $numberOfShopFindings)->create([
            'plant_code' => $user->location->plant_code,
            'status' => 'complete_shipped',
            'shipped_at' => date('Y-m-d H:i:s')
        ])
        ->each(function($sf) use ($user) {
            
            // Random number of piece parts.
            $noOfPieceParts = mt_rand(0, 10);
            
            $sf->HDR_Segment()->save(factory(HDR_Segment::class)->make([
                'shop_finding_id' => (string) $sf->id,
                'RON' => $user->location->name
            ]));
            
            $sf->ShopFindingsDetail()->saveMany(
                factory(ShopFindingsDetail::class, 1)->create(['shop_finding_id' => (string) $sf->id])
                ->each(function($sfd) use($sf) {
                    $sfd->RCS_Segment()->save(
                        factory(RCS_Segment::class)->make(['shop_findings_detail_id' => $sfd->id, 'SFI' => (string) $sf->id])
                    );
                    
                    $sfd->SAS_Segment()->save(
                        factory(SAS_Segment::class)->make(['shop_findings_detail_id' => $sfd->id])
                    );
                    
                    if (mt_rand(0,1)) {
                        $sfd->AID_Segment()->save(
                            factory(AID_Segment::class)->make(['shop_findings_detail_id' => $sfd->id])
                        );
                    }
                    
                    if (mt_rand(0,1)) {
                        $sfd->EID_Segment()->save(
                            factory(EID_Segment::class)->make(['shop_findings_detail_id' => $sfd->id])
                        );
                    }
                    
                    if (mt_rand(0,1)) {
                        $sfd->API_Segment()->save(
                            factory(API_Segment::class)->make(['shop_findings_detail_id' => $sfd->id])
                        );
                    }
                    
                    if (mt_rand(0,1)) {
                        $sfd->SUS_Segment()->save(
                            factory(SUS_Segment::class)->make(['shop_findings_detail_id' => $sfd->id])
                        );
                    }
                    
                    if (mt_rand(0,1)) {
                        $sfd->RLS_Segment()->save(
                            factory(RLS_Segment::class)->make(['shop_findings_detail_id' => $sfd->id])
                        );
                    }
                    
                    if (mt_rand(0,1)) {
                        $sfd->LNK_Segment()->save(
                            factory(LNK_Segment::class)->make(['shop_findings_detail_id' => $sfd->id])
                        );
                    }
                    
                    if (mt_rand(0,1)) {
                        $sfd->ATT_Segment()->save(
                            factory(ATT_Segment::class)->make(['shop_findings_detail_id' => $sfd->id])
                        );
                    }
                    
                    if (mt_rand(0,1)) {
                        $sfd->SPT_Segment()->save(
                            factory(SPT_Segment::class)->make(['shop_findings_detail_id' => $sfd->id])
                        );
                    }
                })
            );
            
            if ($noOfPieceParts) {
                $sf->PiecePart()->saveMany(
                    factory(PiecePart::class, 1)->create(['shop_finding_id' => $sf->id])
                    ->each(function($pp) use($noOfPieceParts, $sf) {
                        $pp->PiecePartDetails()->saveMany(
                            factory(PiecePartDetail::class, $noOfPieceParts)->create(['piece_part_id' => $pp->id])
                            ->each(function($ppd) use ($sf) {
                                $ppd->WPS_Segment()->save(
                                    factory(WPS_Segment::class)->make([
                                        'piece_part_detail_id' => $ppd->id,
                                        'SFI' => $sf->id,
                                        'PPI' => $ppd->id,
                                        'PFC' => 'Y'
                                    ])
                                );
                                
                                if (mt_rand(0,1)) {
                                    $ppd->NHS_Segment()->save(
                                        factory(NHS_Segment::class)->make(['piece_part_detail_id' => $ppd->id])
                                    );
                                }
                                        
                                if (mt_rand(0,1)) {
                                    $ppd->RPS_Segment()->save(
                                        factory(RPS_Segment::class)->make(['piece_part_detail_id' => $ppd->id])
                                    );
                                }
                            })
                        );
                    })
                );
            }
        });
        
        ShopFinding::boot();
        
        return $ShopFindings;
    }
    
    /**
     * Create given number of Shop Findings and Piece Parts with given status.
     *
     * @param (int) $numberOfShopFindings
     * @param (string) $status
     * @param (string) $validity
     * @param (string) $locationCode
     * @param \Carbon\Carbon $from
     * @param \Carbon\Carbon $to
     * @return \Illuminate\Database\Eloquent\Collection $ShopFindings
     */
    protected function createShopFindingsAndPieceParts(
        $numberOfShopFindings = 1,
        $status = 'in_progress',
        $validity = 'valid',
        $locationCode = NULL,
        Carbon $from = NULL,
        Carbon $to = NULL
    )
    {
        $from = $from ?: Carbon::now();
        $to = $to ?: Carbon::now();
        
        ShopFinding::flushEventListeners(); // Prevents activities being recorded during seeding.
        
        switch ($status) {
            case 'complete_shipped':
                $attributes = [
                    'status' => 'complete_shipped',
                    'shipped_at' => $this->faker->dateTimeBetween($from->format('Y-m-d 00:00:00'), $to->format('Y-m-d 23:59:59'))->format('Y-m-d 00:00:00')
                ];
                break;
            case 'complete_scrapped':
                $attributes = [
                    'status' => 'complete_scrapped',
                    'scrapped_at' => $this->faker->dateTimeBetween($from->format('Y-m-d 00:00:00'), $to->format('Y-m-d 23:59:59'))->format('Y-m-d 00:00:00')
                ];
                break;
            case 'subcontracted':
                $attributes = [
                    'status' => 'subcontracted',
                    'subcontracted_at' => $this->faker->dateTimeBetween($from->format('Y-m-d 00:00:00'), $to->format('Y-m-d 23:59:59'))->format('Y-m-d 00:00:00')
                ];
                break;
            default:
                $attributes = ['status' => 'in_progress'];
                break;
        }
        
        // If there's no plant code pick a random one.
        $attributes['plant_code'] = !$locationCode ? Location::inRandomOrder()->first()->plant_code : $locationCode;
        
        $ShopFindings = factory(ShopFinding::class, $numberOfShopFindings)->create($attributes)
        ->each(function($sf) use ($validity, $locationCode, $to, $from) {
            
            // Random number of piece parts.
            $noOfPieceParts = mt_rand(0, 10);
            
            if (!$locationCode) {
                $location = Location::inRandomOrder()->first();
            } else {
                $location = Location::where('plant_code', $locationCode)->firstOrFail();
            }
            
            $attributes = [
                'shop_finding_id' => (string) $sf->id,
                'RON' => $location->name
            ];
            
            $sf->HDR_Segment()->save(factory(HDR_Segment::class)->make($attributes));
            
            $sf->ShopFindingsDetail()->saveMany(
                factory(ShopFindingsDetail::class, 1)->create(['shop_finding_id' => (string) $sf->id])
                ->each(function($sfd) use($sf, $validity) {
                    
                    // Leaving out this segment will invalidate the shop finding.
                    if ($validity == 'valid') {
                        $sfd->RCS_Segment()->save(
                            factory(RCS_Segment::class)->make(['shop_findings_detail_id' => $sfd->id, 'SFI' => (string) $sf->id])
                        );
                    }
                    
                    $sfd->SAS_Segment()->save(
                        factory(SAS_Segment::class)->make(['shop_findings_detail_id' => $sfd->id])
                    );
                    
                    if (mt_rand(0,1)) {
                        $sfd->AID_Segment()->save(
                            factory(AID_Segment::class)->make(['shop_findings_detail_id' => $sfd->id])
                        );
                    }
                    
                    if (mt_rand(0,1)) {
                        $sfd->EID_Segment()->save(
                            factory(EID_Segment::class)->make(['shop_findings_detail_id' => $sfd->id])
                        );
                    }
                    
                    if (mt_rand(0,1)) {
                        $sfd->API_Segment()->save(
                            factory(API_Segment::class)->make(['shop_findings_detail_id' => $sfd->id])
                        );
                    }
                    
                    if (mt_rand(0,1)) {
                        $sfd->SUS_Segment()->save(
                            factory(SUS_Segment::class)->make(['shop_findings_detail_id' => $sfd->id])
                        );
                    }
                    
                    if (mt_rand(0,1)) {
                        $sfd->RLS_Segment()->save(
                            factory(RLS_Segment::class)->make(['shop_findings_detail_id' => $sfd->id])
                        );
                    }
                    
                    if (mt_rand(0,1)) {
                        $sfd->LNK_Segment()->save(
                            factory(LNK_Segment::class)->make(['shop_findings_detail_id' => $sfd->id])
                        );
                    }
                    
                    if (mt_rand(0,1)) {
                        $sfd->ATT_Segment()->save(
                            factory(ATT_Segment::class)->make(['shop_findings_detail_id' => $sfd->id])
                        );
                    }
                    
                    if (mt_rand(0,1)) {
                        $sfd->SPT_Segment()->save(
                            factory(SPT_Segment::class)->make(['shop_findings_detail_id' => $sfd->id])
                        );
                    }
                })
            );
            
            if ($noOfPieceParts) {
                $sf->PiecePart()->saveMany(
                    factory(PiecePart::class, 1)->create(['shop_finding_id' => $sf->id])
                    ->each(function($pp) use($noOfPieceParts, $sf) {
                        $pp->PiecePartDetails()->saveMany(
                            factory(PiecePartDetail::class, $noOfPieceParts)->create(['piece_part_id' => $pp->id])
                            ->each(function($ppd) use ($sf) {
                                $ppd->WPS_Segment()->save(
                                    factory(WPS_Segment::class)->make([
                                        'piece_part_detail_id' => $ppd->id,
                                        'SFI' => $sf->id,
                                        'PPI' => $ppd->id,
                                        'PFC' => 'Y'
                                    ])
                                );
                                
                                if (mt_rand(0,1)) {
                                    $ppd->NHS_Segment()->save(
                                        factory(NHS_Segment::class)->make(['piece_part_detail_id' => $ppd->id])
                                    );
                                }
                                        
                                if (mt_rand(0,1)) {
                                    $ppd->RPS_Segment()->save(
                                        factory(RPS_Segment::class)->make(['piece_part_detail_id' => $ppd->id])
                                    );
                                }
                            })
                        );
                    })
                );
            }
        });
        
        ShopFinding::boot();
        
        return $ShopFindings;
    }
    
    /**
     * Create given number of invalid Shop Findings and Piece Parts with missing WPS Segment.
     *
     * @param (int) $numberOfShopFindings
     * @param (string) $status
     * @param (string) $validity
     * @param (string) $locationCode
     * @param \Carbon\Carbon $from
     * @param \Carbon\Carbon $to
     * @return \Illuminate\Database\Eloquent\Collection $ShopFindings
     */
    protected function createShopFindingsAndPiecePartsWithoutWPSSegment(
        $numberOfShopFindings = 1,
        $status = 'in_progress',
        $validity = 'invalid',
        $locationCode = NULL,
        Carbon $from = NULL,
        Carbon $to = NULL
    )
    {
        $from = $from ?: Carbon::now();
        $to = $to ?: Carbon::now();
        
        ShopFinding::flushEventListeners(); // Prevents activities being recorded during seeding.
        
        switch ($status) {
            case 'complete_shipped':
                $attributes = [
                    'status' => 'complete_shipped',
                    'shipped_at' => $this->faker->dateTimeBetween($from->format('Y-m-d 00:00:00'), $to->format('Y-m-d 23:59:59'))->format('Y-m-d 00:00:00')
                ];
                break;
            case 'complete_scrapped':
                $attributes = [
                    'status' => 'complete_scrapped',
                    'scrapped_at' => $this->faker->dateTimeBetween($from->format('Y-m-d 00:00:00'), $to->format('Y-m-d 23:59:59'))->format('Y-m-d 00:00:00')
                ];
                break;
            case 'subcontracted':
                $attributes = [
                    'status' => 'subcontracted',
                    'subcontracted_at' => $this->faker->dateTimeBetween($from->format('Y-m-d 00:00:00'), $to->format('Y-m-d 23:59:59'))->format('Y-m-d 00:00:00')
                ];
                break;
            default:
                $attributes = ['status' => 'in_progress'];
                break;
        }
        
        // If there's no plant code pick a random one.
        $attributes['plant_code'] = !$locationCode ? Location::inRandomOrder()->first()->plant_code : $locationCode;
        
        $ShopFindings = factory(ShopFinding::class, $numberOfShopFindings)->create($attributes)
        ->each(function($sf) use ($validity, $locationCode, $to, $from) {
            
            // Random number of piece parts.
            $noOfPieceParts = mt_rand(0, 10);
            
            if (!$locationCode) {
                $location = Location::inRandomOrder()->first();
            } else {
                $location = Location::where('plant_code', $locationCode)->firstOrFail();
            }
            
            $attributes = [
                'shop_finding_id' => (string) $sf->id,
                'RON' => $location->name
            ];
            
            $sf->HDR_Segment()->save(factory(HDR_Segment::class)->make($attributes));
            
            $sf->ShopFindingsDetail()->saveMany(
                factory(ShopFindingsDetail::class, 1)->create(['shop_finding_id' => (string) $sf->id])
                ->each(function($sfd) use($sf, $validity) {
                    
                    // Leaving out this segment will invalidate the shop finding.
                    if ($validity == 'valid') {
                        $sfd->RCS_Segment()->save(
                            factory(RCS_Segment::class)->make(['shop_findings_detail_id' => $sfd->id, 'SFI' => (string) $sf->id])
                        );
                    }
                    
                    $sfd->SAS_Segment()->save(
                        factory(SAS_Segment::class)->make(['shop_findings_detail_id' => $sfd->id])
                    );
                    
                    if (mt_rand(0,1)) {
                        $sfd->AID_Segment()->save(
                            factory(AID_Segment::class)->make(['shop_findings_detail_id' => $sfd->id])
                        );
                    }
                    
                    if (mt_rand(0,1)) {
                        $sfd->EID_Segment()->save(
                            factory(EID_Segment::class)->make(['shop_findings_detail_id' => $sfd->id])
                        );
                    }
                    
                    if (mt_rand(0,1)) {
                        $sfd->API_Segment()->save(
                            factory(API_Segment::class)->make(['shop_findings_detail_id' => $sfd->id])
                        );
                    }
                    
                    if (mt_rand(0,1)) {
                        $sfd->SUS_Segment()->save(
                            factory(SUS_Segment::class)->make(['shop_findings_detail_id' => $sfd->id])
                        );
                    }
                    
                    if (mt_rand(0,1)) {
                        $sfd->RLS_Segment()->save(
                            factory(RLS_Segment::class)->make(['shop_findings_detail_id' => $sfd->id])
                        );
                    }
                    
                    if (mt_rand(0,1)) {
                        $sfd->LNK_Segment()->save(
                            factory(LNK_Segment::class)->make(['shop_findings_detail_id' => $sfd->id])
                        );
                    }
                    
                    if (mt_rand(0,1)) {
                        $sfd->ATT_Segment()->save(
                            factory(ATT_Segment::class)->make(['shop_findings_detail_id' => $sfd->id])
                        );
                    }
                    
                    if (mt_rand(0,1)) {
                        $sfd->SPT_Segment()->save(
                            factory(SPT_Segment::class)->make(['shop_findings_detail_id' => $sfd->id])
                        );
                    }
                })
            );
            
            if ($noOfPieceParts) {
                $sf->PiecePart()->saveMany(
                    factory(PiecePart::class, 1)->create(['shop_finding_id' => $sf->id])
                    ->each(function($pp) use($noOfPieceParts, $sf) {
                        $pp->PiecePartDetails()->saveMany(
                            factory(PiecePartDetail::class, $noOfPieceParts)->create(['piece_part_id' => $pp->id])
                            ->each(function($ppd) use ($sf) {
                                if (mt_rand(0,1)) {
                                    $ppd->NHS_Segment()->save(
                                        factory(NHS_Segment::class)->make(['piece_part_detail_id' => $ppd->id])
                                    );
                                }
                                        
                                if (mt_rand(0,1)) {
                                    $ppd->RPS_Segment()->save(
                                        factory(RPS_Segment::class)->make(['piece_part_detail_id' => $ppd->id])
                                    );
                                }
                            })
                        );
                    })
                );
            }
        });
        
        ShopFinding::boot();
        
        return $ShopFindings;
    }
    
    /**
     * Create a single Shop Finding with a given number of Piece Parts with ALL segments.
     *
     * @param  (int) $noOfPieceParts
     * @return boolean $ShopFinding
     */
    protected function createSingleShopFindingAndPiecePartsWithAllSegments($noOfPieceParts = 0, $user = NULL)
    {
        if (!$user) {
            $user = $this->user;
        }
        
        ShopFinding::flushEventListeners(); // Prevents activities being recorded during seeding.
        
        $ShopFinding = factory(ShopFinding::class, 1)->create(['plant_code' => $user->location->plant_code])
        ->each(function($sf) use ($noOfPieceParts, $user) {
            
            $sf->HDR_Segment()->save(factory(HDR_Segment::class)->make([
                'shop_finding_id' => (string) $sf->id,
                'RON' => $user->location->name
            ]));
            
            $sf->ShopFindingsDetail()->saveMany(
                factory(ShopFindingsDetail::class, 1)->create(['shop_finding_id' => (string) $sf->id])
                ->each(function($sfd) use($sf) {
                    $sfd->RCS_Segment()->save(
                        factory(RCS_Segment::class)->make(['shop_findings_detail_id' => $sfd->id, 'SFI' => (string) $sf->id])
                    );
                    
                    $sfd->SAS_Segment()->save(
                        factory(SAS_Segment::class)->make(['shop_findings_detail_id' => $sfd->id])
                    );
                    
                    $sfd->AID_Segment()->save(
                        factory(AID_Segment::class)->make(['shop_findings_detail_id' => $sfd->id])
                    );
                    
                    $sfd->EID_Segment()->save(
                        factory(EID_Segment::class)->make(['shop_findings_detail_id' => $sfd->id])
                    );
                    
                    $sfd->API_Segment()->save(
                        factory(API_Segment::class)->make(['shop_findings_detail_id' => $sfd->id])
                    );
                    
                    $sfd->SUS_Segment()->save(
                        factory(SUS_Segment::class)->make(['shop_findings_detail_id' => $sfd->id])
                    );
                    
                    $sfd->RLS_Segment()->save(
                        factory(RLS_Segment::class)->make(['shop_findings_detail_id' => $sfd->id])
                    );
                    
                    $sfd->LNK_Segment()->save(
                        factory(LNK_Segment::class)->make(['shop_findings_detail_id' => $sfd->id])
                    );
                    
                    $sfd->ATT_Segment()->save(
                        factory(ATT_Segment::class)->make(['shop_findings_detail_id' => $sfd->id])
                    );
                    
                    $sfd->SPT_Segment()->save(
                        factory(SPT_Segment::class)->make(['shop_findings_detail_id' => $sfd->id])
                    );
                    
                    $sfd->MISC_Segment()->save(
                        factory(MISC_Segment::class)->make(['shop_findings_detail_id' => $sfd->id])
                    );
                })
            );
            
            if ($noOfPieceParts) {
                $sf->PiecePart()->saveMany(
                    factory(PiecePart::class, 1)->create(['shop_finding_id' => $sf->id])
                    ->each(function($pp) use($noOfPieceParts, $sf) {
                        $pp->PiecePartDetails()->saveMany(
                            factory(PiecePartDetail::class, $noOfPieceParts)->create(['piece_part_id' => $pp->id])
                            ->each(function($ppd) use ($sf) {
                                $ppd->WPS_Segment()->save(
                                    factory(WPS_Segment::class)->make([
                                        'piece_part_detail_id' => $ppd->id,
                                        'SFI' => $sf->id,
                                        'PPI' => $ppd->id
                                    ])
                                );
                                
                                $ppd->NHS_Segment()->save(
                                    factory(NHS_Segment::class)->make(['piece_part_detail_id' => $ppd->id])
                                );
                                
                                $ppd->RPS_Segment()->save(
                                    factory(RPS_Segment::class)->make(['piece_part_detail_id' => $ppd->id])
                                );
                            })
                        );
                    })
                );
            }
        });
        
        ShopFinding::boot();
        
        return $ShopFinding;
    }
    
    /**
     * Create multiple Shop Findings with a given number of Piece Parts with ALL segments.
     *
     * @param  (int) $noOfShopFindings
     * @param  (int) $noOfPieceParts
     * @param  \App\User $user
     * @return boolean $ShopFinding
     */
    protected function createMultipleShopFindingsAndPiecePartsWithAllSegments($noOfShopFindings = 1, $noOfPieceParts = 0, $user = NULL)
    {
        if (!$user) {
            $user = $this->user;
        }
        
        ShopFinding::flushEventListeners(); // Prevents activities being recorded during seeding.
        
        $ShopFinding = factory(ShopFinding::class, $noOfShopFindings)->create(['plant_code' => $user->location->plant_code])
        ->each(function($sf) use ($noOfPieceParts, $user) {
            
            $sf->HDR_Segment()->save(factory(HDR_Segment::class)->make([
                'shop_finding_id' => (string) $sf->id,
                'RON' => $user->location->name
            ]));
            
            $sf->ShopFindingsDetail()->saveMany(
                factory(ShopFindingsDetail::class, 1)->create(['shop_finding_id' => (string) $sf->id])
                ->each(function($sfd) use($sf) {
                    $sfd->RCS_Segment()->save(
                        factory(RCS_Segment::class)->make(['shop_findings_detail_id' => $sfd->id, 'SFI' => (string) $sf->id])
                    );
                    
                    $sfd->SAS_Segment()->save(
                        factory(SAS_Segment::class)->make(['shop_findings_detail_id' => $sfd->id])
                    );
                    
                    $sfd->AID_Segment()->save(
                        factory(AID_Segment::class)->make(['shop_findings_detail_id' => $sfd->id])
                    );
                    
                    $sfd->EID_Segment()->save(
                        factory(EID_Segment::class)->make(['shop_findings_detail_id' => $sfd->id])
                    );
                    
                    $sfd->API_Segment()->save(
                        factory(API_Segment::class)->make(['shop_findings_detail_id' => $sfd->id])
                    );
                    
                    $sfd->SUS_Segment()->save(
                        factory(SUS_Segment::class)->make(['shop_findings_detail_id' => $sfd->id])
                    );
                    
                    $sfd->RLS_Segment()->save(
                        factory(RLS_Segment::class)->make(['shop_findings_detail_id' => $sfd->id])
                    );
                    
                    $sfd->LNK_Segment()->save(
                        factory(LNK_Segment::class)->make(['shop_findings_detail_id' => $sfd->id])
                    );
                    
                    $sfd->ATT_Segment()->save(
                        factory(ATT_Segment::class)->make(['shop_findings_detail_id' => $sfd->id])
                    );
                    
                    $sfd->SPT_Segment()->save(
                        factory(SPT_Segment::class)->make(['shop_findings_detail_id' => $sfd->id])
                    );
                    
                    $sfd->MISC_Segment()->save(
                        factory(MISC_Segment::class)->make(['shop_findings_detail_id' => $sfd->id])
                    );
                })
            );
            
            if ($noOfPieceParts) {
                $sf->PiecePart()->saveMany(
                    factory(PiecePart::class, 1)->create(['shop_finding_id' => $sf->id])
                    ->each(function($pp) use($noOfPieceParts, $sf) {
                        $pp->PiecePartDetails()->saveMany(
                            factory(PiecePartDetail::class, $noOfPieceParts)->create(['piece_part_id' => $pp->id])
                            ->each(function($ppd) use ($sf) {
                                $ppd->WPS_Segment()->save(
                                    factory(WPS_Segment::class)->make([
                                        'piece_part_detail_id' => $ppd->id,
                                        'SFI' => $sf->id,
                                        'PPI' => $ppd->id
                                    ])
                                );
                                
                                $ppd->NHS_Segment()->save(
                                    factory(NHS_Segment::class)->make(['piece_part_detail_id' => $ppd->id])
                                );
                                
                                $ppd->RPS_Segment()->save(
                                    factory(RPS_Segment::class)->make(['piece_part_detail_id' => $ppd->id])
                                );
                            })
                        );
                    })
                );
            }
        });
        
        ShopFinding::boot();
        
        return $ShopFinding;
    }
    
    /**
     * Create multiple Utas/Collins Shop Findings with a given number of Piece Parts.
     *
     * @param  (int) $noOfShopFindings
     * @param  (int) $noOfPieceParts
     * @param  \App\User $user
     * @return boolean $ShopFinding
     */
    protected function createMultipleCollinsShopFindingsAndPieceParts($noOfShopFindings = 1, $noOfPieceParts = 0, $user = NULL)
    {
        if (!$user) {
            $user = $this->user;
        }
        
        ShopFinding::flushEventListeners(); // Prevents activities being recorded during seeding.
        
        $utasCode = UtasCode::inRandomOrder()->first();
        $location = Location::with('cage_codes')->where('plant_code', $utasCode->PLANT)->inRandomOrder()->first();
        
        $ShopFindings = factory(ShopFinding::class, $noOfShopFindings)->states('collins_part')->create(['plant_code' => $utasCode->PLANT])
        ->each(function($sf) use ($noOfPieceParts, $user, $utasCode, $location) {
            
            $sf->HDR_Segment()->save(factory(HDR_Segment::class)->states('collins_part')->make([
                'shop_finding_id' => (string) $sf->id,
                'RON' => $location->name
            ]));
            
            $sf->ShopFindingsDetail()->saveMany(
                factory(ShopFindingsDetail::class, 1)->create(['shop_finding_id' => (string) $sf->id])
                ->each(function($sfd) use($sf) {
                    $sfd->RCS_Segment()->save(
                        factory(RCS_Segment::class)->states('collins_part')->make(['shop_findings_detail_id' => $sfd->id, 'SFI' => (string) $sf->id])
                    );
                    
                    $sfd->SAS_Segment()->save(
                        factory(SAS_Segment::class)->make(['shop_findings_detail_id' => $sfd->id])
                    );
                    
                    if (mt_rand(0,1)) {
                        $sfd->AID_Segment()->save(
                            factory(AID_Segment::class)->make(['shop_findings_detail_id' => $sfd->id])
                        );
                    }
                    
                    if (mt_rand(0,1)) {
                        $sfd->EID_Segment()->save(
                            factory(EID_Segment::class)->make(['shop_findings_detail_id' => $sfd->id])
                        );
                    }
                    
                    if (mt_rand(0,1)) {
                        $sfd->API_Segment()->save(
                            factory(API_Segment::class)->make(['shop_findings_detail_id' => $sfd->id])
                        );
                    }
                    
                    // Non-mandatory but useful for testing exports.
                    $sfd->SUS_Segment()->save(
                        factory(SUS_Segment::class)->states('collins_part')->make(['shop_findings_detail_id' => $sfd->id])
                    );
                    
                    // Non-mandatory but useful for testing exports.
                    $sfd->RLS_Segment()->save(
                        factory(RLS_Segment::class)->states('collins_part')->make(['shop_findings_detail_id' => $sfd->id])
                    );
                    
                    if (mt_rand(0,1)) {
                        $sfd->LNK_Segment()->save(
                            factory(LNK_Segment::class)->make(['shop_findings_detail_id' => $sfd->id])
                        );
                    }
                    
                    if (mt_rand(0,1)) {
                        $sfd->ATT_Segment()->save(
                            factory(ATT_Segment::class)->make(['shop_findings_detail_id' => $sfd->id])
                        );
                    }
                    
                    if (mt_rand(0,1)) {
                        $sfd->SPT_Segment()->save(
                            factory(SPT_Segment::class)->make(['shop_findings_detail_id' => $sfd->id])
                        );
                    }
                    
                    $sfd->MISC_Segment()->save(
                        factory(MISC_Segment::class)->make(['shop_findings_detail_id' => $sfd->id])
                    );
                })
            );
            
            if ($noOfPieceParts) {
                $sf->PiecePart()->saveMany(
                    factory(PiecePart::class, 1)->create(['shop_finding_id' => $sf->id])
                    ->each(function($pp) use($noOfPieceParts, $sf) {
                        $pp->PiecePartDetails()->saveMany(
                            factory(PiecePartDetail::class, $noOfPieceParts)->create(['piece_part_id' => $pp->id])
                            ->each(function($ppd) use ($sf) {
                                $ppd->WPS_Segment()->save(
                                    factory(WPS_Segment::class)->make([
                                        'piece_part_detail_id' => $ppd->id,
                                        'SFI' => $sf->id,
                                        'PPI' => $ppd->id
                                    ])
                                );
                                
                                // Non-mandatory but useful for testing exports.
                                $ppd->NHS_Segment()->save(
                                    factory(NHS_Segment::class)->states('collins_part')->make(['piece_part_detail_id' => $ppd->id])
                                );
                                
                                // Non-mandatory but useful for testing exports.
                                $ppd->RPS_Segment()->save(
                                    factory(RPS_Segment::class)->make(['piece_part_detail_id' => $ppd->id])
                                );
                            })
                        );
                    })
                );
            }
        });
        
        ShopFinding::boot();
        
        return $ShopFindings;
    }
    
    /**
     * Create a single Shop Finding with a given number of Piece Parts.
     *
     * @param  (int) $noOfPieceParts
     * @return boolean $ShopFinding
     */
    protected function createSingleShopFindingAndPieceParts($noOfPieceParts = 0, $user = NULL)
    {
        if (!$user) {
            $user = $this->user;
        }
        
        ShopFinding::flushEventListeners(); // Prevents activities being recorded during seeding.
        
        $ShopFinding = factory(ShopFinding::class, 1)->create(['plant_code' => $user->location->plant_code])
        ->each(function($sf) use ($noOfPieceParts, $user) {
            
            $sf->HDR_Segment()->save(factory(HDR_Segment::class)->make([
                'shop_finding_id' => (string) $sf->id,
                'RON' => $user->location->name
            ]));
            
            $sf->ShopFindingsDetail()->saveMany(
                factory(ShopFindingsDetail::class, 1)->create(['shop_finding_id' => (string) $sf->id])
                ->each(function($sfd) use($sf) {
                    $sfd->RCS_Segment()->save(
                        factory(RCS_Segment::class)->make(['shop_findings_detail_id' => $sfd->id, 'SFI' => (string) $sf->id])
                    );
                    
                    $sfd->SAS_Segment()->save(
                        factory(SAS_Segment::class)->make(['shop_findings_detail_id' => $sfd->id])
                    );
                    
                    if (mt_rand(0,1)) {
                        $sfd->AID_Segment()->save(
                            factory(AID_Segment::class)->make(['shop_findings_detail_id' => $sfd->id])
                        );
                    }
                    
                    if (mt_rand(0,1)) {
                        $sfd->EID_Segment()->save(
                            factory(EID_Segment::class)->make(['shop_findings_detail_id' => $sfd->id])
                        );
                    }
                    
                    if (mt_rand(0,1)) {
                        $sfd->API_Segment()->save(
                            factory(API_Segment::class)->make(['shop_findings_detail_id' => $sfd->id])
                        );
                    }
                    
                    if (mt_rand(0,1)) {
                        $sfd->SUS_Segment()->save(
                            factory(SUS_Segment::class)->make(['shop_findings_detail_id' => $sfd->id])
                        );
                    }
                    
                    if (mt_rand(0,1)) {
                        $sfd->RLS_Segment()->save(
                            factory(RLS_Segment::class)->make(['shop_findings_detail_id' => $sfd->id])
                        );
                    }
                    
                    if (mt_rand(0,1)) {
                        $sfd->LNK_Segment()->save(
                            factory(LNK_Segment::class)->make(['shop_findings_detail_id' => $sfd->id])
                        );
                    }
                    
                    if (mt_rand(0,1)) {
                        $sfd->ATT_Segment()->save(
                            factory(ATT_Segment::class)->make(['shop_findings_detail_id' => $sfd->id])
                        );
                    }
                    
                    if (mt_rand(0,1)) {
                        $sfd->SPT_Segment()->save(
                            factory(SPT_Segment::class)->make(['shop_findings_detail_id' => $sfd->id])
                        );
                    }
                })
            );
            
            if ($noOfPieceParts) {
                $sf->PiecePart()->saveMany(
                    factory(PiecePart::class, 1)->create(['shop_finding_id' => $sf->id])
                    ->each(function($pp) use($noOfPieceParts, $sf) {
                        $pp->PiecePartDetails()->saveMany(
                            factory(PiecePartDetail::class, $noOfPieceParts)->create(['piece_part_id' => $pp->id])
                            ->each(function($ppd) use ($sf) {
                                $ppd->WPS_Segment()->save(
                                    factory(WPS_Segment::class)->make([
                                        'piece_part_detail_id' => $ppd->id,
                                        'SFI' => $sf->id,
                                        'PPI' => $ppd->id
                                    ])
                                );
                                        
                                if (mt_rand(0,1)) {
                                    $ppd->NHS_Segment()->save(
                                        factory(NHS_Segment::class)->make(['piece_part_detail_id' => $ppd->id])
                                    );
                                }
                                        
                                if (mt_rand(0,1)) {
                                    $ppd->RPS_Segment()->save(
                                        factory(RPS_Segment::class)->make(['piece_part_detail_id' => $ppd->id])
                                    );
                                }
                            })
                        );
                    })
                );
            }
        });
        
        ShopFinding::boot();
        
        return $ShopFinding;
    }
    
    /**
     * Create shopfinding and piece parts that should trigger the failed code warning.
     *
     * @param  (int) $noOfPieceParts
     * @return boolean $ShopFinding
     */
    protected function createSingleShopFindingAndPiecePartsThatShouldTriggerFailedCodeWarning($noOfPieceParts = 0, $user = NULL)
    {
        if (!$user) {
            $user = $this->user;
        }
        
        ShopFinding::flushEventListeners(); // Prevents activities being recorded during seeding.
        
        $ShopFinding = factory(ShopFinding::class, 1)->create(['plant_code' => $user->location->plant_code])
        ->each(function($sf) use ($noOfPieceParts, $user) {
            
            $sf->HDR_Segment()->save(factory(HDR_Segment::class)->make([
                'shop_finding_id' => (string) $sf->id,
                'RON' => $user->location->name
            ]));
            
            // Pick a random failure code combination that should trigger the warning.
            $failureCodes = RcsFailureCode::where('RRC', 'U')
                ->where('FHS', 'HW')
                ->where('FFC', 'FT')
                ->where('FCR', 'CR')
                ->inRandomOrder()
                ->first();
            
            $sf->ShopFindingsDetail()->saveMany(
                factory(ShopFindingsDetail::class, 1)->create(['shop_finding_id' => (string) $sf->id])
                ->each(function($sfd) use($sf, $failureCodes) {
                    $sfd->RCS_Segment()->save(
                        factory(RCS_Segment::class)->make([
                            'shop_findings_detail_id' => $sfd->id,
                            'SFI' => (string) $sf->id,
                            'RRC' => $failureCodes->RRC,
                            'FHS' => $failureCodes->FHS,
                            'FFC' => $failureCodes->FFC,
                            'FCR' => $failureCodes->FCR,
                            'FFI' => $failureCodes->FFI,
                            'FAC' => $failureCodes->FAC,
                            'FBC' => $failureCodes->FBC
                        ])
                    );
                    
                    $sfd->SAS_Segment()->save(
                        factory(SAS_Segment::class)->make(['shop_findings_detail_id' => $sfd->id])
                    );
                    
                    if (mt_rand(0,1)) {
                        $sfd->AID_Segment()->save(
                            factory(AID_Segment::class)->make(['shop_findings_detail_id' => $sfd->id])
                        );
                    }
                    
                    if (mt_rand(0,1)) {
                        $sfd->EID_Segment()->save(
                            factory(EID_Segment::class)->make(['shop_findings_detail_id' => $sfd->id])
                        );
                    }
                    
                    if (mt_rand(0,1)) {
                        $sfd->API_Segment()->save(
                            factory(API_Segment::class)->make(['shop_findings_detail_id' => $sfd->id])
                        );
                    }
                    
                    if (mt_rand(0,1)) {
                        $sfd->SUS_Segment()->save(
                            factory(SUS_Segment::class)->make(['shop_findings_detail_id' => $sfd->id])
                        );
                    }
                    
                    if (mt_rand(0,1)) {
                        $sfd->RLS_Segment()->save(
                            factory(RLS_Segment::class)->make(['shop_findings_detail_id' => $sfd->id])
                        );
                    }
                    
                    if (mt_rand(0,1)) {
                        $sfd->LNK_Segment()->save(
                            factory(LNK_Segment::class)->make(['shop_findings_detail_id' => $sfd->id])
                        );
                    }
                    
                    if (mt_rand(0,1)) {
                        $sfd->ATT_Segment()->save(
                            factory(ATT_Segment::class)->make(['shop_findings_detail_id' => $sfd->id])
                        );
                    }
                    
                    if (mt_rand(0,1)) {
                        $sfd->SPT_Segment()->save(
                            factory(SPT_Segment::class)->make(['shop_findings_detail_id' => $sfd->id])
                        );
                    }
                })
            );
            
            if ($noOfPieceParts) {
                $sf->PiecePart()->saveMany(
                    factory(PiecePart::class, 1)->create(['shop_finding_id' => $sf->id])
                    ->each(function($pp) use($noOfPieceParts, $sf) {
                        $pp->PiecePartDetails()->saveMany(
                            factory(PiecePartDetail::class, $noOfPieceParts)->create(['piece_part_id' => $pp->id])
                            ->each(function($ppd) use ($sf) {
                                $ppd->WPS_Segment()->save(
                                    factory(WPS_Segment::class)->make([
                                        'piece_part_detail_id' => $ppd->id,
                                        'SFI' => $sf->id,
                                        'PPI' => $ppd->id,
                                        'PFC' => 'D'
                                    ])
                                );
                                        
                                if (mt_rand(0,1)) {
                                    $ppd->NHS_Segment()->save(
                                        factory(NHS_Segment::class)->make(['piece_part_detail_id' => $ppd->id])
                                    );
                                }
                                        
                                if (mt_rand(0,1)) {
                                    $ppd->RPS_Segment()->save(
                                        factory(RPS_Segment::class)->make(['piece_part_detail_id' => $ppd->id])
                                    );
                                }
                            })
                        );
                    })
                );
            }
        });
        
        ShopFinding::boot();
        
        return $ShopFinding;
    }
    
    /**
     * Create shopfinding and piece parts that should trigger the not failed code warning.
     *
     * @param  (int) $noOfPieceParts
     * @return boolean $ShopFinding
     */
    protected function createSingleShopFindingAndPiecePartsThatShouldTriggerNotFailedCodeWarning($noOfPieceParts = 0, $user = NULL)
    {
        if (!$user) {
            $user = $this->user;
        }
        
        ShopFinding::flushEventListeners(); // Prevents activities being recorded during seeding.
        
        $ShopFinding = factory(ShopFinding::class)->create(['plant_code' => $user->location->plant_code])
        ->each(function($sf) use ($noOfPieceParts, $user) {
            
            $sf->HDR_Segment()->save(factory(HDR_Segment::class)->make([
                'shop_finding_id' => (string) $sf->id,
                'RON' => $user->location->name
            ]));
            
            // Pick a random failure code combination that should trigger the warning.
            $nonFailureCodes = RcsFailureCode::whereIn('RRC', ['O', 'M', 'S'])
                ->inRandomOrder()
                ->first();
            
            $sf->ShopFindingsDetail()->saveMany(
                factory(ShopFindingsDetail::class, 1)->create(['shop_finding_id' => (string) $sf->id])
                ->each(function($sfd) use($sf, $nonFailureCodes) {
                    $sfd->RCS_Segment()->save(
                        factory(RCS_Segment::class)->make([
                            'shop_findings_detail_id' => $sfd->id,
                            'SFI' => (string) $sf->id,
                            'RRC' => $nonFailureCodes->RRC,
                            'FHS' => $nonFailureCodes->FHS,
                            'FFC' => $nonFailureCodes->FFC,
                            'FCR' => $nonFailureCodes->FCR,
                            'FFI' => $nonFailureCodes->FFI,
                            'FAC' => $nonFailureCodes->FAC,
                            'FBC' => $nonFailureCodes->FBC
                        ])
                    );
                    
                    $sfd->SAS_Segment()->save(
                        factory(SAS_Segment::class)->make(['shop_findings_detail_id' => $sfd->id])
                    );
                    
                    if (mt_rand(0,1)) {
                        $sfd->AID_Segment()->save(
                            factory(AID_Segment::class)->make(['shop_findings_detail_id' => $sfd->id])
                        );
                    }
                    
                    if (mt_rand(0,1)) {
                        $sfd->EID_Segment()->save(
                            factory(EID_Segment::class)->make(['shop_findings_detail_id' => $sfd->id])
                        );
                    }
                    
                    if (mt_rand(0,1)) {
                        $sfd->API_Segment()->save(
                            factory(API_Segment::class)->make(['shop_findings_detail_id' => $sfd->id])
                        );
                    }
                    
                    if (mt_rand(0,1)) {
                        $sfd->SUS_Segment()->save(
                            factory(SUS_Segment::class)->make(['shop_findings_detail_id' => $sfd->id])
                        );
                    }
                    
                    if (mt_rand(0,1)) {
                        $sfd->RLS_Segment()->save(
                            factory(RLS_Segment::class)->make(['shop_findings_detail_id' => $sfd->id])
                        );
                    }
                    
                    if (mt_rand(0,1)) {
                        $sfd->LNK_Segment()->save(
                            factory(LNK_Segment::class)->make(['shop_findings_detail_id' => $sfd->id])
                        );
                    }
                    
                    if (mt_rand(0,1)) {
                        $sfd->ATT_Segment()->save(
                            factory(ATT_Segment::class)->make(['shop_findings_detail_id' => $sfd->id])
                        );
                    }
                    
                    if (mt_rand(0,1)) {
                        $sfd->SPT_Segment()->save(
                            factory(SPT_Segment::class)->make(['shop_findings_detail_id' => $sfd->id])
                        );
                    }
                })
            );
            
            if ($noOfPieceParts) {
                $sf->PiecePart()->saveMany(
                    factory(PiecePart::class, 1)->create(['shop_finding_id' => $sf->id])
                    ->each(function($pp) use($noOfPieceParts, $sf) {
                        $pp->PiecePartDetails()->saveMany(
                            factory(PiecePartDetail::class, $noOfPieceParts)->create(['piece_part_id' => $pp->id])
                            ->each(function($ppd) use ($sf) {
                                $ppd->WPS_Segment()->save(
                                    factory(WPS_Segment::class)->make([
                                        'piece_part_detail_id' => $ppd->id,
                                        'SFI' => $sf->id,
                                        'PPI' => $ppd->id,
                                        'PFC' => 'Y'
                                    ])
                                );
                                        
                                if (mt_rand(0,1)) {
                                    $ppd->NHS_Segment()->save(
                                        factory(NHS_Segment::class)->make(['piece_part_detail_id' => $ppd->id])
                                    );
                                }
                                        
                                if (mt_rand(0,1)) {
                                    $ppd->RPS_Segment()->save(
                                        factory(RPS_Segment::class)->make(['piece_part_detail_id' => $ppd->id])
                                    );
                                }
                            })
                        );
                    })
                );
            }
        });
        
        ShopFinding::boot();
        
        return $ShopFinding;
    }
    
    /**
     * Create given number of Shop Findings and Piece Parts with all fields and maximum possible string lengths.
     *
     * @param (int) $numberOfShopFindings
     * @param (string) $status
     * @param (string) $validity
     * @param (string) $locationCode
     * @param \Carbon\Carbon $from
     * @param \Carbon\Carbon $to
     * @return \Illuminate\Database\Eloquent\Collection $ShopFindings
     */
    protected function createShopFindingsAndPiecePartsWithMaxLengthStrings(
        $numberOfShopFindings = 1,
        $noOfPieceParts = 1,
        $status = 'complete_shipped',
        $locationCode = NULL,
        Carbon $from = NULL,
        Carbon $to = NULL
    )
    {
        $from = $from ?: Carbon::now();
        $to = $to ?: Carbon::now();
        
        ShopFinding::flushEventListeners(); // Prevents activities being recorded during seeding.
        
        switch ($status) {
            case 'complete_shipped':
                $attributes = [
                    'status' => 'complete_shipped',
                    'shipped_at' => $this->faker->dateTimeBetween($from->format('Y-m-d 00:00:00'), $to->format('Y-m-d 00:00:00'))->format('Y-m-d 00:00:00')
                ];
                break;
            case 'complete_scrapped':
                $attributes = [
                    'status' => 'complete_scrapped',
                    'scrapped_at' => $this->faker->dateTimeBetween($from->format('Y-m-d 00:00:00'), $to->format('Y-m-d 00:00:00'))->format('Y-m-d 00:00:00')
                ];
                break;
            case 'subcontracted':
                $attributes = [
                    'status' => 'subcontracted',
                    'subcontracted_at' => $this->faker->dateTimeBetween($from->format('Y-m-d 00:00:00'), $to->format('Y-m-d 00:00:00'))->format('Y-m-d 00:00:00')
                ];
                break;
            default:
                $attributes = ['status' => 'in_progress'];
                break;
        }
        
        $attributes['plant_code'] = $locationCode;
        
        $ShopFindings = factory(ShopFinding::class, $numberOfShopFindings)->states('all_fields_max_string_length')->create($attributes)
        ->each(function($sf) use ($noOfPieceParts, $locationCode, $to, $from) {
            if (!$locationCode) {
                $location = Location::inRandomOrder()->first();
            } else {
                $location = Location::where('plant_code', $locationCode)->firstOrFail();
            }
            
            $attributes = [
                'shop_finding_id' => (string) $sf->id,
                'RON' => $location->name
            ];
            
            $sf->HDR_Segment()->save(factory(HDR_Segment::class)->states('all_fields_max_string_length')->make($attributes));
            
            $sf->ShopFindingsDetail()->saveMany(
                factory(ShopFindingsDetail::class, 1)->create(['shop_finding_id' => (string) $sf->id])
                ->each(function($sfd) use($sf) {
                    $sfd->RCS_Segment()->save(
                        // RRC is set to 'U' so it won't trigger the Fail ID error.
                        factory(RCS_Segment::class, 'all_fields_max_string_length')->make(['shop_findings_detail_id' => $sfd->id, 'SFI' => (string) $sf->id, 'RRC' => 'U'])
                    );
                    
                    $sfd->SAS_Segment()->save(
                        factory(SAS_Segment::class, 'all_fields_max_string_length')->make(['shop_findings_detail_id' => $sfd->id])
                    );
                    
                    $sfd->AID_Segment()->save(
                        factory(AID_Segment::class, 'all_fields_max_string_length')->make(['shop_findings_detail_id' => $sfd->id])
                    );
                
                    $sfd->EID_Segment()->save(
                        factory(EID_Segment::class, 'all_fields_max_string_length')->make(['shop_findings_detail_id' => $sfd->id])
                    );
                
                    $sfd->API_Segment()->save(
                        factory(API_Segment::class, 'all_fields_max_string_length')->make(['shop_findings_detail_id' => $sfd->id])
                    );
                
                    $sfd->SUS_Segment()->save(
                        factory(SUS_Segment::class, 'all_fields_max_string_length')->make(['shop_findings_detail_id' => $sfd->id])
                    );
                
                    $sfd->RLS_Segment()->save(
                        factory(RLS_Segment::class, 'all_fields_max_string_length')->make(['shop_findings_detail_id' => $sfd->id])
                    );
                
                    $sfd->LNK_Segment()->save(
                        factory(LNK_Segment::class, 'all_fields_max_string_length')->make(['shop_findings_detail_id' => $sfd->id])
                    );
                
                    $sfd->ATT_Segment()->save(
                        factory(ATT_Segment::class, 'all_fields_max_string_length')->make(['shop_findings_detail_id' => $sfd->id])
                    );
                
                    $sfd->SPT_Segment()->save(
                        factory(SPT_Segment::class, 'all_fields_max_string_length')->make(['shop_findings_detail_id' => $sfd->id])
                    );
                })
            );
            
            if ($noOfPieceParts) {
                $sf->PiecePart()->saveMany(
                    factory(PiecePart::class, 1)->create(['shop_finding_id' => $sf->id])
                    ->each(function($pp) use($noOfPieceParts, $sf) {
                        $pp->PiecePartDetails()->saveMany(
                            factory(PiecePartDetail::class, $noOfPieceParts)->create(['piece_part_id' => $pp->id])
                            ->each(function($ppd) use ($sf) {
                                $ppd->WPS_Segment()->save(
                                    factory(WPS_Segment::class)->states('all_fields_max_string_length')->make([
                                        'piece_part_detail_id' => $ppd->id,
                                        'SFI' => $sf->id,
                                        'PPI' => $ppd->id,
                                        'PFC' => 'Y'
                                    ])
                                );
                                
                                if (mt_rand(0,1)) {
                                    $ppd->NHS_Segment()->save(
                                        factory(NHS_Segment::class)->states('all_fields_max_string_length')->make(['piece_part_detail_id' => $ppd->id])
                                    );
                                }
                                        
                                if (mt_rand(0,1)) {
                                    $ppd->RPS_Segment()->save(
                                        factory(RPS_Segment::class)->states('all_fields_max_string_length')->make(['piece_part_detail_id' => $ppd->id])
                                    );
                                }
                            })
                        );
                    })
                );
            }
        });
        
        ShopFinding::boot();
        
        return $ShopFindings;
    }
    
    /**
     * Create given number of Shop Findings and Piece Parts with all fields and shortest possible string lengths.
     *
     * @param (int) $numberOfShopFindings
     * @param (string) $status
     * @param (string) $validity
     * @param (string) $locationCode
     * @param \Carbon\Carbon $from
     * @param \Carbon\Carbon $to
     * @return \Illuminate\Database\Eloquent\Collection $ShopFindings
     */
    protected function createShopFindingsAndPiecePartsWithMinLengthStrings(
        $numberOfShopFindings = 1,
        $noOfPieceParts = 1,
        $status = 'complete_shipped',
        $locationCode = NULL,
        Carbon $from = NULL,
        Carbon $to = NULL
    )
    {
        $from = $from ?: Carbon::now();
        $to = $to ?: Carbon::now();
        
        ShopFinding::flushEventListeners(); // Prevents activities being recorded during seeding.
        
        switch ($status) {
            case 'complete_shipped':
                $attributes = [
                    'status' => 'complete_shipped',
                    'shipped_at' => $this->faker->dateTimeBetween($from->format('Y-m-d 00:00:00'), $to->format('Y-m-d 00:00:00'))->format('Y-m-d 00:00:00')
                ];
                break;
            case 'complete_scrapped':
                $attributes = [
                    'status' => 'complete_scrapped',
                    'scrapped_at' => $this->faker->dateTimeBetween($from->format('Y-m-d 00:00:00'), $to->format('Y-m-d 00:00:00'))->format('Y-m-d 00:00:00')
                ];
                break;
            case 'subcontracted':
                $attributes = [
                    'status' => 'subcontracted',
                    'subcontracted_at' => $this->faker->dateTimeBetween($from->format('Y-m-d 00:00:00'), $to->format('Y-m-d 00:00:00'))->format('Y-m-d 00:00:00')
                ];
                break;
            default:
                $attributes = ['status' => 'in_progress'];
                break;
        }
        
        $attributes['plant_code'] = $locationCode;
        
        $ShopFindings = factory(ShopFinding::class, $numberOfShopFindings)->states('all_fields_min_string_length')->create($attributes)
        ->each(function($sf) use ($noOfPieceParts, $locationCode, $to, $from) {
            if (!$locationCode) {
                $location = Location::inRandomOrder()->first();
            } else {
                $location = Location::where('plant_code', $locationCode)->firstOrFail();
            }
            
            $attributes = [
                'shop_finding_id' => (string) $sf->id,
                'RON' => $location->name
            ];
            
            $sf->HDR_Segment()->save(factory(HDR_Segment::class)->states('all_fields_min_string_length')->make($attributes));
            
            $sf->ShopFindingsDetail()->saveMany(
                factory(ShopFindingsDetail::class, 1)->create(['shop_finding_id' => (string) $sf->id])
                ->each(function($sfd) use($sf) {
                    $sfd->RCS_Segment()->save(
                        factory(RCS_Segment::class)->states('all_fields_min_string_length')->make(['shop_findings_detail_id' => $sfd->id, 'SFI' => (string) $sf->id])
                    );
                    
                    $sfd->SAS_Segment()->save(
                        factory(SAS_Segment::class)->states('all_fields_min_string_length')->make(['shop_findings_detail_id' => $sfd->id])
                    );
                    
                    
                    $sfd->AID_Segment()->save(
                        factory(AID_Segment::class)->states('all_fields_min_string_length')->make(['shop_findings_detail_id' => $sfd->id])
                    );
                
                
                
                    $sfd->EID_Segment()->save(
                        factory(EID_Segment::class)->states('all_fields_min_string_length')->make(['shop_findings_detail_id' => $sfd->id])
                    );
                
                
                
                    $sfd->API_Segment()->save(
                        factory(API_Segment::class)->states('all_fields_min_string_length')->make(['shop_findings_detail_id' => $sfd->id])
                    );
                
                
                
                    $sfd->SUS_Segment()->save(
                        factory(SUS_Segment::class)->states('all_fields_min_string_length')->make(['shop_findings_detail_id' => $sfd->id])
                    );
                
                
                
                    $sfd->RLS_Segment()->save(
                        factory(RLS_Segment::class)->states('all_fields_min_string_length')->make(['shop_findings_detail_id' => $sfd->id])
                    );
                
                
                
                    $sfd->LNK_Segment()->save(
                        factory(LNK_Segment::class)->states('all_fields_min_string_length')->make(['shop_findings_detail_id' => $sfd->id])
                    );
                
                
                
                    $sfd->ATT_Segment()->save(
                        factory(ATT_Segment::class)->states('all_fields_min_string_length')->make(['shop_findings_detail_id' => $sfd->id])
                    );
                
                
                
                    $sfd->SPT_Segment()->save(
                        factory(SPT_Segment::class)->states('all_fields_min_string_length')->make(['shop_findings_detail_id' => $sfd->id])
                    );
                    
                })
            );
            
            if ($noOfPieceParts) {
                $sf->PiecePart()->saveMany(
                    factory(PiecePart::class, 1)->create(['shop_finding_id' => $sf->id])
                    ->each(function($pp) use($noOfPieceParts, $sf) {
                        $pp->PiecePartDetails()->saveMany(
                            factory(PiecePartDetail::class, $noOfPieceParts)->create(['piece_part_id' => $pp->id])
                            ->each(function($ppd) use ($sf) {
                                $ppd->WPS_Segment()->save(
                                    factory(WPS_Segment::class)->states('all_fields_min_string_length')->make([
                                        'piece_part_detail_id' => $ppd->id,
                                        'SFI' => $sf->id,
                                        'PPI' => $ppd->id,
                                        'PFC' => 'Y'
                                    ])
                                );
                                
                                if (mt_rand(0,1)) {
                                    $ppd->NHS_Segment()->save(
                                        factory(NHS_Segment::class)->states('all_fields_min_string_length')->make(['piece_part_detail_id' => $ppd->id])
                                    );
                                }
                                        
                                if (mt_rand(0,1)) {
                                    $ppd->RPS_Segment()->save(
                                        factory(RPS_Segment::class)->states('all_fields_min_string_length')->make(['piece_part_detail_id' => $ppd->id])
                                    );
                                }
                            })
                        );
                    })
                );
            }
        });
        
        ShopFinding::boot();
        
        return $ShopFindings;
    }
}