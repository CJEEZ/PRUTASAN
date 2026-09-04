<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Providers\RouteServiceProvider;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\Notification;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Display the seller registration view.
     */
    public function sellerCreate(): View
    {
        return view('seller.welcome');
    }

    /**
     * Display the seller registration form (actual signup page).
     */
    public function sellerForm(): View
    {
        // The standalone seller form has been removed from the Start Selling flow.
        // Redirect users to the general registration page instead.
        return redirect(route('register'));
    }

    /**
     * Handle an incoming seller registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function sellerStore(Request $request): RedirectResponse
    {
        // Validation rules for seller registration
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:'.User::class],
            'phone_number' => ['required', 'string', 'max:20'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'agree_terms' => ['required', 'accepted'],
        ]);

        $seller = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone_number' => $request->phone_number,
            'password' => Hash::make($request->password),
            'role' => 'seller',
        ]);

        User::where('role', 'admin')->each(function (User $admin) use ($seller): void {
            Notification::create([
                'user_id' => $admin->id,
                'type' => 'system_update',
                'title' => 'New User Registration',
                'message' => $seller->name . ' registered a new seller account.',
            ]);
        });

        // Do NOT auto-login; redirect back to signup page with success message
        // User will see login form and log in manually
        return redirect(route('seller.register'))->with('signup_success', 'Account created successfully! Please log in with your email and password.');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        // Public signup may create customer or driver accounts only.
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'role' => ['required', 'string', 'in:customer,driver'],
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role, // Store the role from the request
        ]);

        User::where('role', 'admin')->each(function (User $admin) use ($user): void {
            Notification::create([
                'user_id' => $admin->id,
                'type' => 'system_update',
                'title' => 'New User Registration',
                'message' => $user->name . ' registered a new ' . $user->role . ' account.',
            ]);
        });

        // event(new Registered($user)); // Disabled - no email verification needed

        if ($user->role === 'customer') {
            Auth::login($user);

            return redirect()->intended(route('home'));
        }

        if ($user->role === 'driver') {
            return redirect(route('register'))->with(
                'signup_success',
                'Your driver account has been created. Please log in with your email and password.'
            );
        }

        Auth::login($user);

        // --- NEW ROLE-BASED REDIRECTION LOGIC ---
        if ($user->role === 'admin') {
            return redirect()->intended(route('admin.dashboard'));
        }

        if ($user->role === 'seller') {
            // After registering as a seller, send user to the onboarding flow
            return redirect()->intended(route('seller.onboarding'));
        }

        if ($user->role === 'driver') {
            return redirect()->route('driver.dashboard');
        }

        // Default: redirect customer users to the user dashboard (home route)
        return redirect()->intended(route('home'));
    }
}
