<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TouchLastSeen
{
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);

        $user = Auth::guard('api')->user();
        if ($user) {
            User::where('id', $user->id)->update(['last_seen_at' => now()]);
        }

        return $response;
    }
}
