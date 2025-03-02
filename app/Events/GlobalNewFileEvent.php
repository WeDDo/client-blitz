<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class GlobalNewFileEvent implements ShouldBroadcast, ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public string $url,
        public string $name,
    )
    {
        //
    }

    public function broadcastOn(): array
    {
        return [
            new Channel('ripper'),
        ];
    }

    public function broadcastAs(): string
    {
        return 'GlobalNewFileEvent';
    }
}
