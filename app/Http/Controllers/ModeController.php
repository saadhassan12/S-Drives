<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ModeController extends Controller
{
    //

    public function passengermood()
{
    $user = auth()->user();

    if ($user) {
        $user->update([
            'role' => 'passenger',
            'last_login_at'=> 0,
        ]);

        $user->tokens()->update(['revoked' => true]);

        $tokenResult = $user->createToken('authToken')->accessToken;

        return response()->json([
           'status' => 200, 
            'message' => 'User role updated to passenger successfully',
            'data'=> $user,
            'token' => $tokenResult
        ], 200);
    }

    return response()->json([
        'status' => false,
        'message' => 'No authenticated user found.',
    ], 404);
}


   public function drivermood()
{
    $user = auth()->user();

    if ($user) {
        $user->update([
            'role' => 'driver',
            'last_login_at' => 1,
        ]);

        $user->tokens()->update(['revoked' => true]);

        $tokenResult = $user->createToken('authToken')->accessToken;

        return response()->json([
            'status' => 200,
            'message' => 'User role updated to driver successfully.',
            'data'=> $user,
            'token' => $tokenResult
        ], 200);
    }

    return response()->json([
        'status' => false,
        'message' => 'No authenticated user found.',
    ], 404);
}

}
