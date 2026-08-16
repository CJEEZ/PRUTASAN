<?php

namespace App\Http\Controllers;

use App\Models\PaymentMethod;
use App\Http\Requests\StorePaymentMethodRequest;
use App\Http\Requests\UpdatePaymentMethodRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PaymentMethodController extends Controller
{
    public function index()
    {
        $paymentMethods = Auth::user()->paymentMethods;
        return view('profile.banks-and-cards', compact('paymentMethods'));
    }

    public function create()
    {
        return view('profile.banks-and-cards');
    }

    public function store(StorePaymentMethodRequest $request)
    {
        $data = $request->validated();
        $data['user_id'] = Auth::id();

        // Handle card type fields
        if ($data['type'] === 'card' && isset($data['card_number'])) {
            // Extract last 4 digits before masking
            $cleaned = str_replace(' ', '', $data['card_number']);
            $data['card_last_four'] = substr($cleaned, -4);
            // Mask the card number
            $data['card_number'] = $this->maskCardNumber($data['card_number']);
        }

        // If this is set as default, unset other defaults
        if ($data['is_default']) {
            Auth::user()->paymentMethods()->update(['is_default' => false]);
        }

        PaymentMethod::create($data);

        $redirectRoute = Auth::user()->role === 'seller' ? 'seller.bank_accounts' : 'profile.banks';

        return redirect()->route($redirectRoute)->with('success', 'Payment method added successfully!');
    }

    public function show(PaymentMethod $paymentMethod)
    {
        $this->authorize('view', $paymentMethod);
        return response()->json($paymentMethod);
    }

    public function edit(PaymentMethod $paymentMethod)
    {
        $this->authorize('update', $paymentMethod);
        return view('profile.banks-and-cards', compact('paymentMethod'));
    }

    public function update(UpdatePaymentMethodRequest $request, PaymentMethod $paymentMethod)
    {
        $this->authorize('update', $paymentMethod);

        $data = $request->validated();

        // Handle card type fields
        if ($data['type'] === 'card' && isset($data['card_number'])) {
            // Extract last 4 digits before masking
            $cleaned = str_replace(' ', '', $data['card_number']);
            $data['card_last_four'] = substr($cleaned, -4);
            // Mask the card number
            $data['card_number'] = $this->maskCardNumber($data['card_number']);
        }

        // If this is set as default, unset other defaults
        if ($data['is_default']) {
            Auth::user()->paymentMethods()->where('id', '!=', $paymentMethod->id)->update(['is_default' => false]);
        }

        $paymentMethod->update($data);

        $redirectRoute = Auth::user()->role === 'seller' ? 'seller.bank_accounts' : 'profile.banks';

        return redirect()->route($redirectRoute)->with('success', 'Payment method updated successfully!');
    }

    public function destroy(PaymentMethod $paymentMethod)
    {
        $this->authorize('delete', $paymentMethod);
        $paymentMethod->delete();

        $redirectRoute = Auth::user()->role === 'seller' ? 'seller.bank_accounts' : 'profile.banks';

        return redirect()->route($redirectRoute)->with('success', 'Payment method deleted successfully!');
    }

    public function setDefault(PaymentMethod $paymentMethod)
    {
        $this->authorize('update', $paymentMethod);

        // Unset all defaults first
        Auth::user()->paymentMethods()->update(['is_default' => false]);

        // Set this one as default
        $paymentMethod->update(['is_default' => true]);

        $redirectRoute = Auth::user()->role === 'seller' ? 'seller.bank_accounts' : 'profile.banks';

        return redirect()->route($redirectRoute)->with('success', 'Default payment method updated!');
    }

    private function maskCardNumber($cardNumber)
    {
        // Remove spaces and mask the number
        $cleaned = str_replace(' ', '', $cardNumber);
        return '**** **** **** ' . substr($cleaned, -4);
    }
}
