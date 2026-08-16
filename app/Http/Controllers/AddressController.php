<?php

namespace App\Http\Controllers;

use App\Models\Address;
use App\Http\Requests\StoreAddressRequest;
use App\Http\Requests\UpdateAddressRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AddressController extends Controller
{
    public function index()
    {
        $addresses = Auth::user()->addresses;
        return view('profile.addresses', compact('addresses'));
    }

    public function create()
    {
        return view('profile.addresses');
    }

    public function store(StoreAddressRequest $request)
    {
        $data = $request->validated();
        $data['user_id'] = Auth::id();

        // If this is set as default, unset other defaults
        if ($data['is_default']) {
            Auth::user()->addresses()->update(['is_default' => false]);
        }

        Address::create($data);

        return redirect()->route('profile.show')->with('success', 'Address added successfully!');
    }

    public function show(Address $address)
    {
        $this->authorize('view', $address);
        return response()->json($address);
    }

    public function edit(Address $address)
    {
        $this->authorize('update', $address);
        return view('profile.addresses', compact('address'));
    }

    public function update(UpdateAddressRequest $request, Address $address)
    {
        $this->authorize('update', $address);

        $data = $request->validated();

        // If this is set as default, unset other defaults
        if ($data['is_default']) {
            Auth::user()->addresses()->where('id', '!=', $address->id)->update(['is_default' => false]);
        }

        $address->update($data);

        return redirect()->route('profile.show')->with('success', 'Address updated successfully!');
    }

    public function destroy(Address $address)
    {
        $this->authorize('delete', $address);
        $address->delete();

        return redirect()->route('profile.show')->with('success', 'Address deleted successfully!');
    }

    public function setDefault(Address $address)
    {
        $this->authorize('update', $address);

        // Unset all defaults first
        Auth::user()->addresses()->update(['is_default' => false]);

        // Set this one as default
        $address->update(['is_default' => true]);

        return redirect()->route('profile.show')->with('success', 'Default address updated!');
    }
}