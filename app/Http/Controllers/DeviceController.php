<?php

namespace App\Http\Controllers;

use App\Models\Device;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DeviceController extends Controller
{
    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'identifier' => ['required', 'string'],
            'name' => ['nullable', 'string', 'max:255'],
            'platform' => ['nullable', 'string', 'max:100'],
        ]);

        $user = $request->user();

        if ($user->user_type === 'student') {
            $user->devices()->update(['active' => false]);
        }

        $device = $user->devices()->updateOrCreate(
            ['identifier' => $data['identifier']],
            [
                'name' => $data['name'] ?? $request->userAgent(),
                'platform' => $data['platform'] ?? null,
                'active' => true,
                'last_used_at' => now(),
            ]
        );

        return response()->json($device, 201);
    }

    public function destroy(Request $request, Device $device): JsonResponse
    {
        if ($device->user_id !== $request->user()->id) {
            abort(403, 'Tidak boleh menghapus device ini');
        }

        $device->delete();

        return response()->json(['message' => 'Device removed']);
    }
}
