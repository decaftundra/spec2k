<?php

namespace App\Listeners;

use App\Events\PartListDeleted;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class FireRemoveUnwantedPartsCommand
{
    /**
     * Handle the event.
     *
     * @param  mixed  $event
     * @return void
     */
    public function handle($event)
    {
        Log::info('Firing remove unwanted parts command from event listener.');
        
        Artisan::call('spec2kapp:remove_unwanted_parts');
    }
}
