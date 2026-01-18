<?php

namespace App\Events;

use App\Models\AbsenceRequest;
use Illuminate\Broadcasting\Channel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class AbsenceRequestUpdated implements ShouldBroadcast
{
    use Dispatchable, SerializesModels;

    public function __construct(public AbsenceRequest $absenceRequest)
    {
    }

    public function broadcastOn(): array
    {
        return [
            new Channel('waka.absence-requests'),
            new Channel('classes.'.$this->absenceRequest->class_id),
        ];
    }

    public function broadcastAs(): string
    {
        return 'absence.updated';
    }

    public function broadcastWith(): array
    {
        return [
            'id' => $this->absenceRequest->id,
            'student_id' => $this->absenceRequest->student_id,
            'class_id' => $this->absenceRequest->class_id,
            'type' => $this->absenceRequest->type,
            'status' => $this->absenceRequest->status,
            'approved_by' => $this->absenceRequest->approved_by,
            'approved_at' => optional($this->absenceRequest->approved_at)->toIso8601String(),
        ];
    }
}
