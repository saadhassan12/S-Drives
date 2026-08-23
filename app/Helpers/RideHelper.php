<?php

use App\Models\User;
use Illuminate\Support\Collection;

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

        return nearby_active_driver_query()
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
