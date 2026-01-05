<?php

namespace App\Http\Controllers;

use App\Models\TimeSlot;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TimeSlotController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json(TimeSlot::latest()->paginate());
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string'],
            'start_time' => ['required', 'date_format:H:i'],
            'end_time' => ['required', 'date_format:H:i', 'after:start_time'],
        ]);

        $slot = TimeSlot::create($data);

        return response()->json($slot, 201);
    }

    public function show(TimeSlot $timeSlot): JsonResponse
    {
        return response()->json($timeSlot);
    }

    public function update(Request $request, TimeSlot $timeSlot): JsonResponse
    {
        $data = $request->validate([
            'name' => ['sometimes', 'string'],
            'start_time' => ['sometimes', 'date_format:H:i'],
            'end_time' => ['sometimes', 'date_format:H:i', 'after:start_time'],
        ]);

        $timeSlot->update($data);

        return response()->json($timeSlot);
    }

    public function destroy(TimeSlot $timeSlot): JsonResponse
    {
        $timeSlot->delete();

        return response()->json(['message' => 'Deleted']);
    }
}
