<?php

namespace App\Http\Controllers;

use App\Events\AttendanceRecorded;
use App\Models\Attendance;
use App\Models\AttendanceAttachment;
use App\Models\Qrcode;
use App\Models\Schedule;
use Illuminate\Http\UploadedFile;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;

class AttendanceController extends Controller
{
    public function scan(Request $request): JsonResponse
    {
        $data = $request->validate([
            'token' => ['required', 'string', 'exists:qrcodes,token'],
            'device_id' => ['nullable', 'integer'],
        ]);

        $qr = Qrcode::with('schedule')->where('token', $data['token'])->firstOrFail();

        if (!$qr->is_active || $qr->isExpired()) {
            return response()->json(['message' => 'QR tidak aktif atau sudah kadaluarsa'], 422);
        }

        $user = $request->user();
        $now = now();

        if ($qr->type === 'student' && $user->user_type !== 'student') {
            return response()->json(['message' => 'QR hanya untuk siswa'], 403);
        }

        if ($qr->type === 'teacher' && $user->user_type !== 'teacher') {
            return response()->json(['message' => 'QR hanya untuk guru'], 403);
        }

        if ($user->user_type === 'student' && !$user->studentProfile) {
            return response()->json(['message' => 'Profil siswa tidak ditemukan'], 422);
        }

        if ($user->user_type === 'teacher' && !$user->teacherProfile) {
            return response()->json(['message' => 'Profil guru tidak ditemukan'], 422);
        }

        if ($user->user_type === 'student') {
            if (!$data['device_id'] ?? null) {
                return response()->json(['message' => 'Device belum terdaftar'], 422);
            }

            $device = $user->devices()->where('id', $data['device_id'])->where('active', true)->first();
            if (!$device) {
                return response()->json(['message' => 'Device tidak valid'], 422);
            }

            $device->update(['last_used_at' => $now]);
        }

        if ($qr->type === 'student' && $qr->schedule && $user->studentProfile && $qr->schedule->class_id !== $user->studentProfile->class_id) {
            return response()->json(['message' => 'QR bukan untuk kelas kamu'], 403);
        }

        if ($qr->type === 'teacher' && $qr->schedule && $qr->schedule->teacher_id !== optional($user->teacherProfile)->id) {
            return response()->json(['message' => 'QR bukan untuk guru ini'], 403);
        }

        $attributes = [
            'attendee_type' => $user->user_type,
            'schedule_id' => $qr->schedule_id,
            'student_id' => $user->studentProfile->id ?? null,
            'teacher_id' => $user->teacherProfile->id ?? null,
        ];

        $existing = Attendance::where($attributes)->first();
        if ($existing) {
            return response()->json([
                'message' => 'Presensi sudah tercatat',
                'attendance' => $existing->load(['student.user', 'teacher.user', 'schedule']),
            ]);
        }

        $attendance = Attendance::create([
            ...$attributes,
            'date' => $now,
            'qrcode_id' => $qr->id,
            'status' => 'present',
            'checked_in_at' => $now,
            'source' => 'qrcode',
        ]);

        AttendanceRecorded::dispatch($attendance);

        Log::info('attendance.recorded', [
            'attendance_id' => $attendance->id,
            'schedule_id' => $attendance->schedule_id,
            'user_id' => $user->id,
            'attendee_type' => $attendance->attendee_type,
        ]);

        return response()->json($attendance->load(['student.user', 'teacher.user', 'schedule']));
    }

    public function me(Request $request): JsonResponse
    {
        if ($request->user()->user_type !== 'student' || !$request->user()->studentProfile) {
            abort(403, 'Hanya untuk siswa');
        }

        $attendances = Attendance::query()
            ->with(['schedule.teacher.user', 'schedule.class'])
            ->where('student_id', $request->user()->studentProfile->id)
            ->latest('date')
            ->paginate();

        return response()->json($attendances);
    }

    public function recap(Request $request): JsonResponse
    {
        $request->validate([
            'month' => ['required', 'date_format:Y-m'],
        ]);

        $start = \Illuminate\Support\Carbon::createFromFormat('Y-m', $request->string('month'))->startOfMonth();
        $end = $start->copy()->endOfMonth();

        $summary = Attendance::selectRaw('attendee_type, status, count(*) as total')
            ->whereBetween('date', [$start, $end])
            ->groupBy('attendee_type', 'status')
            ->get();

        return response()->json($summary);
    }

    public function summaryBySchedule(Request $request, Schedule $schedule): JsonResponse
    {
        $this->authorizeSchedule($request, $schedule);

        $data = Attendance::selectRaw('status, count(*) as total')
            ->where('schedule_id', $schedule->id)
            ->groupBy('status')
            ->get();

        return response()->json($data);
    }

    public function summaryByClass(Request $request, \App\Models\Classes $class): JsonResponse
    {
        if ($request->user()->user_type === 'teacher') {
            $teacherId = optional($request->user()->teacherProfile)->id;
            $ownsSchedules = $class->schedules()->where('teacher_id', $teacherId)->exists();
            $isHomeroom = optional($class->homeroomTeacher)->id === $teacherId;
            if (!$ownsSchedules && !$isHomeroom) {
                abort(403, 'Tidak boleh melihat rekap kelas ini');
            }
        }

        $data = Attendance::selectRaw('status, count(*) as total')
            ->whereHas('schedule', fn ($q) => $q->where('class_id', $class->id))
            ->groupBy('status')
            ->get();

        return response()->json($data);
    }

    public function attach(Request $request, Attendance $attendance): JsonResponse
    {
        $request->validate([
            'file' => ['required', 'file', 'max:5120'],
        ]);

        $file = $request->file('file');
        $path = $this->storeAttachment($file);

        $attachment = $attendance->attachments()->create([
            'path' => $path,
            'original_name' => $file->getClientOriginalName(),
            'mime_type' => $file->getMimeType(),
            'size' => $file->getSize(),
        ]);

        return response()->json([
            'attachment' => $attachment,
            'url' => $this->signedUrl($attachment->path),
        ], 201);
    }

    protected function storeAttachment(UploadedFile $file): string
    {
        return $file->store('attendance-attachments');
    }

    public function void(Request $request, Attendance $attendance): JsonResponse
    {
        $this->authorizeSchedule($request, $attendance->schedule);

        $attendance->delete();

        return response()->json(['message' => 'Scan dibatalkan']);
    }

    protected function signedUrl(string $path): string
    {
        try {
            return Storage::temporaryUrl($path, now()->addMinutes(10));
        } catch (\Throwable $e) {
            return Storage::url($path);
        }
    }

    protected function authorizeSchedule(Request $request, Schedule $schedule): void
    {
        if ($request->user()->user_type === 'teacher' && $schedule->teacher_id !== optional($request->user()->teacherProfile)->id) {
            abort(403, 'Tidak boleh mengakses jadwal ini');
        }
    }

    public function bySchedule(Request $request, Schedule $schedule): JsonResponse
    {
        if ($request->user()->user_type === 'teacher' && $schedule->teacher_id !== optional($request->user()->teacherProfile)->id) {
            abort(403, 'Tidak boleh melihat presensi jadwal ini');
        }

        $attendances = Attendance::query()
            ->with(['student.user', 'teacher.user'])
            ->where('schedule_id', $schedule->id)
            ->latest('checked_in_at')
            ->get();

        return response()->json($attendances);
    }

    public function markExcuse(Request $request, Attendance $attendance): JsonResponse
    {
        if ($request->user()->user_type === 'teacher' && $attendance->schedule->teacher_id !== optional($request->user()->teacherProfile)->id) {
            abort(403, 'Tidak boleh mengubah presensi jadwal ini');
        }

        $data = $request->validate([
            'status' => ['required', 'in:late,excused,sick,absent,present,dinas,izin'],
            'reason' => ['nullable', 'string'],
        ]);

        $attendance->update([
            'status' => $data['status'],
            'reason' => $data['reason'] ?? null,
            'source' => 'manual',
        ]);

        return response()->json($attendance);
    }

    public function export(Request $request): StreamedResponse
    {
        $request->validate([
            'schedule_id' => ['nullable', 'exists:schedules,id'],
            'class_id' => ['nullable', 'exists:classes,id'],
            'from' => ['nullable', 'date'],
            'to' => ['nullable', 'date'],
        ]);

        $query = Attendance::with(['student.user', 'teacher.user', 'schedule.class']);

        if ($request->filled('schedule_id')) {
            $schedule = Schedule::findOrFail($request->integer('schedule_id'));
            if ($request->user()->user_type === 'teacher' && $schedule->teacher_id !== optional($request->user()->teacherProfile)->id) {
                abort(403, 'Tidak boleh mengekspor jadwal ini');
            }
            $query->where('schedule_id', $schedule->id);
        }

        if ($request->filled('class_id')) {
            $query->whereHas('schedule', fn ($q) => $q->where('class_id', $request->integer('class_id')));
        }

        if ($request->filled('from')) {
            $query->whereDate('date', '>=', $request->date('from'));
        }
        if ($request->filled('to')) {
            $query->whereDate('date', '<=', $request->date('to'));
        }

        $attendances = $query->orderBy('checked_in_at')->get();

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="attendance_export.csv"',
        ];

        $callback = static function () use ($attendances): void {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['Type', 'Name', 'Status', 'Checked In At', 'Reason', 'Class', 'Schedule']);

            foreach ($attendances as $attendance) {
                $name = $attendance->attendee_type === 'student'
                    ? optional($attendance->student?->user)->name
                    : optional($attendance->teacher?->user)->name;

                fputcsv($handle, [
                    $attendance->attendee_type,
                    $name,
                    $attendance->status,
                    optional($attendance->checked_in_at)->toDateTimeString(),
                    $attendance->reason,
                    optional($attendance->schedule?->class)->label,
                    optional($attendance->schedule)->title,
                ]);
            }

            fclose($handle);
        };

        return response()->stream($callback, 200, $headers);
    }
}
