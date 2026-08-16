<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Ipakita ang login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Hawakan ang papasok na authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        // 1. I-authenticate ang user (kasama na ang rate limiting)
        Log::info('login-store-before-authenticate', [
            'email' => $request->input('email'),
        ]);
        $request->authenticate();

        Log::info('login-store-after-authenticate', [
            'auth_check' => Auth::check(),
            'auth_id' => Auth::id(),
            'session_id' => $request->session()->getId(),
        ]);

        // 2. I-regenerate ang session ID para sa seguridad
        $request->session()->regenerate();

        Log::info('login-store-after-regenerate', [
            'auth_check' => Auth::check(),
            'auth_id' => Auth::id(),
            'session_id' => $request->session()->getId(),
        ]);

        // 3. Check if user is admin and redirect accordingly
        $user = Auth::user();
        if ($user->role === 'admin') {
            return redirect()->intended('/admin');
        }

        // Regular users go to the app home/dashboard
        return redirect()->intended('/');
    }

    /**
     * Wasakin ang isang authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
