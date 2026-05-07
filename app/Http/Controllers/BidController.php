<?php

namespace App\Http\Controllers;

use App\Models\Bid;
use App\Models\Ride;
use App\Models\User;
use Illuminate\Http\Request;

class BidController extends Controller
{
    //

  public function placeBid(Request $request, $rideId)
{
    $request->validate([
        'amount' => 'required|numeric|min:0',
        'time' => 'required|string',
    ]);

    // ✅ Ride fetch karo
    $ride = Ride::findOrFail($rideId);

    // ✅ Ride ka user (passenger)
    $passenger = User::find($ride->user_id);
    
    $existingBid = Bid::where('ride_id', $rideId)
        ->where('driver_id', auth()->id())
        ->first();

    if ($existingBid) {
        $existingBid->update([
            'amount' => $request->amount,
            'time' => $request->time,
            'status' => 'pending',
            'user_id'   => $ride->user_id, 
        ]);

        if ($passenger && $passenger->device_token) {
             
            send_firebase_notification(
              
                'New Bid Received',
                "A driver has updated the bid to {$request->amount} on your ride.",
                  $passenger->device_token,
            );
        }

        return apiResponse($existingBid, 'Bid updated successfully!');
    }

    $bid = Bid::create([
        'ride_id' => $rideId,
        'driver_id' => auth()->id(),
        'amount' => $request->amount,
        'time' => $request->time,
        'user_id'   => $ride->user_id, 
        'status' => 'pending',
        'created_at' => now(),
    ]);

    // ✅ Notify passenger
    if ($passenger && $passenger->device_token) {
       
         send_firebase_notification(
            
            'New Bid Received',
            "A driver has placed a bid of {$request->amount} on your ride.",
            $passenger->device_token,
        );
    }

    return apiResponse($bid, 'Bid placed successfully!');
}


public function acceptBid(Request $request, $rideId, $bid_id)
{
    $ride = Ride::findOrFail($rideId);
    $bid = Bid::where('ride_id', $rideId)
        ->where('id', $bid_id)
        ->first();

    if (!$bid) {
        return apiResponse(null, 'Bid not found.', 404);
    }

    // Update the accepted bid
    $bid->update([
        'user_id' => auth()->id(),
        'status' => 'accepted',
    ]);

    // Update ride with bid info
    $ride->driver_id = $bid->driver_id;
    $ride->bid_id = $bid_id;
    $ride->final_fare = $bid->amount;
    $ride->status = 'accepted';
    $ride->save();

    // Delete other bids
    Bid::where('ride_id', $rideId)
        ->where('id', '!=', $bid_id)
        ->delete();

    // Send Notification to Driver
    $driver = \App\Models\User::find($bid->driver_id);
    $firebaseResponse = null;
    if (!empty($driver) && !empty($driver->device_token)) {
        try {
            $firebaseResponse = send_firebase_notification(
                'Bid Accepted',
                'A Passenger accepted your offer. Get ready for pickup.',
                $driver->device_token
            );
        } catch (\Exception $e) {
            \Log::error('Notification Error: ' . $e->getMessage());
        }
    }

    return apiResponse($bid, 'Bid accepted and other bids removed successfully!', 200);
}

public function rejectBid(Request $request, $rideId, $bid_id)
{
    $ride = Ride::findOrFail($rideId);

    $bid = Bid::where('ride_id', $rideId)
        ->where('id', $bid_id)
        ->first();

    if ($bid) {

        $driver = User::find($bid->driver_id);

        $bid->delete();

        if ($driver && $driver->device_token) {
            $title = "Bid Rejected";
            $body  = "Your bid for ride was rejected by the passenger.";

            send_firebase_notification($title, $body,$driver->device_token);
        }

        return apiResponse(null, 'Bid rejected and driver notified successfully!');
    }

    return apiResponse(null, 'No bid found to reject.', 404);
}

          public function getBid(Request $request, $rideId)
         {
            $ride = Ride::where('id', $rideId)
                ->where('user_id', auth()->id())
                ->firstOrFail();
            $bids = Bid::where('ride_id', $rideId)->where('status', 'pending')->get();
            return apiResponse($bids, 'All bids for the ride retrieved successfully.');
         }
}
