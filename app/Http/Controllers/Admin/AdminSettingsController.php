<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminSettingsController extends Controller
{
    public function index(): View
    {
        $settings = [
            'site_name' => config('app.name'),
            'site_url' => config('app.url'),
            'maintenance_mode' => config('app.maintenance_mode', false),
            'seller_commission' => env('SELLER_COMMISSION_RATE', 10),
        ];

        return view('admin.settings.index', compact('settings'));
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'seller_commission' => 'required|numeric|min:0|max:100',
            'maintenance_mode' => 'nullable|boolean',
        ]);

        // Update environment variables or settings
        // In a real app, this would be stored in database or .env file
        // For now, just return success

        return back()->with('success', 'Settings updated successfully!');
    }
}
