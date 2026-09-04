<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DriverApplication;
use Illuminate\Http\Request;

class AdminDriverController extends Controller
{
    public function index(Request $request)
    {
        $applications = DriverApplication::with('user')
            ->when($request->filled('status'), fn ($query) => $query->where('status', $request->status))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('admin.drivers.index', compact('applications'));
    }

    public function update(Request $request, DriverApplication $application)
    {
        $validated = $request->validate([
            'status' => ['required', 'in:approved,rejected,pending,hired'],
            'rejection_reason' => ['nullable', 'string', 'max:1000'],
        ]);

        $application->update([
            'status' => $validated['status'],
            'rejection_reason' => $validated['status'] === 'rejected' ? ($validated['rejection_reason'] ?? null) : null,
            'reviewed_at' => $validated['status'] === 'pending' ? null : now(),
        ]);

        return back()->with('success', 'Driver application updated.');
    }
}
