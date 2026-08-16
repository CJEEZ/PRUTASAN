<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AdminSellerApprovalController extends Controller
{
    /**
     * Show list of pending seller approval requests
     */
    public function index()
    {
        $pendingRequests = User::where('role', 'seller')
            ->where('seller_status', 'pending')
            ->orderBy('seller_request_date', 'desc')
            ->paginate(10);

        $approvedSellers = User::where('role', 'seller')
            ->where('seller_status', 'approved')
            ->count();

        $rejectedRequests = User::where('role', 'seller')
            ->where('seller_status', 'rejected')
            ->count();

        return view('admin.seller-approvals.index', compact('pendingRequests', 'approvedSellers', 'rejectedRequests'));
    }

    /**
     * Show individual seller request details
     */
    public function show(User $user)
    {
        if ($user->role !== 'seller') {
            abort(404);
        }

        return view('admin.seller-approvals.show', compact('user'));
    }

    /**
     * Approve a seller request
     */
    public function approve(User $user)
    {
        if ($user->role !== 'seller' || $user->seller_status !== 'pending') {
            return redirect(route('admin.seller-approvals.index'))
                ->with('error', 'Cannot approve this seller request.');
        }

        $user->update([
            'seller_status' => 'approved',
            'seller_rejection_reason' => null,
        ]);

        return redirect(route('admin.seller-approvals.index'))
            ->with('success', "Seller '{$user->name}' has been approved!");
    }

    /**
     * Reject a seller request
     */
    public function reject(User $user, Request $request)
    {
        if ($user->role !== 'seller' || $user->seller_status !== 'pending') {
            return redirect(route('admin.seller-approvals.index'))
                ->with('error', 'Cannot reject this seller request.');
        }

        $validated = $request->validate([
            'rejection_reason' => ['required', 'string', 'max:500'],
        ]);

        $user->update([
            'seller_status' => 'rejected',
            'seller_rejection_reason' => $validated['rejection_reason'],
        ]);

        return redirect(route('admin.seller-approvals.index'))
            ->with('success', "Seller '{$user->name}' request has been rejected.");
    }
}
