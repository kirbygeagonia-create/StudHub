<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureHasRole
{
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $user = $request->user();

        if (! ($user instanceof User)) {
            abort(403);
        }

        if (! in_array($user->role->value, $roles, true)) {
            abort(403);
        }

        return $next($request);
    }
}