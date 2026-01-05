<?php

namespace App\Http\Controllers;

use App\Models\Subject;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SubjectController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json(Subject::latest()->paginate());
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'code' => ['required', 'string', 'unique:subjects,code'],
            'name' => ['required', 'string'],
        ]);

        $subject = Subject::create($data);

        return response()->json($subject, 201);
    }

    public function show(Subject $subject): JsonResponse
    {
        return response()->json($subject);
    }

    public function update(Request $request, Subject $subject): JsonResponse
    {
        $data = $request->validate([
            'code' => ['sometimes', 'string', 'unique:subjects,code,'.$subject->id],
            'name' => ['sometimes', 'string'],
        ]);

        $subject->update($data);

        return response()->json($subject);
    }

    public function destroy(Subject $subject): JsonResponse
    {
        $subject->delete();

        return response()->json(['message' => 'Deleted']);
    }
}
