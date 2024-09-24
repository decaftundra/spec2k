<?php

namespace App\Events;

use App\Segment;
use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class SegmentSaving
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $segment;
    public $action;
    
    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(Segment $segment)
    {
        $this->segment = $segment;
        $this->action = 'Saving';
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
