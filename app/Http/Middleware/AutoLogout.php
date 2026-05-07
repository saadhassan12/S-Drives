<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use App\Models\Ride;
use Illuminate\Support\Facades\Log;

class AutoLogout
{ 
    public function handle(Request $request, Closure $next)
    {
        $token = $request->bearerToken();
    
        if ($token) {
            $user = Auth::guard('api')->user();
    
            if (!$user) {
                Log::info('Session expired for API user', ['ip' => $request->ip()]);
                return response()->json([
                    'message' => 'Session expired. Please log in again.'
                ], 401);
            }
        }
    
        return $next($request);
    }
}

