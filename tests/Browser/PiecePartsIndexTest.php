<?php

namespace Tests\Browser;

use App\Notification;
use Tests\DuskTestCase;
use Laravel\Dusk\Browser;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class PiecePartsIndexTest extends DuskTestCase
{
    /**
     * A Dusk test example.
     *
     * @return void
     */
    public function testSavePiecePartsIndex()
    {
        $notification = Notification::with('pieceParts')
            ->where('plant_code', $this->dataAdminUser->location->plant_code)
            ->whereHas('pieceParts')
            ->inRandomOrder()
            ->first();
            
        $this->browse(function (Browser $browser) use ($notification) {
            $browser->loginAs($this->dataAdminUser)
                    ->visit(route('piece-parts.index', $notification->id))
                    ->assertSeeIn('h1', 'Piece Parts')
                    ->assertSee(count($notification->PieceParts) . ' piece parts found.')
                    ->press('Save All')
                    ->pause(1000);
                    
                    if ($browser->assertSee('error')) {
                        $browser->assertSee('Some Piece Parts contained errors, please see below.');
                    } else {
                        $browser->assertSee('All Piece Parts saved successfully!');
                    }
        });
    }
}
