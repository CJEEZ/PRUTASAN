<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EnsureSellerOnboarded
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next)
    {
        $user = Auth::user();

        // Must be authenticated seller
        if (! $user || $user->role !== 'seller') {
            abort(403);
        }

        // Check basic onboarding: pickup/shipping address and phone number
        if (empty($user->shipping_address) || empty($user->phone_number)) {
            return redirect()->route('seller.onboarding')
                ->with('warning', 'Please complete your shop information before requesting withdrawals.');
        }

        return $next($request);
    }
}
