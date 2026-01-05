<?php

namespace App\Http\Controllers;

use App\Models\StudentProfile;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class StudentController extends Controller
{
    public function index(): JsonResponse
    {
        $students = StudentProfile::query()->with(['user', 'classRoom'])->latest()->paginate();

        return response()->json($students);
    }

    public function import(Request $request): JsonResponse
    {
        $data = $request->validate([
            'items' => ['required', 'array', 'min:1'],
            'items.*.name' => ['required', 'string', 'max:255'],
            'items.*.username' => ['required', 'string', 'max:50', 'distinct', 'unique:users,username'],
            'items.*.email' => ['nullable', 'email'],
            'items.*.password' => ['nullable', 'string', 'min:6'],
            'items.*.nisn' => ['required', 'string', 'distinct', 'unique:student_profiles,nisn'],
            'items.*.nis' => ['required', 'string', 'distinct', 'unique:student_profiles,nis'],
            'items.*.gender' => ['required', 'in:L,P'],
            'items.*.address' => ['required', 'string'],
            'items.*.class_id' => ['required', 'exists:classes,id'],
            'items.*.phone' => ['nullable', 'string', 'max:30'],
            'items.*.contact' => ['nullable', 'string', 'max:50'],
        ]);

        $created = collect();

        DB::transaction(function () use ($created, $data): void {
            foreach ($data['items'] as $item) {
                $user = User::create([
                    'name' => $item['name'],
                    'username' => $item['username'],
                    'email' => $item['email'] ?? null,
                    'password' => Hash::make($item['password'] ?? 'password123'),
                    'phone' => $item['phone'] ?? null,
                    'contact' => $item['contact'] ?? null,
                    'user_type' => 'student',
                ]);

                $created->push($user->studentProfile()->create([
                    'nisn' => $item['nisn'],
                    'nis' => $item['nis'],
                    'gender' => $item['gender'],
                    'address' => $item['address'],
                    'class_id' => $item['class_id'],
                ]));
            }
        });

        return response()->json([
            'created' => $created->count(),
            'students' => $created->load(['user', 'classRoom']),
        ], 201);
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'username' => ['required', 'string', 'max:50', 'unique:users,username'],
            'email' => ['nullable', 'email'],
            'password' => ['required', 'string', 'min:6'],
            'nisn' => ['required', 'string', 'unique:student_profiles,nisn'],
            'nis' => ['required', 'string', 'unique:student_profiles,nis'],
            'gender' => ['required', 'in:L,P'],
            'address' => ['required', 'string'],
            'class_id' => ['required', 'exists:classes,id'],
            'phone' => ['nullable', 'string', 'max:30'],
            'contact' => ['nullable', 'string', 'max:50'],
        ]);

        $student = DB::transaction(function () use ($data) {
            $user = User::create([
                'name' => $data['name'],
                'username' => $data['username'],
                'email' => $data['email'] ?? null,
                'password' => Hash::make($data['password']),
                'phone' => $data['phone'] ?? null,
                'contact' => $data['contact'] ?? null,
                'user_type' => 'student',
            ]);

            return $user->studentProfile()->create([
                'nisn' => $data['nisn'],
                'nis' => $data['nis'],
                'gender' => $data['gender'],
                'address' => $data['address'],
                'class_id' => $data['class_id'],
            ]);
        });

        return response()->json($student->load(['user', 'classRoom']), 201);
    }

    public function show(StudentProfile $student): JsonResponse
    {
        return response()->json($student->load(['user', 'classRoom', 'attendances']));
    }

    public function update(Request $request, StudentProfile $student): JsonResponse
    {
        $data = $request->validate([
            'name' => ['sometimes', 'string', 'max:255'],
            'email' => ['nullable', 'email'],
            'password' => ['nullable', 'string', 'min:6'],
            'gender' => ['sometimes', 'in:L,P'],
            'address' => ['sometimes', 'string'],
            'class_id' => ['sometimes', 'exists:classes,id'],
            'phone' => ['nullable', 'string', 'max:30'],
            'contact' => ['nullable', 'string', 'max:50'],
        ]);

        DB::transaction(function () use ($data, $student): void {
            if (isset($data['name']) || isset($data['email']) || isset($data['password']) || isset($data['phone']) || isset($data['contact'])) {
                $student->user->update([
                    'name' => $data['name'] ?? $student->user->name,
                    'email' => $data['email'] ?? $student->user->email,
                    'password' => isset($data['password']) ? Hash::make($data['password']) : $student->user->password,
                    'phone' => $data['phone'] ?? $student->user->phone,
                    'contact' => $data['contact'] ?? $student->user->contact,
                ]);
            }

            $student->update([
                'gender' => $data['gender'] ?? $student->gender,
                'address' => $data['address'] ?? $student->address,
                'class_id' => $data['class_id'] ?? $student->class_id,
            ]);
        });

        return response()->json($student->fresh()->load(['user', 'classRoom']));
    }

    public function destroy(StudentProfile $student): JsonResponse
    {
        $student->user()->delete();

        return response()->json(['message' => 'Deleted']);
    }
}
