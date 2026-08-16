<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SellerApprovalController extends Controller
{
    /**
     * Show the seller approval request page
     */
    public function show()
    {
        $user = Auth::user();

        // Ensure user is a seller
        if ($user->role !== 'seller') {
            return redirect()->route('home')->with('error', 'Only sellers can access this page.');
        }

        return view('seller.approval-request', ['user' => $user]);
    }

    /**
     * Store the seller approval request
     */
    public function store(Request $request)
    {
        $user = Auth::user();

        // Ensure user is a seller
        if ($user->role !== 'seller') {
            return redirect()->route('home')->with('error', 'Only sellers can access this page.');
        }

        // Validate the request
        $validated = $request->validate([
            'shop_name' => 'required|string|max:100',
            'business_type' => 'required|string|max:100',
            'shop_description' => 'required|string|max:1000',
        ]);

        // Update the user with seller approval request data
        $user->update([
            'shop_name' => $validated['shop_name'],
            'business_type' => $validated['business_type'],
            'shop_description' => $validated['shop_description'],
            'seller_status' => 'pending',
            'seller_request_date' => now(),
        ]);

        return redirect()->route('seller.approval.show')
            ->with('success', 'Your seller approval request has been submitted. Please wait for admin review.');
    }
}
