<?php

namespace App\Events;

use App\Models\Qrcode;
use Illuminate\Broadcasting\Channel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Queue\SerializesModels;

class QrSessionCreated implements ShouldBroadcast
{
    use SerializesModels;

    public function __construct(public Qrcode $qrcode)
    {
    }

    public function broadcastOn(): array
    {
        return [new Channel('schedules.'.$this->qrcode->schedule_id)];
    }

    public function broadcastAs(): string
    {
        return 'qr.generated';
    }

    public function broadcastWith(): array
    {
        return [
            'token' => $this->qrcode->token,
            'type' => $this->qrcode->type,
            'schedule_id' => $this->qrcode->schedule_id,
            'expires_at' => optional($this->qrcode->expires_at)->toIso8601String(),
        ];
    }
}
