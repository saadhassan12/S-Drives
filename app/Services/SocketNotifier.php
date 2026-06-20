<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SocketNotifier
{
    public static function chatStarted(array $payload): void
    {
        $baseUrl = rtrim((string) env('SOCKET_SERVER_URL', 'http://127.0.0.1:6001'), '/');
        $secret = (string) env('SOCKET_INTERNAL_SECRET', '');

        if ($secret === '') {
            Log::warning('SOCKET_INTERNAL_SECRET is missing. Skipping chatStarted notify.');
            return;
        }

        try {
            Http::timeout(5)
                ->withHeaders([
                    'x-socket-secret' => $secret,
                ])
                ->post($baseUrl . '/internal/chat-started', $payload)
                ->throw();
        } catch (\Throwable $e) {
            Log::warning('Socket notifier failed: ' . $e->getMessage());
        }
    }

    /**
     * Tell Node to emit chat:new-message to chat:{roomId} (covers REST POST /api/chat/messages).
     */
    public static function broadcastNewChatMessage(\App\Models\ChatMessage $message): void
    {
        $baseUrl = rtrim((string) env('SOCKET_SERVER_URL', 'http://127.0.0.1:6001'), '/');
        $secret = (string) env('SOCKET_INTERNAL_SECRET', '');

        if ($secret === '') {
            Log::warning('SOCKET_INTERNAL_SECRET is missing. Skipping broadcastNewChatMessage.');

            return;
        }

        try {
            Http::timeout(5)
                ->withHeaders([
                    'x-socket-secret' => $secret,
                    'Content-Type' => 'application/json',
                    'Accept' => 'application/json',
                ])
                ->post($baseUrl.'/internal/broadcast-chat-message', [
                    'chat_room_id' => $message->chat_room_id,
                    'message' => $message->toArray(),
                ])
                ->throw();
        } catch (\Throwable $e) {
            Log::warning('Socket broadcastNewChatMessage failed: '.$e->getMessage());
        }
    }
}
