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

        $adminEmail = strtolower($credentials['email']);

        if (Auth::attempt([
            'email' => $adminEmail,
            'password' => $credentials['password'],
        ])) {
            $user = Auth::user();

            if (strtolower((string) $user->role) !== 'admin') {
                Auth::logout();
                $request->session()->invalidate();
                return back()->withErrors(['admin' => 'Invalid administrator credentials.']);
            }

            $request->session()->regenerate();

            return redirect()->route('admin.dashboard');
        }

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
