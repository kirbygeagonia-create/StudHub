<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsOnboarded
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user !== null && ! $user->hasCompletedOnboarding()) {
            if ($request->expectsJson()) {
                abort(403, 'User has not completed onboarding.');
            }

            if (! $request->routeIs('onboarding.*', 'logout', 'verification.*', 'password.confirm')) {
                return redirect()->route('onboarding.show');
            }
        }

        return $next($request);
    }
}
