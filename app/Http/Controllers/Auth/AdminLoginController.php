<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class AdminLoginController extends Controller
{
    /**
     * Show the admin login form
     */
    public function create(): View
    {
        return view('auth.admin-login');
    }

    /**
     * Handle admin login
     */
    public function store(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (strtolower($credentials['email']) !== 'admin@fruitexpress.com') {
            return back()->withErrors(['admin' => 'Invalid administrator credentials.']);
        }

        // Attempt to authenticate the canonical admin only
        if (Auth::attempt([
            'email' => 'admin@fruitexpress.com',
            'password' => $credentials['password'],
        ])) {
            $user = Auth::user();

            // Final guard: only the canonical admin account can access the admin area
            if ($user->role !== 'admin' || strtolower((string) $user->email) !== 'admin@fruitexpress.com') {
                Auth::logout();
                $request->session()->invalidate();
                return back()->withErrors(['admin' => 'Invalid administrator credentials.']);
            }

            // Regenerate session
            $request->session()->regenerate();

            // Redirect to admin dashboard
            return redirect()->route('admin.dashboard');
        }

        // Authentication failed
        return back()->withErrors(['admin' => 'Invalid credentials.']);
    }

    /**
     * Logout admin
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}
