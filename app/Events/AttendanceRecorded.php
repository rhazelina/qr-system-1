<?php

namespace App\Events;

use App\Models\Attendance;
use Illuminate\Broadcasting\Channel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Queue\SerializesModels;

class AttendanceRecorded implements ShouldBroadcast
{
    use SerializesModels;

    public function __construct(public Attendance $attendance)
    {
    }

    public function broadcastOn(): array
    {
        return [new Channel('schedules.'.$this->attendance->schedule_id)];
    }

    public function broadcastAs(): string
    {
        return 'attendance.recorded';
    }

    public function broadcastWith(): array
    {
        return [
            'attendee_type' => $this->attendance->attendee_type,
            'schedule_id' => $this->attendance->schedule_id,
            'status' => $this->attendance->status,
            'name' => $this->attendance->attendee_type === 'student'
                ? optional($this->attendance->student?->user)->name
                : optional($this->attendance->teacher?->user)->name,
        ];
    }
}
