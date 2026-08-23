<?php

namespace App\Http\Controllers;

use App\Models\ChatRoom;
use App\Models\User;
use Illuminate\Http\Request;

class SocketController extends Controller
{
    public function me(Request $request)
    {
        $user = $request->user();

        $roomIds = ChatRoom::query()
            ->where('status', 'active')
            ->where(function ($query) use ($user) {
                $query->where('passenger_id', $user->id)
                    ->orWhere('driver_id', $user->id);
            })
            ->pluck('id')
            ->values();

        $socketClientBase = env('SOCKET_SERVER_PUBLIC_URL')
            ?: env('SOCKET_SERVER_URL', '');

        return apiResponse([
            'id' => $user->id,
            'first_name' => $user->first_name,
            'last_name' => $user->last_name,
            'role' => $user->role,
            'last_login_at' => (int) $user->last_login_at,
            'room_ids' => $roomIds,
            'socket_url' => $socketClientBase !== '' ? rtrim((string) $socketClientBase, '/') : null,
        ], 'Socket auth successful.');
    }

    public function updatePresence(Request $request)
    {
        $this->assertSecret($request);

        $data = $request->validate([
            'user_id' => 'required|integer|exists:users,id',
            'is_online' => 'required|boolean',
            'is_app_foreground' => 'sometimes|boolean',
        ]);

        /** @var User $user */
        $user = User::findOrFail($data['user_id']);

        $updates = [
            'is_online' => $data['is_online'],
            'last_seen_at' => now(),
        ];

        if (array_key_exists('is_app_foreground', $data)) {
            $updates['is_app_foreground'] = $data['is_app_foreground'];
        } elseif (!$data['is_online']) {
            $updates['is_app_foreground'] = false;
        }

        $user->forceFill($updates)->save();

        return response()->json(['ok' => true]);
    }

    public function touchActivity(Request $request)
    {
        $this->assertSecret($request);

        $data = $request->validate([
            'user_id' => 'required|integer|exists:users,id',
        ]);

        User::where('id', $data['user_id'])->update(['last_seen_at' => now()]);

        return response()->json(['ok' => true]);
    }

    protected function assertSecret(Request $request): void
    {
        $expected = (string) env('SOCKET_INTERNAL_SECRET', '');
        $provided = (string) $request->header('x-socket-secret', '');

        if ($expected === '' || !hash_equals($expected, $provided)) {
            abort(401, 'Invalid internal socket secret.');
        }
    }
}
