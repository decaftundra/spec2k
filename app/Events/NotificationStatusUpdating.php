<?php

namespace App\Events;

use App\ShopFindings\ShopFinding;
use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use App\Notification;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class NotificationStatusUpdating
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $notification;
    public $shopFinding;
    
    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(Notification $notification)
    {
        $this->notification = $notification;
        
        $shopFinding = ShopFinding::withTrashed()->find((string) $notification->id);
        
        if ($shopFinding) {
            $this->shopFinding = $shopFinding;
        }
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new PrivateChannel('channel-name');
    }
}
