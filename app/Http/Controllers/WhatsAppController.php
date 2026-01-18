<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WhatsAppController extends Controller
{
    public function sendText(Request $request): JsonResponse
    {
        $data = $request->validate([
            'to' => ['required', 'string', 'max:30'],
            'message' => ['required', 'string'],
        ]);

        return $this->dispatchRequest('send-text', $data);
    }

    public function sendMedia(Request $request): JsonResponse
    {
        $data = $request->validate([
            'to' => ['required', 'string', 'max:30'],
            'mediaBase64' => ['required', 'string'],
            'filename' => ['required', 'string', 'max:200'],
            'caption' => ['nullable', 'string'],
        ]);

        return $this->dispatchRequest('send-media', $data);
    }

    protected function dispatchRequest(string $endpoint, array $payload): JsonResponse
    {
        $baseUrl = config('services.whatsapp.base_url');
        $token = config('services.whatsapp.token');

        if (!$baseUrl) {
            return response()->json([
                'message' => 'WhatsApp provider not configured',
            ], 501);
        }

        $client = Http::timeout(config('services.whatsapp.timeout', 10));

        if ($token) {
            $client = $client->withToken($token);
        }

        $response = $client->post(rtrim($baseUrl, '/').'/'.$endpoint, $payload);

        if (!$response->successful()) {
            Log::warning('whatsapp.send.failed', [
                'endpoint' => $endpoint,
                'status' => $response->status(),
            ]);
            return response()->json([
                'message' => 'Failed to send WhatsApp message',
                'error' => $response->json() ?? $response->body(),
            ], $response->status());
        }

        Log::info('whatsapp.send.success', [
            'endpoint' => $endpoint,
        ]);

        return response()->json([
            'message' => 'Sent',
            'data' => $response->json() ?? $response->body(),
        ]);
    }
}
