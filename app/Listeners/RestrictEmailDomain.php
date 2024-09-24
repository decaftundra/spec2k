<?php

namespace App\Listeners;

use Log;
use App\User;
use App\Traits\MiscTraits;
use Illuminate\Mail\Events\MessageSending;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class RestrictEmailDomain
{
    use MiscTraits;
    
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  MessageSending  $event
     * @return void
     */
    public function handle(MessageSending $event)
    {
        // remove any bcc and cc
        $event->message->setBcc([]);
        $event->message->setCc([]);
        
        $to = $event->message->getTo();
        
        $allowedDomains = User::getAllowedDomains();
        
        $pass = 0;
        
        foreach ($allowedDomains as $domain) {
            foreach ($to as $address => $name) {
                if ($this->endsWith($address, $domain)) {
                    $pass = 1;
                }
            }
        }
        
        if (!$pass) {
            Log::error('Email attempted with prohibited domain: ', [$to]);
            return false;
        }
    }
}
