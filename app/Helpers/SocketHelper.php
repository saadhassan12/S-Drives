<?php

use App\Models\Ride;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Broadcast socket event to connected drivers in real-time
 * 
 * @param string $event Event name (e.g., 'driver:new-ride-available')
 * @param array $data Data to send with the event
 * @param array|int|null $userIds Specific user IDs to target (optional, null = broadcast to all)
 * @param bool $refreshDrivers Whether to auto-refresh all online drivers' nearby rides list
 * @return bool Success status
 */
if (!function_exists('broadcast_socket_event')) {
    function broadcast_socket_event(string $event, array $data, $userIds = null, bool $refreshDrivers = false): bool
    {
        try {
            $socketUrl = env('SOCKET_SERVER_URL', 'http://127.0.0.1:6001');
            $secret = env('SOCKET_INTERNAL_SECRET', '');

            if (empty($secret)) {
                // Silent fail - logging disabled to prevent recursion
                return false;
            }

            $payload = [
                'event' => $event,
                'data' => $data,
                'refresh_drivers' => $refreshDrivers,
            ];

            if ($userIds !== null) {
                $payload['user_ids'] = is_array($userIds) ? $userIds : [$userIds];
            }

            $response = Http::timeout(5)
                ->withHeaders([
                    'X-Socket-Secret' => $secret,
                    'Accept' => 'application/json',
                ])
                ->post("{$socketUrl}/internal/broadcast", $payload);

            // SUCCESS - Don't log to prevent recursive logging crash
            return $response->successful();

        } catch (\Exception $e) {
            // FAIL - Silent fail to prevent recursive logging crash
            // If you need debugging, use error_log() instead:
            // error_log("[socket-helper] Broadcast error: {$e->getMessage()}");
            return false;
        }
    }
}

/**
 * Notify specific user(s) via socket event
 * 
 * @param int|array $userIds User ID(s) to notify
 * @param string $event Event name
 * @param array $data Event data
 * @return bool Success status
 */
if (!function_exists('notify_users_socket')) {
    function notify_users_socket($userIds, string $event, array $data): bool
    {
        return broadcast_socket_event($event, $data, $userIds);
    }
}

/**
 * Mark a ride as visible to specific drivers for a short window (default 60 seconds).
 * After the window expires, near_ride() stops returning it until re-marked (new ride / fare update).
 */
if (!function_exists('mark_ride_visible_for_drivers')) {
    function mark_ride_visible_for_drivers(array $driverIds, int $rideId, int $seconds = 60): void
    {
        foreach ($driverIds as $driverId) {
            Cache::put(
                "driver_{$driverId}_ride_{$rideId}_visible",
                true,
                now()->addSeconds($seconds)
            );
        }
    }
}

if (!function_exists('is_ride_visible_for_driver')) {
    function is_ride_visible_for_driver(int $driverId, int $rideId): bool
    {
        return Cache::has("driver_{$driverId}_ride_{$rideId}_visible");
    }
}

/**
 * Notify all online drivers about a new ride and refresh their lists
 * 
 * @param array $driverIds Driver user IDs to notify
 * @param array $rideData Ride information
 * @return bool Success status
 */
if (!function_exists('notify_drivers_new_ride')) {
    function notify_drivers_new_ride(array $driverIds, array $rideData, bool $resetVisibility = false): bool
    {
        $driverIds = array_values(array_unique(array_map('intval', $driverIds)));

        if (empty($driverIds)) {
            return false;
        }

        if (!empty($rideData['ride_id'])) {
            remember_ride_notified_drivers((int) $rideData['ride_id'], $driverIds);
            mark_ride_visible_for_drivers($driverIds, (int) $rideData['ride_id'], ride_visibility_seconds());
        }

        $socketDriverIds = get_online_driver_ids_for_socket($driverIds);

        if (empty($socketDriverIds)) {
            return false;
        }

        $eventData = [
            'ride' => $rideData,
            'message' => 'New ride available nearby',
        ];

        if ($resetVisibility && !empty($rideData['ride_id'])) {
            $eventData['ride_id'] = (int) $rideData['ride_id'];
            $eventData['visibility_seconds'] = ride_visibility_seconds();
            $eventData['visibility_reset'] = true;
            $eventData['reason'] = !empty($rideData['fare_updated']) ? 'fare_updated' : 'ride_updated';
        }

        return broadcast_socket_event('driver:new-ride-available', $eventData, $socketDriverIds, true);
    }
}

/**
 * Refresh all online drivers' nearby rides list (for ride updates, cancellations, etc.)
 * 
 * @param string $action Action description (e.g., 'ride_canceled', 'ride_updated', 'fare_increased')
 * @param array $rideData Ride information
 * @return bool Success status
 */
if (!function_exists('refresh_all_drivers_list')) {
    function refresh_all_drivers_list(string $action, array $rideData = []): bool
    {
        $payload = [
            'action' => $action,
            'ride' => $rideData,
            'message' => "Rides list updated: {$action}",
        ];

        if (!empty($rideData['visibility_reset'])) {
            $payload['ride_id'] = $rideData['ride_id'] ?? null;
            $payload['visibility_seconds'] = $rideData['visibility_seconds'] ?? ride_visibility_seconds();
            $payload['visibility_reset'] = true;
            $payload['reason'] = !empty($rideData['fare_updated'])
                ? 'fare_updated'
                : ($action === 'bid_placed' ? 'bid_placed' : 'ride_updated');
        }

        return broadcast_socket_event('driver:rides-list-updated', $payload, null, true);
    }
}

/**
 * Notify passenger about ride status update
 * 
 * @param int $passengerId Passenger user ID
 * @param array $rideData Ride information
 * @param string $status New status
 * @return bool Success status
 */
if (!function_exists('notify_passenger_ride_update')) {
    function notify_passenger_ride_update(int $passengerId, array $rideData, string $status): bool
    {
        return broadcast_socket_event('passenger:ride-updated', [
            'ride' => $rideData,
            'status' => $status,
            'message' => "Ride status updated to: {$status}",
        ], $passengerId);
    }
}

/**
 * Notify about bid updates
 * 
 * @param int $userId User ID to notify (driver or passenger)
 * @param array $bidData Bid information
 * @return bool Success status
 */
if (!function_exists('ride_visibility_seconds')) {
    function ride_visibility_seconds(): int
    {
        return max(10, (int) config('ride.visibility_seconds', 60));
    }
}

/**
 * Restart the ride visibility window when a driver sends/updates a bid.
 */
if (!function_exists('reset_ride_visibility_after_bid')) {
    function reset_ride_visibility_after_bid(Ride $ride): void
    {
        $seconds = ride_visibility_seconds();

        $ride->touch();

        if ($ride->vehicle_category_id) {
            $drivers = find_nearby_drivers_for_ride(
                (float) $ride->start_latitude,
                (float) $ride->start_longitude,
                [(int) $ride->vehicle_category_id]
            );

            $driverIds = $drivers->pluck('id')->map(fn ($id) => (int) $id)->all();

            if (!empty($driverIds)) {
                mark_ride_visible_for_drivers($driverIds, (int) $ride->id, $seconds);
            }
        }

        refresh_all_drivers_list('bid_placed', [
            'ride_id' => $ride->id,
            'visibility_seconds' => $seconds,
            'visibility_reset' => true,
        ]);
    }
}

if (!function_exists('notify_bid_update')) {
    function notify_bid_update(int $userId, array $bidData): bool
    {
        return broadcast_socket_event('ride:bid-updated', [
            'bid' => $bidData,
        ], $userId);
    }
}
