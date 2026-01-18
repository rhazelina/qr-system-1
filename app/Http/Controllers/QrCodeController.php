<?php

namespace App\Http\Controllers;

use App\Events\QrSessionCreated;
use App\Models\Qrcode;
use App\Models\Schedule;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use SimpleSoftwareIO\QrCode\Facades\QrCode as QrCodeFacade;

class QrCodeController extends Controller
{
    public function active(Request $request): JsonResponse
    {
        $items = Qrcode::query()
            ->with(['schedule.class', 'issuer'])
            ->where('is_active', true)
            ->latest()
            ->paginate();

        return response()->json($items);
    }

    public function generate(Request $request): JsonResponse
    {
        $data = $request->validate([
            'schedule_id' => ['required', 'exists:schedules,id'],
            'type' => ['required', 'in:student,teacher'],
            'expires_in_minutes' => ['nullable', 'integer', 'min:1', 'max:240'],
        ]);

        $schedule = Schedule::with(['class.homeroomTeacher'])->findOrFail($data['schedule_id']);
        $user = $request->user();

        if ($user->user_type === 'teacher') {
            $teacherId = optional($user->teacherProfile)->id;
            $isOwner = $schedule->teacher_id === $teacherId;
            $isHomeroom = optional($schedule->class?->homeroomTeacher)->id === $teacherId;

            if (!$isOwner && !$isHomeroom) {
                abort(403, 'Guru tidak boleh membuat QR untuk jadwal lain');
            }
        }

        if ($user->user_type === 'student') {
            $studentProfile = $user->studentProfile;

            if (!$studentProfile || !$studentProfile->is_class_officer) {
                abort(403, 'Pengurus kelas saja yang boleh membuat QR');
            }

            if ($schedule->class_id !== $studentProfile->class_id) {
                abort(403, 'Pengurus kelas hanya boleh membuat QR untuk kelasnya');
            }

            if ($data['type'] !== 'student') {
                abort(422, 'Pengurus kelas hanya boleh membuat QR siswa');
            }
        }
        $expiresAt = now()->addMinutes($data['expires_in_minutes'] ?? 15);

        $qr = Qrcode::create([
            'token' => Str::uuid()->toString(),
            'type' => $data['type'],
            'schedule_id' => $schedule->id,
            'issued_by' => $request->user()->id,
            'status' => 'available',
            'expires_at' => $expiresAt,
            'is_active' => true,
        ]);

        $payload = [
            'token' => $qr->token,
            'type' => $qr->type,
            'schedule_id' => $qr->schedule_id,
            'expires_at' => $expiresAt->toIso8601String(),
        ];

        Log::info('qrcode.generated', [
            'schedule_id' => $qr->schedule_id,
            'type' => $qr->type,
            'issued_by' => $qr->issued_by,
            'expires_at' => $expiresAt->toIso8601String(),
        ]);

        $svg = QrCodeFacade::format('svg')
            ->size(240)
            ->generate(json_encode($payload));

        QrSessionCreated::dispatch($qr);

        return response()->json([
            'qrcode' => $qr->load('schedule'),
            'qr_svg' => base64_encode($svg),
            'payload' => $payload,
        ], 201);
    }

    public function revoke(Request $request, string $token): JsonResponse
    {
        $qr = Qrcode::with('schedule.class.homeroomTeacher')->where('token', $token)->firstOrFail();

        if ($request->user()->user_type === 'teacher') {
            $teacherId = optional($request->user()->teacherProfile)->id;
            $isOwner = $qr->schedule?->teacher_id === $teacherId;
            $isHomeroom = optional($qr->schedule?->class?->homeroomTeacher)->id === $teacherId;

            if (!$isOwner && !$isHomeroom) {
                abort(403, 'Guru tidak boleh mencabut QR ini');
            }
        }

        if ($request->user()->user_type === 'student') {
            $studentProfile = $request->user()->studentProfile;

            if (!$studentProfile || !$studentProfile->is_class_officer) {
                abort(403, 'Pengurus kelas saja yang boleh mencabut QR');
            }

            if ($qr->schedule?->class_id !== $studentProfile->class_id) {
                abort(403, 'Pengurus kelas hanya boleh mencabut QR kelasnya');
            }
        }

        $qr->update(['is_active' => false, 'status' => 'expired']);

        Log::info('qrcode.revoked', [
            'token' => $qr->token,
            'schedule_id' => $qr->schedule_id,
            'revoked_by' => $request->user()->id,
        ]);

        return response()->json(['message' => 'QR revoked']);
    }
}
