<?php

namespace App\Http\Middleware;

use App\Domain\Identity\Enums\UserRole;
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
            if ($request->expectsJson()) {
                abort(403, 'Unauthenticated.');
            }
            abort(403);
        }

        // SuperAdmin inherits all roles — they pass any role check
        if ($user->isSuperAdmin()) {
            return $next($request);
        }

        $roleValue = $user->role instanceof UserRole
            ? $user->role->value
            : (string) $user->role;

        if (! in_array($roleValue, $roles, true)) {
            if ($request->expectsJson()) {
                abort(403, 'Insufficient role.');
            }
            abort(403);
        }

        return $next($request);
    }
}
