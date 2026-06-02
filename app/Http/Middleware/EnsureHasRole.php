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

        // SuperAdmin and SAO inherit all roles — they pass any role check
        if ($user->isSuperAdmin() || $user->isSao()) {
            return $next($request);
        }

        $roleValue = $user->role instanceof UserRole
            ? $user->role->value
            : (string) $user->role;

        // Check if user's role is in the inherited roles of any required role
        $hasRole = false;
        foreach ($roles as $requiredRole) {
            $requiredEnum = UserRole::tryFrom($requiredRole);
            if ($requiredEnum !== null) {
                $inherited = $requiredEnum->inheritedRoles();
                if (in_array($roleValue, $inherited, true)) {
                    $hasRole = true;
                    break;
                }
            } elseif ($roleValue === $requiredRole) {
                $hasRole = true;
                break;
            }
        }

        if (! $hasRole) {
            if ($request->expectsJson()) {
                abort(403, 'Insufficient role.');
            }
            abort(403);
        }

        return $next($request);
    }
}
