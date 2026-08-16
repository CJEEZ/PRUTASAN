<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EnsureRole
{
    /**
     * Handle an incoming request.
     * Usage: middleware('role:seller') or 'role:admin|seller'
     */
    public function handle(Request $request, Closure $next, string $roles)
    {
        $user = Auth::user();
        if (! $user) {
            abort(403);
        }

        $allowed = explode('|', $roles);
        if (! in_array($user->role, $allowed, true)) {
            abort(403);
        }

        return $next($request);
    }
}
