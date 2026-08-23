<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ModeController extends Controller
{
    public function drivermood(Request $request)
    {
        $user = auth()->user();

        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'No authenticated user found.',
            ], 404);
        }

        $user->update([
            'role' => 'driver',
            'last_login_at' => 1,
        ]);

        $freshUser = $user->fresh();

        return response()->json([
            'status' => 200,
            'message' => 'User role updated to driver successfully.',
            'data' => $freshUser,
            'role' => $freshUser->role,
            'token' => $request->bearerToken(),
        ], 200);
    }

    public function passengermood(Request $request)
    {
        $user = auth()->user();

        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'No authenticated user found.',
            ], 404);
        }

        $user->update([
            'role' => 'passenger',
            'last_login_at' => 0,
        ]);

        $freshUser = $user->fresh();

        return response()->json([
            'status' => 200,
            'message' => 'User role updated to passenger successfully',
            'data' => $freshUser,
            'role' => $freshUser->role,
            'token' => $request->bearerToken(),
        ], 200);
    }
}
