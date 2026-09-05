<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use App\Models\Shipment;
use App\Models\Notification;

class DriverController extends Controller
{
    public function dashboard()
    {
        $user = Auth::user();
        abort_unless($user && $user->role === 'driver', 403);

        $application = $user->driverApplications()->latest()->first();

        $driverName = $user->name;
        $driverAvailable = (bool) $user->driver_available;
        $activeDeliveries = $this->shipmentsFor($user)->whereIn('status', ['shipped', 'in_transit', 'out_for_delivery', 'to_receive'])->get();
        $availableDeliveries = Shipment::with('order')->whereNull('driver_id')->whereIn('status', ['shipped', 'ready_for_pickup'])->latest()->get();

        return view('driver.dashboard', compact('application', 'driverName', 'driverAvailable', 'activeDeliveries', 'availableDeliveries'));
    }

    public function claim(Shipment $shipment)
    {
        $user = $this->approvedDriver();

        $claimed = Shipment::whereKey($shipment->id)
            ->whereNull('driver_id')
            ->whereIn('status', ['shipped', 'ready_for_pickup'])
            ->update(['driver_id' => $user->id, 'status' => 'in_transit']);

        if (! $claimed) {
            return back()->with('error', 'This delivery was already claimed by another driver.');
        }

        $shipment->refresh()->load('order.items.product');
        $this->notifyDeliveryParties($shipment, 'Delivery picked up', $user->name . ' picked up your order and is now transporting it.');

        return back()->with('success', 'Delivery claimed. You are now responsible for its tracking updates.');
    }

    public function updateDelivery(Request $request, Shipment $shipment)
    {
        $user = $this->approvedDriver();
        abort_unless((int) $shipment->driver_id === (int) $user->id, 403);

        $data = $request->validate([
            'status' => ['required', 'in:in_transit,out_for_delivery,delivered'],
            'location' => ['nullable', 'string', 'max:255'],
            'latitude' => ['nullable', 'numeric', 'between:-90,90'],
            'longitude' => ['nullable', 'numeric', 'between:-180,180'],
        ]);

        $shipment->load('order.items.product');
        $shipment->update(['status' => $data['status']]);
        $this->trackingService()->updateOrderStatus(
            $shipment->order,
            $data['status'],
            $data['location'] ?? null,
            'Live update from rider ' . $user->name,
            $data['latitude'] ?? null,
            $data['longitude'] ?? null
        );
        $this->notifyDeliveryParties($shipment, 'Delivery update: ' . str_replace('_', ' ', $data['status']), 'Rider ' . $user->name . ' reported your order as ' . str_replace('_', ' ', $data['status']) . ($data['location'] ?? null ? ' near ' . $data['location'] : '') . '.');

        return back()->with('success', 'Delivery status and location shared with the customer and seller.');
    }

    public function updateLocation(Request $request, Shipment $shipment)
    {
        $user = $this->approvedDriver();
        abort_unless((int) $shipment->driver_id === (int) $user->id, 403);

        if (! in_array($shipment->status, ['in_transit', 'out_for_delivery'], true)) {
            return response()->json(['message' => 'This delivery is not active.'], 422);
        }

        $data = $request->validate([
            'latitude' => ['required', 'numeric', 'between:-90,90'],
            'longitude' => ['required', 'numeric', 'between:-180,180'],
        ]);

        $shipment->order()->update([
            'driver_latitude' => $data['latitude'],
            'driver_longitude' => $data['longitude'],
            'driver_location_updated_at' => now(),
        ]);

        return response()->json(['updated_at' => now()->toIso8601String()]);
    }

    public function analytics()
    {
        $user = Auth::user();
        abort_unless($user && $user->role === 'driver', 403);
        $shipments = $this->shipmentsFor($user)->get();
        return view('driver.section', ['section' => 'analytics', 'shipments' => $shipments, 'driverName' => $user->name]);
    }

    public function schedule()
    {
        $user = Auth::user();
        abort_unless($user && $user->role === 'driver', 403);
        $shipments = $this->shipmentsFor($user)->whereNotIn('status', ['delivered', 'completed', 'cancelled'])->orderBy('shipped_at')->get();
        return view('driver.section', ['section' => 'schedule', 'shipments' => $shipments, 'driverName' => $user->name]);
    }

    public function history()
    {
        $user = Auth::user();
        abort_unless($user && $user->role === 'driver', 403);
        $shipments = $this->shipmentsFor($user)->whereIn('status', ['delivered', 'completed'])->latest('updated_at')->get();
        return view('driver.section', ['section' => 'history', 'shipments' => $shipments, 'driverName' => $user->name]);
    }

    public function messages()
    {
        $user = Auth::user();
        abort_unless($user && $user->role === 'driver', 403);

        $messages = $user->notifications()->latest()->get();

        return view('driver.messages', compact('user', 'messages'));
    }

    public function profile()
    {
        $user = Auth::user();
        abort_unless($user && $user->role === 'driver', 403);

        $application = $user->driverApplications()->latest()->first();
        $driverStats = [
            'active' => $user->driverShipments()->whereIn('status', ['shipped', 'in_transit', 'out_for_delivery', 'to_receive'])->count(),
            'completed' => $user->driverShipments()->whereIn('status', ['delivered', 'completed'])->count(),
            'total' => $user->driverShipments()->count(),
        ];

        return view('driver.profile', compact('user', 'application', 'driverStats'));
    }

    public function updateProfile(Request $request)
    {
        $user = Auth::user();
        abort_unless($user && $user->role === 'driver', 403);

        $application = $user->driverApplications()->latest()->first();
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email,' . $user->id],
            'phone_number' => ['nullable', 'string', 'max:20'],
            'date_of_birth' => ['nullable', 'date'],
            'gender' => ['nullable', 'in:male,female,other'],
            'shipping_address' => ['nullable', 'string', 'max:255'],
            'profile_photo' => ['nullable', 'image', 'mimes:jpeg,jpg,png', 'max:1024'],
            'license_serial_number' => [$application ? 'required' : 'nullable', 'string', 'max:100'],
            'license_photo' => ['nullable', 'image', 'mimes:jpeg,jpg,png', 'max:5120'],
            'or_photo' => ['nullable', 'image', 'mimes:jpeg,jpg,png', 'max:5120'],
            'cr_photo' => ['nullable', 'image', 'mimes:jpeg,jpg,png', 'max:5120'],
        ]);

        $user->update($request->only('name', 'email', 'phone_number', 'date_of_birth', 'gender', 'shipping_address'));

        if ($request->hasFile('profile_photo')) {
            $photoPath = $request->file('profile_photo')->storeAs(
                'profile_photos',
                'driver_profile_' . $user->id . '_' . Str::random(12) . '.' . $request->file('profile_photo')->getClientOriginalExtension(),
                'public'
            );

            if ($user->profile_photo_path && Storage::disk('public')->exists($user->profile_photo_path)) {
                Storage::disk('public')->delete($user->profile_photo_path);
            }

            $user->update(['profile_photo_path' => $photoPath]);
        }

        if ($application) {
            $paths = [];
            foreach (['license_photo', 'or_photo', 'cr_photo'] as $field) {
                if (! $request->hasFile($field)) {
                    continue;
                }
                $paths[$field . '_path'] = $request->file($field)->storeAs(
                    'driver_documents/' . $user->id,
                    $field . '_' . Str::random(16) . '.' . $request->file($field)->getClientOriginalExtension(),
                    'public'
                );
                if ($application->{$field . '_path'}) {
                    Storage::disk('public')->delete($application->{$field . '_path'});
                }
            }
            $application->update(array_merge(['license_serial_number' => $validated['license_serial_number']], $paths));
        }

        return redirect()->route('driver.profile')->with('success', 'Driver profile updated successfully.');
    }

    public function availability(Request $request)
    {
        $user = Auth::user();
        abort_unless($user && $user->role === 'driver', 403);
        $user->update(['driver_available' => $request->boolean('available')]);
        return back();
    }

    private function shipmentsFor($user)
    {
        return Shipment::with('order')->where('driver_id', $user->id);
    }

    private function approvedDriver()
    {
        $user = Auth::user();
        abort_unless($user && $user->role === 'driver' && in_array(optional($user->driverApplications()->latest()->first())->status, ['approved', 'hired'], true), 403);
        return $user;
    }

    private function trackingService()
    {
        return app(\App\Services\TrackingService::class);
    }

    private function notifyDeliveryParties(Shipment $shipment, string $title, string $message): void
    {
        $recipients = collect([$shipment->order->user_id]);
        $sellerIds = $shipment->order->items->pluck('product.seller_id')->filter();

        foreach ($recipients->merge($sellerIds)->unique() as $userId) {
            Notification::create([
                'user_id' => $userId,
                'type' => 'order_update',
                'title' => $title,
                'message' => $message,
                'order_id' => $shipment->order_id,
            ]);
        }
    }

    public function submit(Request $request)
    {
        $user = Auth::user();
        abort_unless($user && $user->role === 'driver', 403);

        $application = $user->driverApplications()->latest()->first();

        if ($application && $application->status === 'pending') {
            return back()->with('success', 'Your driver application is pending admin review. You cannot upload new documents yet.');
        }

        $validated = $request->validate([
            'license_serial_number' => ['required', 'string', 'max:100'],
            'license_photo' => [$application ? 'nullable' : 'required', 'image', 'mimes:jpeg,jpg,png', 'max:5120'],
            'or_photo' => [$application ? 'nullable' : 'required', 'image', 'mimes:jpeg,jpg,png', 'max:5120'],
            'cr_photo' => [$application ? 'nullable' : 'required', 'image', 'mimes:jpeg,jpg,png', 'max:5120'],
        ]);

        if ($application && in_array($application->status, ['approved', 'hired'], true)) {
            return back()->with('success', 'Your driver application has already been approved and hired.');
        }

        $paths = [];
        foreach (['license_photo', 'or_photo', 'cr_photo'] as $field) {
            if (! $request->hasFile($field)) {
                continue;
            }
            $file = $request->file($field);
            $paths[$field . '_path'] = $file->storeAs(
                'driver_documents/' . $user->id,
                $field . '_' . Str::random(16) . '.' . $file->getClientOriginalExtension(),
                'public'
            );
        }

        if ($application) {
            foreach (['license_photo_path', 'or_photo_path', 'cr_photo_path'] as $pathField) {
                if (isset($paths[$pathField]) && $application->{$pathField}) {
                    Storage::disk('public')->delete($application->{$pathField});
                }
            }
            $application->update(array_merge([
                'license_serial_number' => $validated['license_serial_number'],
            ], $paths, [
                'status' => 'pending',
                'rejection_reason' => null,
                'reviewed_at' => null,
            ]));
        } else {
            $user->driverApplications()->create(array_merge([
                'license_serial_number' => $validated['license_serial_number'],
            ], $paths));
        }

        return redirect()->route('driver.dashboard')->with('success', 'Application submitted. Admin will review your documents soon.');
    }
}
