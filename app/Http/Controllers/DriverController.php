<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use App\Models\Cnic;
use App\Models\DriverLicenses;
use App\Models\Vehicles;
use App\Models\User;
use App\Models\Rating;
use App\Models\Ride;

use Carbon\Carbon;

class DriverController extends Controller
{
    //
    public function drivercnic(request $request)
    {
        $request->validate([
            'cnic_no' => 'required|string|max:255',
            'exp_date' => 'required|string|max:255',
            'front_pic' => 'nullable|image|mimes:jpg,jpeg,png,gif|max:10240',
            'back_pic' => 'nullable|image|mimes:jpg,jpeg,png,gif|max:10240',
            'selfie_with_id' => 'nullable|image|mimes:jpg,jpeg,png,gif|max:10240',
        ]);
        if ($request->hasFile('front_pic')) {
            $file = $request->file('front_pic');
            $file_name = time() . '-' . $file->getClientOriginalName();
            $file->move(public_path('cnic_front'), $file_name);
            $cnic_front = asset('cnic_front/' . $file_name);
        }else{
            $cnic_front = null;
        }
        if ($request->hasFile('back_pic')) {
            $file = $request->file('back_pic');
            $file_name = time() . '-' . $file->getClientOriginalName();
            $file->move(public_path('back_pic'), $file_name);
            $back_pic = asset('back_pic/' . $file_name);
        }else{
            $back_pic = null;
        }
        if ($request->hasFile('selfie_with_id')) {
            $file = $request->file('selfie_with_id');
            $file_name = time() . '-' . $file->getClientOriginalName();
            $file->move(public_path('selfie'), $file_name);
            $selfie = asset('selfie/' . $file_name);
        }else{
            $selfie = null;
        }
        $cinc = Cnic::updateOrCreate([
            'user_id' => auth()->id(),
            'cnic_no' => $request->input('cnic_no'),
            'exp_date' => $request->input('exp_date'),
            'front_pic' => $cnic_front,
            'back_pic' => $back_pic,
            'selfie_with_id' => $selfie,
        ]);
        $cnicData = Cnic::with('user')->find($cinc->id);
        return apiResponse(
            $cnicData,
        );
    }

    public function driverlicenses(Request $request)
    {
        $request->validate([
            'license_no' => 'required|string|max:255',
            'expiration_date' => 'required|string|max:255',
            'licenses_front_pic' => 'nullable|image|mimes:jpg,jpeg,png,gif|max:10240',
            'licenses_back_pic' => 'nullable|image|mimes:jpg,jpeg,png,gif|max:10240',
            'selfie_with_licenses' => 'nullable|image|mimes:jpg,jpeg,png,gif|max:10240',
        ]);
        if ($request->hasFile('licenses_front_pic')) {
            $file = $request->file('licenses_front_pic');
            $file_name = time() . '-' . $file->getClientOriginalName();
            $file->move(public_path('licenses_front_pic'), $file_name);
            $licenses_front_pic = asset('licenses_front_pic/' . $file_name);
        }else{
            $licenses_front_pic = null;
        }
        if ($request->hasFile('licenses_back_pic')) {
            $file = $request->file('licenses_back_pic');
            $file_name = time() . '-' . $file->getClientOriginalName();
            $file->move(public_path('licenses_back_pic'), $file_name);
            $licenses_back_pic = asset('licenses_back_pic/' . $file_name);
        }else{
            $licenses_back_pic = null;
        }
        if ($request->hasFile('selfie_with_licenses')) {
            $file = $request->file('selfie_with_licenses');
            $file_name = time() . '-' . $file->getClientOriginalName();
            $file->move(public_path('selfie_with_licenses'), $file_name);
            $selfie = asset('selfie_with_licenses/' . $file_name);
        }else{
            $selfie = null;
        }
        $DriverLicenses = DriverLicenses::updateOrCreate([
            'user_id' => auth()->id(),
            'license_no' => $request->input('license_no'),
            'expiration_date' => $request->input('expiration_date'),
            'licenses_front_pic' => $licenses_front_pic,
            'licenses_back_pic' => $licenses_back_pic,
            'selfie_with_licenses' => $selfie,
        ]);
        $DriverLicensesdata = DriverLicenses::with('user')->find($DriverLicenses->id);
        return apiResponse(
            $DriverLicensesdata,
        );
    }

    public function vehicles(Request $request)
    {
        $request->validate([
            'vehicle_category_id' => 'required|string|max:255',
            'engine' => 'nullable|string|max:255',
            'manufacture_year' => 'nullable|string|max:255',
            'manufacture_model' => 'nullable|string|max:255',
            'manufacture_company' => 'nullable|string|max:255',
            'registration_number' => 'nullable|string|max:255',
            'vehicle_front_pic' => 'nullable|image|mimes:jpg,jpeg,png,gif|max:10240',
            'vehicle_back_pic' => 'nullable|image|mimes:jpg,jpeg,png,gif|max:10240',
            'vehicle_dashboard' => 'nullable|image|mimes:jpg,jpeg,png,gif|max:10240',
            'vehicle_certificate_front' => 'nullable|image|mimes:jpg,jpeg,png,gif|max:10240',
            'vehicle_certificate_back' => 'nullable|image|mimes:jpg,jpeg,png,gif|max:10240',
            'interior' => 'nullable|image|mimes:jpg,jpeg,png,gif|max:10240',
        ]);
        if ($request->hasFile('vehicle_front_pic')) {
            $file = $request->file('vehicle_front_pic');
            $file_name = time() . '-' . $file->getClientOriginalName();
            $file->move(public_path('vehicle_front_pic'), $file_name);
            $vehicle_front_pic = asset('vehicle_front_pic/' . $file_name);
        }
        else{
            $vehicle_front_pic = null;
        }
        if ($request->hasFile('vehicle_back_pic')) {
            $file = $request->file('vehicle_back_pic');
            $file_name = time() . '-' . $file->getClientOriginalName();
            $file->move(public_path('vehicle_back_pic'), $file_name);
            $vehicle_back_pic = asset('vehicle_back_pic/' . $file_name);
        }else{
            $vehicle_back_pic = null;
        }
        if ($request->hasFile('vehicle_dashboard')) {
            $file = $request->file('vehicle_dashboard');
            $file_name = time() . '-' . $file->getClientOriginalName();
            $file->move(public_path('vehicle_dashboard'), $file_name);
            $vehicle_dashboard = asset('vehicle_dashboard/' . $file_name);
        }else{
            $vehicle_dashboard = null;
        }
        if ($request->hasFile('vehicle_certificate_front')) {
            $file = $request->file('vehicle_certificate_front');
            $file_name = time() . '-' . $file->getClientOriginalName();
            $file->move(public_path('vehicle_certificate_front'), $file_name);
            $vehicle_certificate_front = asset('vehicle_certificate_front/' . $file_name);
        }else{
            $vehicle_certificate_front = null;
        }
        if ($request->hasFile('vehicle_certificate_back')) {
            $file = $request->file('vehicle_certificate_back');
            $file_name = time() . '-' . $file->getClientOriginalName();
            $file->move(public_path('vehicle_certificate_back'), $file_name);
            $vehicle_certificate_back = asset('vehicle_certificate_back/' . $file_name);
        }else{
            $vehicle_certificate_back = null;
        }
        if ($request->hasFile('interior')) {
            $file = $request->file('interior');
            $file_name = time() . '-' . $file->getClientOriginalName();
            $file->move(public_path('interior'), $file_name);
            $interior = asset('interior/' . $file_name);
        }else{
            $interior = null;
        }
        $Vehicles = Vehicles::updateOrCreate([
            'user_id' => auth()->id(),
            'vehicle_category_id' => $request->input('vehicle_category_id'),
            'engine' => $request->input('engine'),
            'manufacture_year' => $request->input('manufacture_year'),
            'manufacture_model' => $request->input('manufacture_model'),
            'manufacture_company' => $request->input('manufacture_company'),
            'courier_servies' => $request->input('courier_servies'),
            'registration_number' => $request->input('registration_number'),
            'vehicle_front_pic' => $vehicle_front_pic,
            'vehicle_back_pic' => $vehicle_back_pic,
            'vehicle_dashboard' => $vehicle_dashboard,
            'vehicle_certificate_front' => $vehicle_certificate_front,
            'vehicle_certificate_back' => $vehicle_certificate_back,
            'interior' => $interior,
        ]);
        $Vehiclesdata = Vehicles::with('user','vehicle')->find($Vehicles->id);
        return apiResponse(
            $Vehiclesdata,
        );
    }
// public function near_ride()
// {
//     $user = auth()->user();

//     if ($user->role !== 'driver') {
//         return response()->json(['error' => 'Unauthorized'], 403);
//     }

//     $vehicle = Vehicles::where('user_id', $user->id)->first();
//     if (!$vehicle) {
//         return response()->json(['error' => 'Vehicle not found for this driver'], 404);
//     }

//     $driverVehicleCategoryId = $vehicle->vehicle_category_id;
//     $today = Carbon::now();
//     $driverLatitude = $user->latitude;
//     $driverLongitude = $user->longitude;
        
//     $ridesToUpdate = Ride::where('vehicle_category_id', $driverVehicleCategoryId)
//         ->whereRaw("
//             (6371 * acos(
//                 cos(radians(?)) *
//                 cos(radians(start_latitude)) *
//                 cos(radians(start_longitude) - radians(?)) +
//                 sin(radians(?)) *
//                 sin(radians(start_latitude))
//             )) <= 10", [$driverLatitude, $driverLongitude, $driverLatitude])
//         ->where('status', 'in_progress')
//         ->where('time_out', 0)
//         ->whereDate('created_at', $today)
//         ->with(['user'])
//         ->get();

//     if ($ridesToUpdate->isNotEmpty()) {
//         $tenMinutesAgo = Carbon::now()->subMinutes(10);
//         foreach ($ridesToUpdate as $ride) {
//             // ✅ Timeout check
//             if ($ride->created_at <= $tenMinutesAgo) {
//                 $ride->time_out = 1;
//                 $ride->status = 'canceled';
//                 $ride->save();
//                 continue;
//             }
//             $nearbyDrivers = User::where('role', 'driver')
//                 ->whereHas('vehicles', function ($q) use ($driverVehicleCategoryId) {
//                     $q->where('vehicle_category_id', $driverVehicleCategoryId);
//                 })
//                 ->whereRaw("
//                     (6371 * acos(
//                         cos(radians(?)) *
//                         cos(radians(latitude)) *
//                         cos(radians(longitude) - radians(?)) +
//                         sin(radians(?)) *
//                         sin(radians(latitude))
//                     )) <= 10", [$ride->start_latitude, $ride->start_longitude, $ride->start_latitude])
//                 ->get();

//                 foreach ($nearbyDrivers as $driver) {
//                 if (!empty($driver->device_token)) {
//                     send_firebase_notification(
//                         'Nearby Ride Found',
//                         'A passenger nearby is requesting a ride. Accept now before it’s gone.',
//                         $driver->device_token,
//                         ['ride_id' => $ride->id]
//                     );
//                 }
//             }
//         }

//         return apiResponse($ridesToUpdate, 'Nearby rides fetched and drivers notified.');
//     }

//     return apiResponse([], 'Ride not Found', 200, false);
// }
// public function near_ride()
// {
//     $user = auth()->user();
//     if ($user->role !== 'driver') {
//         return response()->json(['error' => 'Unauthorized'], 403);
//     }

//     $vehicle = Vehicles::where('user_id', $user->id)->first();
//     if (!$vehicle) {
//         return response()->json(['error' => 'Vehicle not found for this driver'], 404);
//     }

//     $driverVehicleCategoryId = $vehicle->vehicle_category_id;

//     $compatibleCategories = [
//         1 => [1, 2],
//         2 => [1, 2],
//         4 => [4, 5],    
//         5 => [5],
//     ];

//     $allowedCategories = $compatibleCategories[$driverVehicleCategoryId] ?? [$driverVehicleCategoryId];

//     $today = Carbon::now();
//     $driverLatitude = $user->latitude;
//     $driverLongitude = $user->longitude;
        
//     $ridesToUpdate = Ride::whereIn('vehicle_category_id', $allowedCategories)
//         ->whereRaw("
//             (6371 * acos(
//                 cos(radians(?)) *
//                 cos(radians(start_latitude)) *
//                 cos(radians(start_longitude) - radians(?)) +
//                 sin(radians(?)) *
//                 sin(radians(start_latitude))
//             )) <= 10", [$driverLatitude, $driverLongitude, $driverLatitude])
//         ->where('status', 'in_progress')
//         ->where('time_out', 0)
//         ->whereDate('created_at', $today)
//         ->with(['user','vehicleCategory'])
//         ->get();

// if ($ridesToUpdate->isNotEmpty()) {
//     $tenMinutesAgo = Carbon::now()->subMinutes(10);
//     $firebaseResponse = null;

//     foreach ($ridesToUpdate as $ride) {
//         if ($ride->created_at <= $tenMinutesAgo) {
//             $ride->time_out = 1;
//             $ride->status = 'canceled';
//             $ride->save();

//             if (!empty($user->device_token)) {
//                 $firebaseResponse = send_firebase_notification(
//                     'Ride Canceled',
//                     'Your nearby ride was canceled due to timeout.',
//                     $user->device_token,
//                     ['ride_id' => $ride->id, 'status' => 'canceled']
//                 );
//             }
//             continue;
//         }

//         $cacheKey = 'driver_'.$user->id.'_last_ride';
//         $lastRideId = Cache::get($cacheKey);

//         if ($lastRideId && $lastRideId == $ride->id) {
//             continue;
//         }
//         if (!empty($user->device_token)) {
//             // $firebaseResponse = send_firebase_notification(
//             //     'Nearby Ride Found',
//             //     'A passenger nearby is requesting a ride. Accept now before it’s gone.',
//             //     $user->device_token,
               
//             // );
//             Cache::put($cacheKey, $ride->id, now()->addMinutes(15));
//         }

//         break;
//     }

//     return apiResponse($ridesToUpdate, 'Nearby rides fetched and driver notified.', 200,);
// }

// return apiResponse([], 'Ride not Found', 200, false);



//     return apiResponse([], 'Ride not Found', 200, false);
// }

public function near_ride()
{
    $user = auth()->user();
    if ($user->role !== 'driver') {
        return response()->json(['error' => 'Unauthorized'], 403);
    }

    $vehicle = Vehicles::where('user_id', $user->id)->first();
    if (!$vehicle) {
        return response()->json(['error' => 'Vehicle not found for this driver'], 404);
    }

    $driverVehicleCategoryId = $vehicle->vehicle_category_id;

    $compatibleCategories = [
        1 => [1, 2],
        2 => [1, 2],
        4 => [4, 5],
        5 => [5],
    ];

    $allowedCategories = $compatibleCategories[$driverVehicleCategoryId] ?? [$driverVehicleCategoryId];

    $today = Carbon::now();
    $driverLatitude = $user->latitude;
    $driverLongitude = $user->longitude;

    $ridesToUpdate = Ride::whereIn('vehicle_category_id', $allowedCategories)
        ->whereRaw("
            (6371 * acos(
                cos(radians(?)) *
                cos(radians(start_latitude)) *
                cos(radians(start_longitude) - radians(?)) +
                sin(radians(?)) *
                sin(radians(start_latitude))
            )) <= 10", [$driverLatitude, $driverLongitude, $driverLatitude])
        ->where('status', 'in_progress')
        ->where('time_out', 0)
        ->whereDate('created_at', $today)
        ->with(['user', 'vehicleCategory'])
        ->get();

    if ($ridesToUpdate->isNotEmpty()) {

        foreach ($ridesToUpdate as $ride) {

            // ???? TIMEOUT SYSTEM REMOVED HERE  
            // NO auto cancel
            // NO timeout check
            // NO timeout notification

            $cacheKey = 'driver_' . $user->id . '_last_ride';
            $lastRideId = Cache::get($cacheKey);

            if ($lastRideId && $lastRideId == $ride->id) {
                continue;
            }

            if (!empty($user->device_token)) {
                // Notification send karna ho to yahan add kar sakte ho
                // FireBase notification commented intentionally

                // send_firebase_notification(
                //     'Nearby Ride Found',
                //     'A passenger nearby is requesting a ride.',
                //     $user->device_token,
                //     ['ride_id' => $ride->id]
                // );

                Cache::put($cacheKey, $ride->id, now()->addMinutes(15));
            }

            break;
        }

        return apiResponse($ridesToUpdate, 'Nearby rides fetched successfully.', 200);
    }

    return apiResponse([], 'Ride not found', 200, false);
}


    public function getCaptainById($id)
    {
        $captain = User::with('driverCnic', 'driverliceses', 'vehicles')
            ->where('id', $id)
            ->where('role', 'driver')
            ->find($id);
        $rides = Ride::whereIn('status', ['completed'])
            ->where('driver_id', $id)
            ->get();
        $completedCount = $rides->where('status', 'completed')->count();
        $averageRating = Rating::where('rated_to', $id)->avg('rating');
        return apiResponse([
            'captian' => $captain,
            'average_rating' => round($averageRating, 2),
            'completed_rides_count' => $completedCount
        ], 'Driver Get By ID');
    }
    
    
     public function driverreach(Request $request, $rideId)
    {
        $ride = Ride::findOrFail($rideId);
        $ride->driver_id = auth()->id();
        $ride->status = 'driver_reach';
        $ride->save();
        
        // ✅ Get the passenger user model (user_id refers to passenger)
        $passenger = \App\Models\User::find($ride->user_id);
        if (!empty($passenger) && !empty($passenger->device_token)) {
            $firebaseResponse = send_firebase_notification(
                'Driver Arrived',
                'Captain has reached your pickup location.',
                $passenger->device_token
            );
        }
        return apiResponse($ride, 'Driver Reach in Your Loction!', 200, );
    }
    
       public function startedride(Request $request, $rideId)
    {
        $ride = Ride::findOrFail($rideId);
        $ride->driver_id = auth()->id();
        $ride->status = 'started_ride';
        $ride->save();
    
        // ✅ Get the passenger user model
        $passenger = \App\Models\User::find($ride->user_id);
    
        $firebaseResponse = null;
        if (!empty($passenger) && !empty($passenger->device_token)) {
            try {
                $firebaseResponse = send_firebase_notification(
                    'Ride Started',
                    'Your Captain is on the way to pick you up.',
                    $passenger->device_token
                );
            } catch (\Exception $e) {
                \Log::error('Firebase Notification Error: ' . $e->getMessage());
            }
        }
    
            return apiResponse($ride, 'Driver has started the Ride.', 200,);   
        }

          public function pickride(Request $request, $rideId)
        {
            $ride = Ride::findOrFail($rideId);
            $ride->driver_id = auth()->id();
            $ride->status = 'ride_pick';
            $ride->save();
            
                    // ✅ Get the passenger user model
         $passenger = \App\Models\User::find($ride->user_id);
        $firebaseResponse = null;
        if (!empty($passenger) && !empty($passenger->device_token)) {
            try {
                $firebaseResponse = send_firebase_notification(
                    'Ride Pick',
                    'Ride Started Have a safe journey with SheDrives.',
                    $passenger->device_token
                );
            } catch (\Exception $e) {
                \Log::error('Firebase Notification Error: ' . $e->getMessage());
            }
        }
            return apiResponse($ride, 'Driver has Pick the Passenger.',200, );
        }
        


   public function getAcceptedRides(Request $request)
    {

        $rides = Ride::whereIn('status', ['started_ride', 'accepted','driver_reach','ride_pick'])
            ->where('driver_id', auth()->id())
            ->with(['driver', 'user_pe'])
            ->get();
         
            $ridecomp = Ride::whereIn('status', ['completed'])
            ->where('driver_id', auth()->id())
            ->get();
        $completedCount = $ridecomp->where('status', 'completed')->count();
        $averageRating = Rating::where('rated_to', auth()->id())->avg('rating');

        return apiResponse(['ride'=>$rides, 'average_rating' => round($averageRating, 2),
        'completed_rides_count' => $completedCount], 'Accepted rides with assigned drivers retrieved successfully.');
    }
    public function ridebydriver(Request $request)
    {
        $rides = Ride::whereIn('status', ['completed', 'canceled'])
        ->where('driver_id', auth()->id()) 
            ->with(['driver', 'user_pe', 'vehicles','ratings'])
            ->get();
    
        return apiResponse($rides, 'All Rides.');
    }
    public function completeride(Request $request)
    {
        $driverId = auth()->id();

        // Fetch all rides with status 'completed' or 'canceled'
        $rides = Ride::whereIn('status', ['completed'])
            ->where('driver_id', $driverId)
            ->with(['driver', 'user_pe', 'vehicles','ratings'])
            ->get();

        // Count completed rides
        $completedCount = $rides->where('status', 'completed')->count();

        return apiResponse([
            'rides' => $rides,
            'completed_rides_count' => $completedCount
        ], 'All Complete Rides.');
    }
    
     public function totalammountdata(Request $request)
    {
        $driverId = auth()->id();
        $rides = Ride::where('status', 'completed')
            ->where('driver_id', $driverId)
            ->get(['id', 'final_fare', 'created_at', 'status']);

        // Sum of final_fare
        $totalFinalFare = $rides->sum('final_fare');

        return apiResponse([
            'rides' => $rides,
            'total_final_fare' => $totalFinalFare
        ], 'All Complete Rides.');
    }
    
 public function completeRidesby($rideId)
{
    $ride = Ride::findOrFail($rideId);

    // Sirf driver ya passenger hi ride complete kar sakta hai
    if ($ride->driver_id != auth()->id() && $ride->user_id != auth()->id()) {
        return response()->json(['message' => 'Unauthorized'], 403);
    }

    $ride->status = 'completed';
    $ride->save();

    $firebaseResponse = null;

    // ✅ Sirf Passenger ko notification bhejna hai (ride->user_id)
    $passenger = \App\Models\User::find($ride->user_id);
    if (!empty($passenger) && !empty($passenger->device_token)) {
        try {
            $firebaseResponse = send_firebase_notification(
                'Ride Completed',
                'Your ride has been completed. Don’t forget to rate your captain!',
                $passenger->device_token,
                [
                    'ride_id' => $ride->id,
                    'status'  => 'completed',
                ],
                'high_importance_channel', // channel ID
                'notification'             // sound name
            );
        } catch (\Exception $e) {
            \Log::error('Firebase Notification Error (Passenger): ' . $e->getMessage());
        }
    }

    return apiResponse($ride->status, 'Ride completed successfully.', 200, $firebaseResponse);
}


}
