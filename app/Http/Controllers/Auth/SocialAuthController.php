<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;

class SocialAuthController extends Controller
{
    /**
     * Redirect to Google OAuth provider
     */
    public function redirectToGoogle()
    {
        // preserve optional role (e.g., ?role=seller) so we can assign it after callback
        $role = request()->query('role');
        if ($role) {
            session(['social_role' => $role]);
        }
        return Socialite::driver('google')->redirect();
    }

    /**
     * Handle Google OAuth callback
     */
    public function handleGoogleCallback()
    {
        try {
            $user = Socialite::driver('google')->user();
            return $this->handleSocialLogin($user, 'google');
        } catch (\Exception $e) {
            return redirect()->route('login')->with('error', 'Unable to login with Google.');
        }
    }

    /**
     * Redirect to Facebook OAuth provider
     */
    public function redirectToFacebook()
    {
        // preserve optional role (e.g., ?role=seller) so we can assign it after callback
        $role = request()->query('role');
        if ($role) {
            session(['social_role' => $role]);
        }
        return Socialite::driver('facebook')->redirect();
    }

    /**
     * Handle Facebook OAuth callback
     */
    public function handleFacebookCallback()
    {
        try {
            $user = Socialite::driver('facebook')->user();
            return $this->handleSocialLogin($user, 'facebook');
        } catch (\Exception $e) {
            return redirect()->route('login')->with('error', 'Unable to login with Facebook.');
        }
    }

    /**
     * Handle social login logic
     */
    private function handleSocialLogin($socialUser, $provider)
    {
        // Check if user already exists with this provider
        $user = User::where('provider', $provider)
            ->where('provider_id', $socialUser->getId())
            ->first();

        if ($user) {
            Auth::login($user);
            return redirect()->intended(route('dashboard'));
        }

        // Check if email exists
        $existingUser = User::where('email', $socialUser->getEmail())->first();

        if ($existingUser) {
            // Link the provider to existing account and set role if provided in session
            $data = [
                'provider' => $provider,
                'provider_id' => $socialUser->getId(),
            ];
            $role = session('social_role');
            if ($role) {
                $data['role'] = $role;
                // clear after use
                session()->forget('social_role');
            }
            $existingUser->update($data);
            Auth::login($existingUser);
            return redirect()->intended(route('dashboard'));
        }

        // Create new user
        $newUserData = [
            'name' => $socialUser->getName(),
            'email' => $socialUser->getEmail(),
            'provider' => $provider,
            'provider_id' => $socialUser->getId(),
            'password' => bcrypt('social_auth_' . uniqid()),
            'email_verified_at' => now(),
        ];
        $role = session('social_role');
        if ($role) {
            $newUserData['role'] = $role;
            session()->forget('social_role');
        }

        $newUser = User::create($newUserData);

        Auth::login($newUser);
        return redirect()->intended(route('dashboard'));
    }
}
