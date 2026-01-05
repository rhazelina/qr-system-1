<?php

namespace App\Http\Controllers;

use App\Models\Schedule;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class ScheduleController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = Schedule::query()->with(['teacher.user', 'class']);

        if ($request->user()->user_type === 'teacher') {
            $query->where('teacher_id', optional($request->user()->teacherProfile)->id);
        }

        if ($request->filled('class_id')) {
            $query->where('class_id', $request->integer('class_id'));
        }

        if ($request->filled('date')) {
            $day = Carbon::parse($request->string('date'))->format('l');
            $query->where('day', $day);
        }

        return response()->json($query->latest()->paginate());
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'day' => ['required', 'string'],
            'start_time' => ['required', 'date_format:H:i'],
            'end_time' => ['required', 'date_format:H:i', 'after:start_time'],
            'title' => ['required', 'string', 'max:255'],
            'teacher_id' => ['required', 'exists:teacher_profiles,id'],
            'class_id' => ['required', 'exists:classes,id'],
            'room' => ['nullable', 'string', 'max:50'],
            'semester' => ['required', 'integer'],
            'year' => ['required', 'integer'],
        ]);

        $schedule = Schedule::create($data);

        return response()->json($schedule->load(['teacher.user', 'class']), 201);
    }

    public function show(Request $request, Schedule $schedule): JsonResponse
    {
        if ($request->user()->user_type === 'teacher' && $schedule->teacher_id !== optional($request->user()->teacherProfile)->id) {
            abort(403, 'Tidak boleh melihat jadwal guru lain');
        }

        return response()->json($schedule->load(['teacher.user', 'class', 'qrcodes', 'attendances']));
    }

    public function update(Request $request, Schedule $schedule): JsonResponse
    {
        $data = $request->validate([
            'day' => ['sometimes', 'string'],
            'start_time' => ['sometimes', 'date_format:H:i'],
            'end_time' => ['sometimes', 'date_format:H:i', 'after:start_time'],
            'title' => ['sometimes', 'string', 'max:255'],
            'teacher_id' => ['sometimes', 'exists:teacher_profiles,id'],
            'class_id' => ['sometimes', 'exists:classes,id'],
            'room' => ['nullable', 'string', 'max:50'],
            'semester' => ['sometimes', 'integer'],
            'year' => ['sometimes', 'integer'],
        ]);

        $schedule->update($data);

        return response()->json($schedule->load(['teacher.user', 'class']));
    }

    public function destroy(Schedule $schedule): JsonResponse
    {
        $schedule->delete();

        return response()->json(['message' => 'Deleted']);
    }
}
