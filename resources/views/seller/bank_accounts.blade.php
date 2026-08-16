@extends('layouts.app')

@section('hideHeader')@endsection

@section('content')
<div class="flex w-full p-6 gap-6" style="min-height:70vh;">
    <aside class="hidden md:block w-64 bg-white rounded shadow p-4">
        @include('seller._sidebar')
    </aside>
    <div class="md:hidden w-full">
        @include('seller._mobile_nav')
    </div>
    <div class="flex-1">
        <div class="bg-white rounded shadow p-6">
            <h2 class="text-xl font-semibold mb-4">Bank Accounts</h2>
            <p class="text-gray-600">Link and manage your payout account.</p>

            <div class="mt-6 rounded-xl border border-gray-200 bg-gray-50 p-4">
                <div class="flex items-center justify-between gap-3">
                    <div>
                        <h3 class="text-sm font-semibold text-gray-700">Saved Bank & Cards</h3>
                        <p class="text-xs text-gray-500">Manage your payout account</p>
                    </div>
                    <button type="button" onclick="showAddPaymentMethodModal()" class="inline-flex items-center justify-center rounded-lg bg-emerald-600 px-3 py-2 text-xs font-semibold text-white hover:bg-emerald-700">
                        <i class="fas fa-plus mr-1"></i> Add
                    </button>
                </div>

                @if(($paymentMethods ?? collect())->isEmpty())
                    <div class="mt-4 rounded-lg border border-dashed border-gray-300 bg-white p-4 text-sm text-gray-500">
                        No payment method saved yet.
                    </div>
                @else
                    <div class="mt-4 space-y-3">
                        @foreach($paymentMethods ?? [] as $method)
                            <div class="flex items-center justify-between gap-3 rounded-lg border border-gray-200 bg-white p-3">
                                <div class="flex items-center gap-3">
                                    @if($method->type === 'card')
                                        @if($method->card_type === 'gcash')
                                            <img src="https://www.thefastmode.com/media/k2/items/src/03160998318f697230a7e611fb0fa87d.jpg?t=20200629_013741" alt="GCash" class="h-12 w-14 rounded-lg bg-white object-cover shadow-sm ring-1 ring-gray-200">
                                        @elseif($method->card_type === 'maya')
                                            <img src="https://cdn.manilastandard.net/wp-content/uploads/2025/02/maya-logo-black.png" alt="Maya" class="h-12 w-14 rounded-lg bg-white object-cover shadow-sm ring-1 ring-gray-200">
                                        @elseif($method->card_type === 'bdo')
                                            <img src="https://i.pinimg.com/originals/17/a6/de/17a6de136da9aa796bca4bf04315a0a1.png" alt="BDO" class="h-12 w-14 rounded-lg bg-white object-cover shadow-sm ring-1 ring-gray-200">
                                        @else
                                            <i class="fas fa-credit-card text-2xl text-gray-600"></i>
                                        @endif
                                    @elseif($method->type === 'bank')
                                        <i class="fas fa-university text-2xl text-gray-600"></i>
                                    @else
                                        <i class="fas fa-money-check text-2xl text-gray-600"></i>
                                    @endif

                                    <div>
                                        <div class="font-medium text-gray-800">
                                            @if($method->type === 'card')
                                                @php
                                                    $providerLabel = match($method->card_type ?? '') {
                                                        'gcash' => 'GCash',
                                                        'maya' => 'Maya',
                                                        'bdo' => 'BDO',
                                                        default => ucfirst($method->card_type ?? 'Card'),
                                                    };
                                                @endphp
                                                {{ $providerLabel }}
                                            @else
                                                {{ $method->bank_name ?? 'Bank Account' }}
                                            @endif
                                        </div>
                                        <div class="text-xs text-gray-500">
                                            @if($method->type === 'card')
                                                •••• {{ $method->card_last_four ?? '0000' }}
                                            @else
                                                {{ $method->account_name ?? 'Account holder' }}
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                <div class="flex items-center gap-2">
                                    @if($method->is_default)
                                        <span class="rounded-full bg-green-100 px-2 py-1 text-[10px] font-semibold text-green-700">Default</span>
                                    @else
                                        <button type="button" onclick="setAsDefault({{ $method->id }})" class="text-xs font-medium text-orange-600 hover:text-orange-700">Set Default</button>
                                    @endif
                                    <button type="button" onclick="deletePaymentMethod({{ $method->id }})" class="text-xs font-medium text-red-600 hover:text-red-700">Delete</button>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

@include('profile.payment-method-modal')
@endsection
