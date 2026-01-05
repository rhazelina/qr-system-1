<?php

namespace App\Http\Controllers;

use App\Models\Semester;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SemesterController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json(Semester::with('schoolYear')->latest()->paginate());
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string'],
            'school_year_id' => ['required', 'exists:school_years,id'],
            'active' => ['nullable', 'boolean'],
        ]);

        $semester = Semester::create($data);

        return response()->json($semester->load('schoolYear'), 201);
    }

    public function show(Semester $semester): JsonResponse
    {
        return response()->json($semester->load('schoolYear'));
    }

    public function update(Request $request, Semester $semester): JsonResponse
    {
        $data = $request->validate([
            'name' => ['sometimes', 'string'],
            'school_year_id' => ['sometimes', 'exists:school_years,id'],
            'active' => ['nullable', 'boolean'],
        ]);

        $semester->update($data);

        return response()->json($semester->load('schoolYear'));
    }

    public function destroy(Semester $semester): JsonResponse
    {
        $semester->delete();

        return response()->json(['message' => 'Deleted']);
    }
}
