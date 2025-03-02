<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class NewFileEvent implements ShouldBroadcast, ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public string           $url,
        public string           $name,
        private readonly string $path,
    )
    {
        //
    }

    public function broadcastOn(): array
    {
        return [
            new Channel(str_replace('/', '_', rtrim("files.$this->path", '/'))),
        ];
    }

    public function broadcastAs(): string
    {
        return 'NewFileEvent';
    }
}
