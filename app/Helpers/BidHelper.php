<?php

use App\Models\Bid;
use App\Models\Rating;
use Illuminate\Support\Collection;

if (!function_exists('driver_average_rating')) {
    function driver_average_rating(int $driverId): array
    {
        $query = Rating::query()->where('rated_to', $driverId);
        $count = (int) $query->count();
        $average = $count > 0 ? round((float) $query->avg('rating'), 2) : 0;

        return [
            'average_rating' => $average,
            'rating_count' => $count,
        ];
    }
}

if (!function_exists('format_bid_for_passenger')) {
    function format_bid_for_passenger(Bid $bid): array
    {
        $bid->loadMissing(['driver', 'vehicles']);

        $ratings = driver_average_rating((int) $bid->driver_id);

        return array_merge($bid->toArray(), $ratings);
    }
}

if (!function_exists('format_bids_for_passenger')) {
    function format_bids_for_passenger(Collection $bids): array
    {
        return $bids->map(fn (Bid $bid) => format_bid_for_passenger($bid))->values()->all();
    }
}

if (!function_exists('bid_socket_payload_for_passenger')) {
    function bid_socket_payload_for_passenger(Bid $bid, string $status): array
    {
        $ratings = driver_average_rating((int) $bid->driver_id);

        return array_merge([
            'bid_id' => $bid->id,
            'ride_id' => $bid->ride_id,
            'driver_id' => $bid->driver_id,
            'amount' => $bid->amount,
            'time' => $bid->time,
            'status' => $status,
            'visibility_seconds' => ride_visibility_seconds(),
            'visibility_reset' => true,
        ], $ratings);
    }
}
