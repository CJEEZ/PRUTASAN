<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Auth;

class CheckAdminAccess
{
    /**
     * Handle an incoming request.
     * Allows access if user is authenticated admin OR has secret key access
     */
    public function handle(Request $request, Closure $next)
    {
        // Check if user is authenticated and has admin role
        if (Auth::check() && Auth::user()->role === 'admin') {
            return $next($request);
        }

        // Check if user has secret key access from session
        if (Session::has('admin_secret_access') && Session::get('admin_secret_access') === true) {
            return $next($request);
        }

        // Deny access and redirect to admin login
        return redirect()->route('admin.login');
    }
}
