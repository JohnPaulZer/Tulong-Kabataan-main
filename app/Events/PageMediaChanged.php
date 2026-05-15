<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class PageMediaChanged implements ShouldBroadcastNow
{
    use Dispatchable;
    use SerializesModels;

    public function __construct(
        public readonly array $item,
        public readonly string $action = 'updated',
    ) {
    }

    public function broadcastOn(): Channel
    {
        return new Channel('public.live');
    }

    public function broadcastAs(): string
    {
        return 'page-media.changed';
    }

    public function broadcastWith(): array
    {
        return [
            'action' => $this->action,
            'item' => $this->item,
            'sent_at' => now()->toIso8601String(),
        ];
    }
}
