<?php

use App\Models\Ride;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

if (!function_exists('driver_ride_radius_km')) {
    function driver_ride_radius_km(): float
    {
        return max(1, (float) config('ride.driver_radius_km', 10));
    }
}

if (!function_exists('calculate_geo_distance_km')) {
    function calculate_geo_distance_km(float $startLat, float $startLng, float $endLat, float $endLng): float
    {
        $earthRadius = 6371;
        $latDistance = deg2rad($endLat - $startLat);
        $lonDistance = deg2rad($endLng - $startLng);
        $a = sin($latDistance / 2) * sin($latDistance / 2)
            + cos(deg2rad($startLat)) * cos(deg2rad($endLat))
            * sin($lonDistance / 2) * sin($lonDistance / 2);
        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return $earthRadius * $c;
    }
}

if (!function_exists('get_geo_bounds')) {
    function get_geo_bounds(float $latitude, float $longitude, float $distanceKm): array
    {
        $latDelta = $distanceKm / 111.0;
        $lngDelta = $distanceKm / max(0.00001, 111.320 * cos(deg2rad($latitude)));

        return [
            $latitude - $latDelta,
            $latitude + $latDelta,
            $longitude - $lngDelta,
            $longitude + $lngDelta,
        ];
    }
}

if (!function_exists('compatible_vehicle_category_ids')) {
    function compatible_vehicle_category_ids(int $vehicleCategoryId): array
    {
        $compatibleCategories = [
            1 => [1, 2],
            2 => [1, 2],
            4 => [4, 5],
            5 => [5],
        ];

        return $compatibleCategories[$vehicleCategoryId] ?? [$vehicleCategoryId];
    }
}

if (!function_exists('remember_ride_notified_drivers')) {
    function remember_ride_notified_drivers(int $rideId, array $driverIds): void
    {
        if (empty($driverIds)) {
            return;
        }

        $key = "ride_{$rideId}_notified_drivers";
        $existing = Cache::get($key, []);
        Cache::put(
            $key,
            array_values(array_unique(array_merge($existing, array_map('intval', $driverIds)))),
            now()->addHours(2)
        );
    }
}

if (!function_exists('get_ride_previously_notified_driver_ids')) {
    function get_ride_previously_notified_driver_ids(int $rideId): array
    {
        return array_map('intval', Cache::get("ride_{$rideId}_notified_drivers", []));
    }
}

if (!function_exists('find_drivers_for_fare_update')) {
    /**
     * Nearby drivers plus any previously notified driver who is still eligible.
     */
    function find_drivers_for_fare_update(Ride $ride, ?float $radiusKm = null): Collection
    {
        $radiusKm = $radiusKm ?? driver_ride_radius_km();
        $categoryIds = compatible_vehicle_category_ids((int) $ride->vehicle_category_id);

        $nearbyDrivers = find_nearby_drivers_for_ride(
            (float) $ride->start_latitude,
            (float) $ride->start_longitude,
            $categoryIds,
            $radiusKm
        );

        $previousDriverIds = get_ride_previously_notified_driver_ids((int) $ride->id);
        if (empty($previousDriverIds)) {
            return $nearbyDrivers;
        }

        $knownIds = $nearbyDrivers->pluck('id')->map(fn ($id) => (int) $id)->all();
        $missingIds = array_diff($previousDriverIds, $knownIds);

        if (empty($missingIds)) {
            return $nearbyDrivers;
        }

        $extraDrivers = active_driver_mode_query()
            ->select('id', 'latitude', 'longitude', 'device_token', 'role', 'last_login_at', 'is_online', 'is_app_foreground')
            ->whereIn('id', $missingIds)
            ->whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->get()
            ->filter(function (User $driver) use ($ride, $radiusKm, $categoryIds) {
                if (! $driver->vehicles()->whereIn('vehicle_category_id', $categoryIds)->exists()) {
                    return false;
                }

                return calculate_geo_distance_km(
                    (float) $ride->start_latitude,
                    (float) $ride->start_longitude,
                    (float) $driver->latitude,
                    (float) $driver->longitude
                ) <= $radiusKm;
            });

        return $nearbyDrivers->merge($extraDrivers)->unique('id')->values();
    }
}

if (!function_exists('notify_drivers_fare_updated')) {
    /**
     * Re-show a ride to eligible drivers after the passenger increases fare.
     */
    function notify_drivers_fare_updated(Ride $ride, $fareAmount): void
    {
        $fareAmount = is_numeric($fareAmount) ? round((float) $fareAmount) : $fareAmount;

        $ride->final_fare = $fareAmount;
        $ride->estimated_fare = $fareAmount;
        $ride->touch();
        $ride->save();

        $seconds = ride_visibility_seconds();
        $radiusKm = driver_ride_radius_km();
        $drivers = find_drivers_for_fare_update($ride, $radiusKm);
        $allDriverIds = $drivers->pluck('id')->map(fn ($id) => (int) $id)->all();

        remember_ride_notified_drivers((int) $ride->id, $allDriverIds);

        if (!empty($allDriverIds)) {
            mark_ride_visible_for_drivers($allDriverIds, (int) $ride->id, $seconds);
        }

        foreach ($drivers as $driver) {
            send_driver_ride_notification(
                $driver,
                'Fare Updated',
                'Ride fare has been updated to ' . $fareAmount
            );
        }

        $ridePayload = [
            'ride_id' => $ride->id,
            'final_fare' => $ride->final_fare,
            'estimated_fare' => $ride->estimated_fare,
            'fare_updated' => true,
            'start' => $ride->start,
            'destination' => $ride->destination,
            'vehicle_category_id' => $ride->vehicle_category_id,
            'status' => $ride->status,
            'max_radius_km' => $radiusKm,
        ];

        $socketDriverIds = get_online_driver_ids_for_socket($allDriverIds);
        if (!empty($socketDriverIds)) {
            broadcast_socket_event('driver:new-ride-available', [
                'ride' => $ridePayload,
                'message' => 'Updated fare ride available nearby',
                'ride_id' => (int) $ride->id,
                'visibility_seconds' => $seconds,
                'visibility_reset' => true,
                'reason' => 'fare_updated',
            ], $socketDriverIds, false);
        }

        $rideDetails = Ride::with(['user', 'vehicleCategory'])
            ->find($ride->id)
            ?->toArray();

        refresh_all_drivers_list('fare_updated', [
            'ride_id' => $ride->id,
            'visibility_seconds' => $seconds,
            'visibility_reset' => true,
            'final_fare' => $ride->final_fare,
            'estimated_fare' => $ride->estimated_fare,
            'fare_updated' => true,
            'eligible_driver_ids' => $allDriverIds,
            'ride_details' => $rideDetails,
        ]);
    }
}

if (!function_exists('find_nearby_drivers_for_ride')) {
    /**
     * Active drivers within configured radius who can receive ride notifications.
     *
     * @param  list<int>  $vehicleCategoryIds
     */
    function find_nearby_drivers_for_ride(
        float $rideLat,
        float $rideLng,
        array $vehicleCategoryIds,
        ?float $radiusKm = null
    ): Collection {
        $radiusKm = $radiusKm ?? driver_ride_radius_km();
        [$minLat, $maxLat, $minLng, $maxLng] = get_geo_bounds($rideLat, $rideLng, $radiusKm);

        return active_driver_mode_query()
            ->select('id', 'latitude', 'longitude', 'device_token', 'role', 'last_login_at', 'is_online', 'is_app_foreground')
            ->whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->whereBetween('latitude', [$minLat, $maxLat])
            ->whereBetween('longitude', [$minLng, $maxLng])
            ->whereHas('vehicles', function ($query) use ($vehicleCategoryIds) {
                $query->whereIn('vehicle_category_id', $vehicleCategoryIds);
            })
            ->get()
            ->filter(function (User $driver) use ($rideLat, $rideLng, $radiusKm) {
                return calculate_geo_distance_km(
                    $rideLat,
                    $rideLng,
                    (float) $driver->latitude,
                    (float) $driver->longitude
                ) <= $radiusKm;
            })
            ->values();
    }
}
