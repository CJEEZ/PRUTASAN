<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;

class UserProfileController extends Controller
{
    /**
     * Display the profile completion view (shipping address form).
     */
    public function create(): View
    {
        return view('profile.complete');
    }

    /**
     * Handle the profile completion submission.
     */
    public function store(Request $request): RedirectResponse
    {
        $user = Auth::user();

        // 1. Validation (matches fields in address.png)
        $validated = $request->validate([
            'full_name' => ['required', 'string', 'max:255'],
            'phone_number' => ['required', 'string', 'max:20'],
            'street_address' => ['required', 'string', 'max:255'],
            'barangay' => ['nullable', 'string', 'max:255'],
            'city' => ['required', 'string', 'max:255'],
            'province' => ['required', 'string', 'max:255'],
            'postal_code' => ['required', 'string', 'max:10'],
        ]);

        // 2. Save the validated data as a JSON array in the shipping_address column
        $user->shipping_address = $validated;
        $user->save();

        // 3. Redirect the user to the main catalog
        return redirect()->route('catalog.index')->with('status', 'Profile information saved successfully! You can now place orders.');
    }
}