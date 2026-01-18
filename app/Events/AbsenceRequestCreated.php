<?php

namespace App\Events;

use App\Models\AbsenceRequest;
use Illuminate\Broadcasting\Channel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class AbsenceRequestCreated implements ShouldBroadcast
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
        return 'absence.requested';
    }

    public function broadcastWith(): array
    {
        return [
            'id' => $this->absenceRequest->id,
            'student_id' => $this->absenceRequest->student_id,
            'class_id' => $this->absenceRequest->class_id,
            'type' => $this->absenceRequest->type,
            'status' => $this->absenceRequest->status,
            'start_date' => optional($this->absenceRequest->start_date)->toDateString(),
            'end_date' => optional($this->absenceRequest->end_date)->toDateString(),
        ];
    }
}
