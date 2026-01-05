<?php

namespace App\Http\Controllers;

use App\Models\SchoolYear;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SchoolYearController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json(SchoolYear::latest()->paginate());
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string'],
            'start_year' => ['required', 'integer'],
            'end_year' => ['required', 'integer', 'gte:start_year'],
            'active' => ['nullable', 'boolean'],
        ]);

        $year = SchoolYear::create($data);

        return response()->json($year, 201);
    }

    public function show(SchoolYear $schoolYear): JsonResponse
    {
        return response()->json($schoolYear);
    }

    public function update(Request $request, SchoolYear $schoolYear): JsonResponse
    {
        $data = $request->validate([
            'name' => ['sometimes', 'string'],
            'start_year' => ['sometimes', 'integer'],
            'end_year' => ['sometimes', 'integer'],
            'active' => ['nullable', 'boolean'],
        ]);

        $schoolYear->update($data);

        return response()->json($schoolYear);
    }

    public function destroy(SchoolYear $schoolYear): JsonResponse
    {
        $schoolYear->delete();

        return response()->json(['message' => 'Deleted']);
    }
}
