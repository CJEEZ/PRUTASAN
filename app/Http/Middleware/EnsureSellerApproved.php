<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EnsureSellerApproved
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next)
    {
        $user = Auth::user();

        // If user is not a seller, let other middleware handle access
        if (! $user || $user->role !== 'seller') {
            return $next($request);
        }

        // If seller not approved (email_verified_at null), allow viewing but show a warning.
        // This lets sellers access their dashboard while still preventing sensitive actions
        // (withdrawal etc. are protected by other middleware).
        if (is_null($user->email_verified_at)) {
            $request->session()->flash('warning', 'Your seller account is pending admin approval. Some actions may be restricted.');
            return $next($request);
        }

        return $next($request);
    }
}
