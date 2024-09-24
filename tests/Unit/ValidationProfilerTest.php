<?php

namespace Tests\Unit;

use App\Location;
use App\Notification;
use App\NotificationPiecePart;
use App\UtasCode;
use App\ValidationProfiler;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ValidationProfilerTest extends TestCase
{
    /**
     * Test that the default validation profile is chosen to validate the shop finding.
     *
     * @return void
     */
    public function testDefaultProfile()
    {
        $shopFindings = $this->createMultipleShopFindingsAndPiecePartsWithAllSegments(5, mt_rand(1, 5), $this->adminUser);
        
        $shopfinding = $shopFindings->random(1)->first();
        
        $profile = new ValidationProfiler('RCS_Segment', $shopfinding->ShopFindingsDetail->RCS_Segment, $shopfinding->id);
        
        $this->assertEquals($profile->getProfileName(), 'DefaultProfile');
        
        $this->assertFalse($profile->hasMiscSegment());
    }
    
    /**
     * Test that the collins validation profile is chosen to validate the shop finding.
     *
     * @return void
     */
    public function testCollinsProfile()
    {
        $collinsShopFindings = $this->createMultipleCollinsShopFindingsAndPieceParts(5, mt_rand(1, 5), $this->adminUser);
        
        $shopfinding = $collinsShopFindings->random(1)->first();
        
        $profile = new ValidationProfiler('RCS_Segment', $shopfinding->ShopFindingsDetail->RCS_Segment, $shopfinding->id);
        
        $this->assertEquals($profile->getProfileName(), 'UtasProfile');
        
        $this->assertTrue($profile->hasMiscSegment());
    }
    
    /**
     * Test that the default validation profile is chosen for a notification.
     *
     * @return void
     */
    public function testDefaultProfileOnNotification()
    {
        $location = Location::inRandomOrder()->first();
        
        $notifications = factory(Notification::class, 5)
            ->states('all_segments')
            ->create(['plant_code' => $location->plant_code, 'hdrRON' => $location->name])
            ->each(function($n){
                $noOfPieceParts = mt_rand(1, 20);
                
                if ($noOfPieceParts) {
                    $n->PieceParts()->saveMany(
                        factory(NotificationPiecePart::class, $noOfPieceParts)->states('all_segments')->make(['notification_id' => $n->id, 'wpsSFI' => $n->id])
                    );
                }
            });
        
        $notification = $notifications->random(1)->first();
        
        $profile = new ValidationProfiler('RCS_Segment', $notification, $notification->get_RCS_SFI());
        
        $this->assertEquals($profile->getProfileName(), 'DefaultProfile');
        
        $this->assertFalse($profile->hasMiscSegment());
    }
    
    /**
     * Test that the default validation profile is chosen for a notification.
     *
     * @return void
     */
    public function testCollinsProfileOnNotification()
    {
        $location = Location::where('plant_code', 3101)->inRandomOrder()->first();
        
        $utasParts = UtasCode::getAllUtasCodes();
        $uOrS = ['U', 'S'];
        
        $notifications = factory(Notification::class, 5)
            ->create([
                'plant_code' => $location->plant_code,
                'hdrRON' => $location->name,
                'rcsRRC' => $uOrS[array_rand($uOrS)],
                'rcsMPN' => $utasParts[array_rand($utasParts)]
            ])
            ->each(function($n) {
                $noOfPieceParts = mt_rand(1, 20);
                
                if ($noOfPieceParts) {
                    $n->PieceParts()->saveMany(
                        factory(NotificationPiecePart::class, $noOfPieceParts)->make([
                            'notification_id' => $n->id,
                            'wpsSFI' => $n->id
                        ])
                    );
                }
            });
        
        $notification = $notifications->random(1)->first();
        
        $profile = new ValidationProfiler('RCS_Segment', $notification, $notification->get_RCS_SFI());
        
        $this->assertEquals($profile->getProfileName(), 'UtasProfile');
        
        $this->assertTrue($profile->hasMiscSegment());
    }
}
