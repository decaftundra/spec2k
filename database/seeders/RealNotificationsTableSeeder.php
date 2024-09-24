<?php

namespace Database\Seeders;

use App\Location;
use App\UtasCode;
use Illuminate\Database\Seeder;
use App\Notification;
use App\NotificationPiecePart;

class RealNotificationsTableSeeder extends Seeder
{
    protected $locations;
    
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Make notifications and piece parts for each location.
        $this->locations = Location::all();
        
        $this->seedNotificationsWithoutPieceParts();
        
        $this->seedNotificationsWithRandomNumberOfPieceParts();
        
        $this->seedNotificationsWithRandomNumberOfPiecePartsAllSegments();
        
        $this->seedUtasNotificationsWithRandomNumberOfPieceParts();
    }
    
    /**
     * Seed some notifications without any piece parts attached.
     *
     * @return void
     */
    public function seedNotificationsWithoutPieceParts()
    {
        foreach ($this->locations as $location) {
            factory(Notification::class, 5)
                ->create(['plant_code' => $location->plant_code, 'hdrRON' => $location->name]);
        }
    }
    
    /**
     * Seed some notifications with a random amount of piece parts.
     *
     * @return void
     */
    public function seedNotificationsWithRandomNumberOfPieceParts()
    {
        foreach ($this->locations as $location) {
            factory(Notification::class, 5)
                ->create(['plant_code' => $location->plant_code, 'hdrRON' => $location->name])
                ->each(function($n){
                    $noOfPieceParts = mt_rand(1, 20);
                    
                    if ($noOfPieceParts) {
                        $n->PieceParts()->saveMany(
                            factory(NotificationPiecePart::class, $noOfPieceParts)->make(['notification_id' => $n->id, 'wpsSFI' => $n->id])
                        );
                    }
                });
        }
    }
    
    /**
     * Seed some notifications with random amount of piece parts and all segments filled.
     *
     * @return void
     */
    public function seedNotificationsWithRandomNumberOfPiecePartsAllSegments()
    {
        foreach ($this->locations as $location) {
            factory(Notification::class, 5)
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
        }
    }
    
    /**
     * Seed some Utas notifications with random amount of piece parts.
     *
     * @return void
     */
    public function seedUtasNotificationsWithRandomNumberOfPieceParts()
    {
        $utasParts = UtasCode::getAllUtasCodes();
        $uOrS = ['U', 'S'];

        foreach ($this->locations as $location) {
            factory(Notification::class, 5)
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
        }
    }
}
