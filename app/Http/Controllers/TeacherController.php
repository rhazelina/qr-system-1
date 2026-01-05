<?php

namespace App\Http\Controllers;

use App\Models\TeacherProfile;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class TeacherController extends Controller
{
    public function index(): JsonResponse
    {
        $teachers = TeacherProfile::query()->with(['user', 'homeroomClass'])->latest()->paginate();

        return response()->json($teachers);
    }

    public function import(Request $request): JsonResponse
    {
        $data = $request->validate([
            'items' => ['required', 'array', 'min:1'],
            'items.*.name' => ['required', 'string', 'max:255'],
            'items.*.username' => ['required', 'string', 'max:50', 'distinct', 'unique:users,username'],
            'items.*.email' => ['nullable', 'email'],
            'items.*.password' => ['nullable', 'string', 'min:6'],
            'items.*.nip' => ['required', 'string', 'distinct', 'unique:teacher_profiles,nip'],
            'items.*.phone' => ['nullable', 'string', 'max:30'],
            'items.*.contact' => ['nullable', 'string', 'max:50'],
            'items.*.homeroom_class_id' => ['nullable', 'exists:classes,id'],
            'items.*.subject' => ['nullable', 'string', 'max:100'],
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
                    'user_type' => 'teacher',
                ]);

                $created->push($user->teacherProfile()->create([
                    'nip' => $item['nip'],
                    'homeroom_class_id' => $item['homeroom_class_id'] ?? null,
                    'subject' => $item['subject'] ?? null,
                ]));
            }
        });

        return response()->json([
            'created' => $created->count(),
            'teachers' => $created->load(['user', 'homeroomClass']),
        ], 201);
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'username' => ['required', 'string', 'max:50', 'unique:users,username'],
            'email' => ['nullable', 'email'],
            'password' => ['required', 'string', 'min:6'],
            'nip' => ['required', 'string', 'unique:teacher_profiles,nip'],
            'phone' => ['nullable', 'string', 'max:30'],
            'contact' => ['nullable', 'string', 'max:50'],
            'homeroom_class_id' => ['nullable', 'exists:classes,id'],
            'subject' => ['nullable', 'string', 'max:100'],
        ]);

        $teacher = DB::transaction(function () use ($data) {
            $user = User::create([
                'name' => $data['name'],
                'username' => $data['username'],
                'email' => $data['email'] ?? null,
                'password' => Hash::make($data['password']),
                'phone' => $data['phone'] ?? null,
                'contact' => $data['contact'] ?? null,
                'user_type' => 'teacher',
            ]);

            return $user->teacherProfile()->create([
                'nip' => $data['nip'],
                'homeroom_class_id' => $data['homeroom_class_id'] ?? null,
                'subject' => $data['subject'] ?? null,
            ]);
        });

        return response()->json($teacher->load(['user', 'homeroomClass']), 201);
    }

    public function show(TeacherProfile $teacher): JsonResponse
    {
        return response()->json($teacher->load(['user', 'homeroomClass', 'schedules']));
    }

    public function update(Request $request, TeacherProfile $teacher): JsonResponse
    {
        $data = $request->validate([
            'name' => ['sometimes', 'string', 'max:255'],
            'email' => ['nullable', 'email'],
            'password' => ['nullable', 'string', 'min:6'],
            'phone' => ['nullable', 'string', 'max:30'],
            'contact' => ['nullable', 'string', 'max:50'],
            'homeroom_class_id' => ['nullable', 'exists:classes,id'],
            'subject' => ['nullable', 'string', 'max:100'],
        ]);

        DB::transaction(function () use ($data, $teacher): void {
            if (isset($data['name']) || isset($data['email']) || isset($data['password']) || isset($data['phone']) || isset($data['contact'])) {
                $teacher->user->update([
                    'name' => $data['name'] ?? $teacher->user->name,
                    'email' => $data['email'] ?? $teacher->user->email,
                    'password' => isset($data['password']) ? Hash::make($data['password']) : $teacher->user->password,
                    'phone' => $data['phone'] ?? $teacher->user->phone,
                    'contact' => $data['contact'] ?? $teacher->user->contact,
                ]);
            }

            $teacher->update([
                'homeroom_class_id' => $data['homeroom_class_id'] ?? $teacher->homeroom_class_id,
                'subject' => $data['subject'] ?? $teacher->subject,
            ]);
        });

        return response()->json($teacher->fresh()->load(['user', 'homeroomClass']));
    }

    public function destroy(TeacherProfile $teacher): JsonResponse
    {
        $teacher->user()->delete();

        return response()->json(['message' => 'Deleted']);
    }
}
