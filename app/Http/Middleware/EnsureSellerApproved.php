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

        if ($user->seller_status !== 'approved' && ! $request->routeIs('seller.approval.*')) {
            return redirect()->route('seller.approval.show')->with(
                'warning',
                'Your seller account must be approved by an admin before you can access the seller dashboard.'
            );
        }

        return $next($request);
    }
}
