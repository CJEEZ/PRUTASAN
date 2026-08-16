<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdatePaymentMethodRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        $type = $this->input('type');

        $rules = [
            'type' => ['required', Rule::in('card', 'bank')],
            'is_default' => 'boolean',
        ];

        if ($type === 'card') {
            $rules = array_merge($rules, [
                'card_type' => ['required', Rule::in('gcash', 'maya', 'bdo')],
                'card_holder_name' => 'required|string|max:255',
                'card_number' => 'required|string|min:10|max:20',
                'expiry_month' => ['required', 'string', 'regex:/^\d{2}$/'],
                'expiry_year' => ['required', 'string', 'digits:4', 'min:' . date('Y')],
            ]);
        } elseif ($type === 'bank') {
            $rules = array_merge($rules, [
                'bank_name' => 'required|string|max:255',
                'account_name' => 'required|string|max:255',
                'account_number' => 'required|string|min:8|max:20',
            ]);
        }

        return $rules;
    }

    public function messages()
    {
        return [
            'type.required' => 'Please select a payment type',
            'type.in' => 'Invalid payment type',
            'card_type.required' => 'Payment provider is required',
            'card_type.in' => 'Invalid payment provider',
            'card_number.required' => 'Account/Reference number is required',
            'card_number.min' => 'Account/Reference number must be at least 10 characters',
            'card_number.max' => 'Account/Reference number must not exceed 20 characters',
            'expiry_month.required' => 'Expiry month is required',
            'expiry_month.regex' => 'Expiry month must be between 01 and 12',
            'expiry_year.required' => 'Expiry year is required',
            'expiry_year.digits' => 'Expiry year must be 4 digits',
            'expiry_year.min' => 'Card has expired',
            'bank_name.required' => 'Bank name is required',
            'account_name.required' => 'Account holder name is required',
            'account_number.required' => 'Account number is required',
        ];
    }
}
