<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class UpdateLastSeenAt
{
    /**
     * Update the authenticated user's last_seen_at timestamp,
     * throttled to once per minute to avoid excessive writes.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        $user = Auth::user();

        if ($user === null) {
            return $response;
        }

        $lastSeen = $user->last_seen_at;

        if ($lastSeen === null || now()->diffInMinutes($lastSeen) >= 1) {
            $user->timestamps = false;
            $user->forceFill(['last_seen_at' => now()])->save();
            $user->timestamps = true;
        }

        return $response;
    }
}
