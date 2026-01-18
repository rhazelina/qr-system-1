<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class SchedulesBulkUpdated implements ShouldBroadcast
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public int $classId,
        public string $day,
        public int $semester,
        public int $year,
        public int $count
    ) {
    }

    public function broadcastOn(): array
    {
        return [new Channel('classes.'.$this->classId)];
    }

    public function broadcastAs(): string
    {
        return 'schedules.bulk-updated';
    }

    public function broadcastWith(): array
    {
        return [
            'class_id' => $this->classId,
            'day' => $this->day,
            'semester' => $this->semester,
            'year' => $this->year,
            'count' => $this->count,
        ];
    }
}
