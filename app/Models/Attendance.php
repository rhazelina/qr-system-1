<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Attendance extends Model
{
    protected $fillable = [
        'attendee_type',
        'date',
        'student_id',
        'teacher_id',
        'qrcode_id',
        'reason',
        'reason_file',
        'status',
        'checked_in_at',
        'schedule_id',
        'source',
    ];

    protected $casts = [
        'date' => 'datetime',
        'checked_in_at' => 'datetime',
    ];

    public function student(): BelongsTo
    {
        return $this->belongsTo(StudentProfile::class);
    }

    public function teacher(): BelongsTo
    {
        return $this->belongsTo(TeacherProfile::class);
    }

    public function schedule(): BelongsTo
    {
        return $this->belongsTo(Schedule::class);
    }

    public function qrcode(): BelongsTo
    {
        return $this->belongsTo(Qrcode::class);
    }

    public function attachments(): HasMany
    {
        return $this->hasMany(AttendanceAttachment::class);
    }
}
