<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Queue\SerializesModels;
use App\Models\Odd;
use Illuminate\Support\Facades\Log;

class OddsUpdated implements ShouldBroadcast
{
    use InteractsWithSockets, SerializesModels;

    public $odd;

    public function __construct(Odd $odd)
    {
        if (!$odd) {
            Log::warning('Attempted to broadcast OddsUpdated event with null data.');
            return;
        }

        $this->odd = $odd;
    }

    public function broadcastOn()
    {
        return new Channel('odds-updates');
    }

    public function broadcastAs()
    {
        return 'OddsUpdated';
    }
}
