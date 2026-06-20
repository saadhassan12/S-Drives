<?php

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
 * Notify all online drivers about a new ride and refresh their lists
 * 
 * @param array $driverIds Driver user IDs to notify
 * @param array $rideData Ride information
 * @return bool Success status
 */
if (!function_exists('notify_drivers_new_ride')) {
    function notify_drivers_new_ride(array $driverIds, array $rideData): bool
    {
        // Broadcast event AND auto-refresh all online drivers' nearby rides list
        return broadcast_socket_event('driver:new-ride-available', [
            'ride' => $rideData,
            'message' => 'New ride available nearby',
        ], $driverIds, true); // true = refresh_drivers
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
        // Broadcast to ALL online drivers with refresh flag
        return broadcast_socket_event('driver:rides-list-updated', [
            'action' => $action,
            'ride' => $rideData,
            'message' => "Rides list updated: {$action}",
        ], null, true); // null = all users, true = refresh_drivers
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
if (!function_exists('notify_bid_update')) {
    function notify_bid_update(int $userId, array $bidData): bool
    {
        return broadcast_socket_event('ride:bid-updated', [
            'bid' => $bidData,
        ], $userId);
    }
}
