<?php

namespace App\Http\Controllers;

use App\Models\Classes;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ClassController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json(Classes::query()->latest()->get());
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'grade' => ['required', 'string', 'max:10'],
            'label' => ['required', 'string', 'max:20'],
        ]);

        $class = Classes::create($data);

        return response()->json($class, 201);
    }

    public function show(Classes $class): JsonResponse
    {
        return response()->json($class->load(['students', 'homeroomTeacher']));
    }

    public function update(Request $request, Classes $class): JsonResponse
    {
        $data = $request->validate([
            'grade' => ['sometimes', 'string', 'max:10'],
            'label' => ['sometimes', 'string', 'max:20'],
        ]);

        $class->update($data);

        return response()->json($class);
    }

    public function destroy(Classes $class): JsonResponse
    {
        $class->delete();

        return response()->json(['message' => 'Deleted']);
    }
}
