<?php

namespace App\Http\Controllers;

use App\Models\ChatMessage;
use App\Models\ChatRoom;
use App\Models\Ride;
use App\Models\User;
use App\Services\SocketNotifier;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ChatController extends Controller
{
    public function rooms(Request $request)
    {
        $userId = (int) $request->user()->id;

        $rooms = ChatRoom::query()
            ->select('id', 'ride_id', 'status', 'started_at', 'passenger_id', 'driver_id', 'updated_at')
            ->where(function ($query) use ($userId) {
                $query->where('passenger_id', $userId)
                    ->orWhere('driver_id', $userId);
            })
            ->with([
                'passenger:id,first_name,last_name,profile_picture,is_online,last_seen_at',
                'driver:id,first_name,last_name,profile_picture,is_online,last_seen_at',
                'messages' => function ($query) {
                    $query->select('id', 'chat_room_id', 'sender_id', 'message_type', 'message', 'image_url', 'created_at')
                        ->latest()
                        ->limit(1);
                },
            ])
            ->latest('updated_at')
            ->get()
            ->map(function (ChatRoom $room) {
                $lastMessage = $room->messages->first();

                return [
                    'id' => $room->id,
                    'ride_id' => $room->ride_id,
                    'status' => $room->status,
                    'started_at' => $room->started_at,
                    'passenger' => $room->passenger,
                    'driver' => $room->driver,
                    'last_message' => $lastMessage,
                ];
            });

        return apiResponse($rooms, 'Chat rooms fetched successfully.');
    }

    public function messages(Request $request, ChatRoom $room)
    {
        $this->assertMember($request, $room);

        return apiResponse(
            $this->messageListForRoom($room, $request),
            'Messages fetched successfully.'
        );
    }

    /**
     * GET /api/chat/rides/{ride}/messages — same as room messages, keyed by ride_id.
     */
    public function messagesByRide(Request $request, Ride $ride)
    {
        $room = $this->chatRoomForRideOrAbort($request, $ride);

        return apiResponse(
            $this->messageListForRoom($room, $request),
            'Messages fetched successfully.'
        );
    }

    public function sendMessage(Request $request)
    {
        $request->validate([
            'room_id' => 'nullable|integer|exists:chat_rooms,id',
            'ride_id' => 'nullable|integer|exists:rides,id',
            'message_type' => 'nullable|in:text,image',
            'message' => 'nullable|string|max:5000',
            'image_url' => 'nullable|string|max:2048',
            'image' => 'nullable',
            'photo' => 'nullable|image|mimes:jpg,jpeg,png,webp,gif|max:' . config('upload.max_image_kb'),
            'file' => 'nullable|image|mimes:jpg,jpeg,png,webp,gif|max:' . config('upload.max_image_kb'),
            'image_base64' => 'nullable|string',
            'meta' => 'nullable|array',
        ]);

        if ($request->hasFile('image')) {
            $request->validate([
                'image' => 'image|mimes:jpg,jpeg,png,webp,gif|max:' . config('upload.max_image_kb'),
            ]);
        }

        if ($request->filled('room_id') && $request->filled('ride_id')) {
            return apiResponse(null, 'Send either room_id or ride_id, not both.', 422, false);
        }

        if (! $request->filled('room_id') && ! $request->filled('ride_id')) {
            return apiResponse(null, 'room_id or ride_id is required.', 422, false);
        }

        /** @var ChatRoom $room */
        $room = $this->resolveChatRoomFromRequest($request);
        $this->assertMember($request, $room);

        if ($room->status !== 'active') {
            return apiResponse(null, 'Chat is not active for this ride.', 422, false);
        }

        $messageType = (string) ($request->input('message_type') ?? 'text');
        $text = trim((string) $request->input('message', ''));
        $imageUrl = $request->input('image_url') ?: $request->input('image');

        $uploadedFile = $request->file('image')
            ?? $request->file('photo')
            ?? $request->file('file');

        if ($uploadedFile instanceof UploadedFile) {
            $imageUrl = $this->storeChatImage($uploadedFile);
            $messageType = 'image';
        } elseif ($request->filled('image_base64')) {
            $imageUrl = $this->storeChatImageFromBase64((string) $request->input('image_base64'));
            $messageType = 'image';
        } elseif (is_string($imageUrl) && filter_var($imageUrl, FILTER_VALIDATE_URL)) {
            $messageType = 'image';
        } elseif ($messageType === 'image' && is_string($imageUrl) && $imageUrl !== '') {
            $messageType = 'image';
        } else {
            $imageUrl = null;
        }

        if ($messageType === 'text' && $text === '') {
            return apiResponse(null, 'Text message cannot be empty.', 422, false);
        }

        if ($messageType === 'image' && empty($imageUrl)) {
            return apiResponse(null, 'Image is required for image messages.', 422, false);
        }

        $message = ChatMessage::create([
            'chat_room_id' => $room->id,
            'sender_id' => $request->user()->id,
            'message_type' => $messageType,
            'message' => $messageType === 'text' ? $text : null,
            'image_url' => $messageType === 'image' ? $imageUrl : null,
            'meta' => $request->input('meta'),
        ])->load('sender:id,first_name,last_name,profile_picture');

        $this->notifyChatRecipient($room, (int) $request->user()->id, $message);

        SocketNotifier::broadcastNewChatMessage($message);

        return apiResponse($message, 'Message sent successfully.');
    }

    public function uploadImage(Request $request)
    {
        $file = $request->file('image')
            ?? $request->file('photo')
            ?? $request->file('file');

        if (! $file instanceof UploadedFile) {
            return apiResponse(null, 'Image file is required (use image, photo, or file field).', 422, false);
        }

        $request->validate([
            'image' => 'nullable|image|mimes:jpg,jpeg,png,webp,gif|max:' . config('upload.max_image_kb'),
            'photo' => 'nullable|image|mimes:jpg,jpeg,png,webp,gif|max:' . config('upload.max_image_kb'),
            'file' => 'nullable|image|mimes:jpg,jpeg,png,webp,gif|max:' . config('upload.max_image_kb'),
        ]);

        $path = $file->store('chat-images', 'public');
        $url = Storage::disk('public')->url($path);

        return apiResponse([
            'path' => $path,
            'url' => $url,
            'image_url' => $url,
        ], 'Image uploaded successfully.');
    }

    public function presence(Request $request, ChatRoom $room)
    {
        $this->assertMember($request, $room);

        return apiResponse(
            $this->presenceForRoom($room),
            'Presence fetched successfully.'
        );
    }

    /**
     * GET /api/chat/rides/{ride}/presence
     */
    public function presenceByRide(Request $request, Ride $ride)
    {
        $room = $this->chatRoomForRideOrAbort($request, $ride);

        return apiResponse(
            $this->presenceForRoom($room),
            'Presence fetched successfully.'
        );
    }

    protected function messageListForRoom(ChatRoom $room, Request $request)
    {
        $limit = min((int) $request->input('limit', 50), 200);

        return $room->messages()
            ->with('sender:id,first_name,last_name,profile_picture')
            ->latest('id')
            ->limit($limit)
            ->get()
            ->reverse()
            ->values();
    }

    protected function presenceForRoom(ChatRoom $room)
    {
        $users = [$room->passenger_id, $room->driver_id];

        return \App\Models\User::query()
            ->whereIn('id', $users)
            ->get(['id', 'first_name', 'last_name', 'is_online', 'last_seen_at']);
    }

    /**
     * Resolves ChatRoom from room_id or ride_id (ride must belong to current user as passenger or driver).
     */
    protected function resolveChatRoomFromRequest(Request $request): ChatRoom
    {
        if ($request->filled('room_id')) {
            return ChatRoom::findOrFail($request->integer('room_id'));
        }

        $ride = Ride::findOrFail($request->integer('ride_id'));
        $this->assertRideParticipant($request, $ride);

        $room = ChatRoom::where('ride_id', $ride->id)->first();
        if (! $room) {
            throw new HttpResponseException(response()->json([
                'status' => 404,
                'success' => false,
                'message' => 'No chat room for this ride yet (e.g. bid not accepted).',
                'data' => null,
            ], 404));
        }

        return $room;
    }

    protected function chatRoomForRideOrAbort(Request $request, Ride $ride): ChatRoom
    {
        $this->assertRideParticipant($request, $ride);

        $room = ChatRoom::where('ride_id', $ride->id)->first();
        if (! $room) {
            throw new HttpResponseException(response()->json([
                'status' => 404,
                'success' => false,
                'message' => 'No chat room for this ride yet (e.g. bid not accepted).',
                'data' => null,
            ], 404));
        }

        $this->assertMember($request, $room);

        return $room;
    }

    protected function assertRideParticipant(Request $request, Ride $ride): void
    {
        $userId = (int) $request->user()->id;
        if ((int) $ride->user_id !== $userId && (int) ($ride->driver_id ?? 0) !== $userId) {
            throw new HttpResponseException(response()->json([
                'status' => 403,
                'success' => false,
                'message' => 'You are not part of this ride.',
                'data' => null,
            ], 403));
        }
    }

    protected function assertMember(Request $request, ChatRoom $room): void
    {
        $userId = (int) $request->user()->id;
        if ($room->passenger_id !== $userId && $room->driver_id !== $userId) {
            throw new HttpResponseException(response()->json([
                'status' => 403,
                'success' => false,
                'message' => 'You are not allowed to access this chat room.',
                'data' => null,
            ], 403));
        }
    }

    /**
     * Push notification to the other person in the room (not the sender).
     */
    protected function notifyChatRecipient(ChatRoom $room, int $senderId, ChatMessage $message): void
    {
        $recipientId = null;
        if ((int) $room->passenger_id === $senderId) {
            if ($room->driver_id !== null) {
                $recipientId = (int) $room->driver_id;
            }
        } elseif ((int) $room->driver_id === $senderId) {
            $recipientId = (int) $room->passenger_id;
        }

        if ($recipientId === null) {
            return;
        }

        $recipient = User::query()->find($recipientId);
        if (! $recipient || empty($recipient->device_token)) {
            return;
        }

        $sender = $message->sender;
        $senderName = '';
        if ($sender) {
            $senderName = trim((string) $sender->first_name.' '.(string) $sender->last_name);
        }
        if ($senderName === '') {
            $senderName = 'Someone';
        }

        if ($message->message_type === 'image') {
            $body = $senderName.' sent a photo.';
        } else {
            $body = Str::limit((string) ($message->message ?? ''), 140);
        }

        if ($body === '') {
            $body = $senderName.' sent a message.';
        }
        send_user_push_notification($recipient, 'New message', $body);
    }

    protected function storeChatImage(UploadedFile $file): string
    {
        $path = $file->store('chat-images', 'public');

        return Storage::disk('public')->url($path);
    }

    protected function storeChatImageFromBase64(string $base64): string
    {
        $extension = 'jpg';

        if (preg_match('/^data:image\/(\w+);base64,/', $base64, $matches)) {
            $extension = strtolower($matches[1]);
            $base64 = substr($base64, strpos($base64, ',') + 1);
        }

        $binary = base64_decode($base64, true);
        if ($binary === false) {
            throw new HttpResponseException(response()->json([
                'status' => 422,
                'success' => false,
                'message' => 'Invalid image data.',
                'data' => null,
            ], 422));
        }

        $path = 'chat-images/' . Str::random(40) . '.' . $extension;
        Storage::disk('public')->put($path, $binary);

        return Storage::disk('public')->url($path);
    }
}
