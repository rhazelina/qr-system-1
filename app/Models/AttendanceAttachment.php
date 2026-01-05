<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AttendanceAttachment extends Model
{
    protected $fillable = [
        'attendance_id',
        'path',
        'original_name',
        'mime_type',
        'size',
    ];

    public function attendance(): BelongsTo
    {
        return $this->belongsTo(Attendance::class);
    }
}
