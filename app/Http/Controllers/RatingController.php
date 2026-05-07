<?php

namespace App\Http\Controllers;

use App\Models\Rating;
use App\Models\Ride;
use Illuminate\Http\Request;

class RatingController extends Controller
{
    //
    public function rateDriver(Request $request, $rideId)
    {
        $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string|max:255',
        ]);
        $ride = Ride::findOrFail($rideId);
        if ($ride->user_id != auth()->id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }
        if ($ride->status != 'completed') {
            return response()->json(['message' => 'You can only rate after the ride is completed.'], 400);
        }
        $rating =  Rating::create([
            'ride_id' => $ride->id,
            'rated_by' => auth()->id(), // Customer ID
            'rated_to' => $ride->driver_id, // Driver ID
            'rating' => $request->rating,
            'comment' => $request->comment,
        ]);

        return apiResponse($rating, 'Driver rated successfully.');
    }

    public function rateCustomer(Request $request, $rideId)
    {
        $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string|max:255',
        ]);

        // Find the ride
        $ride = Ride::findOrFail($rideId);
        // Ensure the authenticated user is the driver for this ride
        if ($ride->driver_id != auth()->id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        // Check if the ride is completed
        if ($ride->status != 'completed') {
            return response()->json(['message' => 'You can only rate after the ride is completed.'], 400);
        }

        // Save the rating
     $rating =   Rating::create([
            'ride_id' => $ride->id,
            'rated_by' => auth()->id(), // Driver ID
            'rated_to' => $ride->user_id, // Customer ID
            'rating' => $request->rating,
            'comment' => $request->comment,
        ]);

        return apiResponse($rating, 'Customer rated successfully.');
    }
    
    public function getRateCustomer($rideId)
    {

        $driver_id = auth()->id();

        $ride = Ride::with('ratings')
            ->whereHas('ratings', function ($query) use ($driver_id) {
                $query->where('rated_by', $driver_id);
            })
            ->findOrFail($rideId);
            return apiResponse($ride, 'Get driver rated successfully.');
    }

    public function getrateDriver($rideId)
    {

        $user_id = auth()->id();

        $ride = Ride::with('ratings')
            ->whereHas('ratings', function ($query) use ($user_id) {
                $query->where('rated_to', $user_id);
            })
            ->findOrFail($rideId);
            return apiResponse($ride, 'Get Customer rated successfully.');
    }
}
