<?php

namespace App\Listeners;

use App\Events\PasswordUpdated;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Log;

class LogPasswordUpdate
{
    /**
     * Handle the event.
     *
     * @param  PasswordUpdated  $event
     * @return void
     */
    public function handle(PasswordUpdated $event)
    {
        Log::info('Password Updated', ['user_id' => $event->user->id, 'user_fullname' => $event->user->fullname]);
    }
}
