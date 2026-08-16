<?php

namespace App\Http\Controllers;

use App\Models\Inquiry;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ProfileController extends Controller
{
    /**
     * Display the user profile.
     */
    public function show(Request $request)
    {
        $user = Auth::user();

        // Allow optional status filter via query param for robust server-side fallback
        $status = $request->query('status', 'all');
        $statusMap = [
            'all' => null,
            'pending' => ['pending'],
            'packed' => ['packed', 'confirmed'],
            'shipped' => ['shipped'],
            'delivered' => ['delivered', 'completed'],
            'cancelled' => ['cancelled'],
        ];

        $query = $user->orders()->with('items.product')->latest();
        if ($status !== 'all') {
            $dbStatuses = $statusMap[$status] ?? [$status];
            $query->whereIn('status', $dbStatuses);
        }

        $orders = $query->get();

        $orderStats = $this->getOrderStats($user->id);
        $activeOrderFilter = $status;

        return view('profile.show', compact('user', 'orders', 'orderStats', 'activeOrderFilter'));
    }

    /**
     * Show the edit profile form.
     */
    public function edit()
    {
        $user = Auth::user();
        return view('profile.edit', compact('user'))
            ->with('activeSection', 'edit');
    }

    /**
     * Update the user profile.
     */
    public function update(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,' . $user->id],
            'phone_number' => ['nullable', 'string', 'max:20'],
            'shipping_address' => ['nullable', 'string', 'max:255'],
            'profile_photo' => ['nullable', 'image', 'mimes:jpeg,png,jpg', 'max:1024'],
        ]);

        // Update basic fields
        $user->update($request->only('name', 'email', 'phone_number', 'shipping_address', 'date_of_birth', 'gender'));

        // Handle profile photo upload if present
        if ($request->hasFile('profile_photo')) {
            $file = $request->file('profile_photo');

            // generate a filename
            $filename = 'profile_' . $user->id . '_' . Str::random(12) . '.' . $file->getClientOriginalExtension();

            // store in the public disk under profile_photos
            $path = $file->storeAs('profile_photos', $filename, 'public');

            if ($path) {
                // delete old photo when exists
                if ($user->profile_photo_path && Storage::disk('public')->exists($user->profile_photo_path)) {
                    Storage::disk('public')->delete($user->profile_photo_path);
                }

                $user->profile_photo_path = $path;
                $user->save();
            }
        }

        return redirect()->route('profile.show')->with('success', 'Profile updated successfully!');
    }

    /**
     * Delete the user account.
     */
    public function destroy(Request $request)
    {
        $user = Auth::user();
        Auth::logout();
        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/')->with('success', 'Account deleted successfully!');
    }

    /**
     * Display the profile completion view.
     */
    public function complete()
    {
        return view('profile.complete');
    }

    /**
     * Show notifications section of the profile.
     */
    public function notifications()
    {
        $user = Auth::user();
        $orders = $user->orders()->with('items.product')->latest()->get();
        $notifications = $user->notifications()->latest()->get();
        $communicationMessages = Inquiry::where(function ($query) use ($user) {
            $query->where('user_id', $user->id)
                ->orWhere('email', $user->email);
        })->latest()->take(8)->get();

        $orderStats = $this->getOrderStats($user->id);
        return view('profile.show', compact('user', 'orders', 'notifications', 'communicationMessages', 'orderStats'))
            ->with('activeSection', 'messages');
    }

    /**
     * Show vouchers section of the profile.
     */
    public function vouchers()
    {
        $user = Auth::user();
        $orders = $user->orders()->with('items.product')->latest()->get();

        $orderStats = $this->getOrderStats($user->id);
        return view('profile.show', compact('user', 'orders', 'orderStats'))
            ->with('activeSection', 'vouchers');
    }

    /**
     * Show payment methods section of the profile.
     */
    public function banks()
    {
        $user = Auth::user();
        $orders = $user->orders()->with('items.product')->latest()->get();
        $paymentMethods = $user->paymentMethods()->get();

        $orderStats = $this->getOrderStats($user->id);
        return view('profile.show', compact('user', 'orders', 'paymentMethods', 'orderStats'))
            ->with('activeSection', 'banks');
    }

    /**
     * Show saved addresses section of the profile.
     */
    public function addresses()
    {
        $user = Auth::user();
        $orders = $user->orders()->with('items.product')->latest()->get();
        $addresses = $user->addresses()->get();

        $orderStats = $this->getOrderStats($user->id);
        return view('profile.show', compact('user', 'orders', 'addresses', 'orderStats'))
            ->with('activeSection', 'addresses');
    }

    /**
     * Show the change password section of the profile.
     */
    public function changePassword()
    {
        $user = Auth::user();
        $orders = $user->orders()->with('items.product')->latest()->get();

        $orderStats = $this->getOrderStats($user->id);
        return view('profile.show', compact('user', 'orders', 'orderStats'))
            ->with('activeSection', 'change-password');
    }

    /**
     * Update the logged-in user password.
     */
    public function updatePassword(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'current_password' => ['required', 'string'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        if (! Hash::check($request->current_password, $user->password)) {
            return back()->withErrors(['current_password' => 'The current password is incorrect.']);
        }

        $user->password = Hash::make($request->password);
        $user->save();

        return redirect()->route('profile.change_password')->with('success', 'Password updated successfully!');
    }

    /**
     * Return orders list HTML filtered by status (AJAX)
     */
    public function orders(Request $request)
    {
        $user = Auth::user();
        $status = $request->query('status', 'all');

        // Map logical filter keys to actual DB statuses
        $statusMap = [
            'all' => null,
            'pending' => ['pending'],
            'packed' => ['packed', 'confirmed'],
            'shipped' => ['shipped'],
            'delivered' => ['delivered', 'completed'],
            'cancelled' => ['cancelled'],
        ];

        $query = $user->orders()->with('items.product')->latest();
        if ($status !== 'all') {
            $dbStatuses = $statusMap[$status] ?? [$status];
            $query->whereIn('status', $dbStatuses);
        }

        $orders = $query->get();

        // If requesting 'all', group orders into logical buckets for display
        if ($status === 'all') {
            $allOrders = $orders;
            $grouped = [
                'pending' => collect(),
                'packed' => collect(),
                'shipped' => collect(),
                'delivered' => collect(),
                'cancelled' => collect(),
            ];

            // define mapping from DB status to bucket
            $map = [
                'pending' => ['pending'],
                'packed' => ['packed', 'confirmed'],
                'shipped' => ['shipped'],
                'delivered' => ['delivered', 'completed'],
                'cancelled' => ['cancelled'],
            ];

            foreach ($allOrders as $o) {
                $placed = false;
                foreach ($map as $bucket => $syns) {
                    if (in_array($o->status, $syns, true)) {
                        $grouped[$bucket]->push($o);
                        $placed = true;
                        break;
                    }
                }
                if (! $placed) {
                    // unknown statuses go into 'pending' fallback
                    $grouped['pending']->push($o);
                }
            }

            return view('profile.orders-list-content', ['groupedOrders' => $grouped]);
        }

        return view('profile.orders-list-content', compact('orders'));
    }

    /**
     * Compute order counts and totals grouped by status for a user.
     */
    protected function getOrderStats(int $userId)
    {
        // Determine which column stores the order total (some migrations use `total_amount`, others use `total`).
        if (Schema::hasColumn('orders', 'total_amount')) {
            $totalCol = 'total_amount';
        } elseif (Schema::hasColumn('orders', 'total')) {
            $totalCol = 'total';
        } else {
            $totalCol = null;
        }

        $selects = ['status', DB::raw('count(*) as count')];
        if ($totalCol) {
            $selects[] = DB::raw("coalesce(sum({$totalCol}),0) as total");
        } else {
            // Fallback: no total column present; return zero totals.
            $selects[] = DB::raw('0 as total');
        }

        $rows = DB::table('orders')
            ->where('user_id', $userId)
            ->select($selects)
            ->groupBy('status')
            ->get()
            ->keyBy('status');

        // Define logical buckets and possible synonyms stored in DB
        $buckets = [
            'all' => [],
            'pending' => ['pending'],
            // Some DBs use 'confirmed' instead of 'packed'
            'packed' => ['packed', 'confirmed'],
            'shipped' => ['shipped'],
            // Accept both 'delivered' and older 'completed' values
            'delivered' => ['delivered', 'completed'],
            'cancelled' => ['cancelled'],
        ];

        // Initialize map with zeros
        $map = array_map(function () {
            return ['count' => 0, 'total' => 0];
        }, $buckets);

        $totalAll = 0;
        $countAll = 0;

        foreach ($rows as $status => $data) {
            $count = (int)$data->count;
            $total = (float)$data->total;
            $countAll += $count;
            $totalAll += $total;

            // Assign this DB status' values into any bucket that lists it as a synonym
            foreach ($buckets as $bucketKey => $synonyms) {
                if ($bucketKey === 'all') continue;
                if (in_array($status, $synonyms, true)) {
                    $map[$bucketKey]['count'] += $count;
                    $map[$bucketKey]['total'] += $total;
                }
            }
        }

        $map['all'] = ['count' => $countAll, 'total' => $totalAll];

        return $map;
    }

    /**
     * Handle the submission of the profile completion form (address).
     */
    public function saveCompletion(Request $request)
    {
        $request->validate([
            'phone_number' => ['required', 'string', 'max:20'],
            'street_address' => ['required', 'string', 'max:255'],
            'barangay' => ['nullable', 'string', 'max:100'],
        ]);

        $user = Auth::user();

        // Save the essential address and phone number
        $user->forceFill([
            'phone_number' => $request->phone_number,
            'shipping_address' => $request->street_address .
                                  ($request->barangay ? ', ' . $request->barangay : '') .
                                  ', Victoria, Oriental Mindoro 5205', // Concatenate fixed parts
        ])->save();

        // Redirect to the intended page or the catalog
        return redirect()->intended(route('catalog.index'))->with('success', 'Profile and shipping address saved!');
    }
}
