<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureProfileIsComplete
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check()) {
            $user = Auth::user();

            // Check if the user is a regular user AND their profile is incomplete.
            if ($user->isUser() && !$user->hasCompletedProfile()) {
                // If they are already on the profile completion page, allow them through.
                if ($request->routeIs('profile.complete.form')) {
                    return $next($request);
                }

                // Redirect to the profile completion form.
                return redirect()->route('profile.complete.form');
            }
        }

        return $next($request);
    }
}