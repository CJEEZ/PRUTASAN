@extends('layouts.app')

@section('hideHeader')@endsection

@section('content')
<div class="flex w-full min-w-0 gap-3 p-3 sm:gap-6 sm:p-6" style="min-height:70vh;">
    <aside class="hidden md:block w-64 bg-white rounded shadow p-4">
        @include('seller._sidebar')
    </aside>
    <div class="flex-1">
        <div class="w-full">
            @include('seller._mobile_nav')
        </div>

        <div class="rounded bg-white p-3 shadow sm:p-6">
            <h2 class="mb-3 text-lg font-semibold sm:mb-4 sm:text-xl">Bank Accounts</h2>
            <p class="text-sm text-gray-600 sm:text-base">Link and manage your payout account.</p>

            <div class="mt-4 rounded-xl border border-gray-200 bg-gray-50 p-3 sm:mt-6 sm:p-4">
                <div class="flex items-center justify-between gap-3">
                    <div class="min-w-0">
                        <h3 class="text-xs font-semibold text-gray-700 sm:text-sm">Saved Bank & Cards</h3>
                        <p class="text-xs text-gray-500">Manage your payout account</p>
                    </div>
                    <button type="button" onclick="showAddPaymentMethodModal()" class="inline-flex shrink-0 items-center justify-center rounded-lg bg-emerald-600 px-2.5 py-1.5 text-[11px] font-semibold text-white hover:bg-emerald-700 sm:px-3 sm:py-2 sm:text-xs">
                        <i class="fas fa-plus mr-1"></i> Add
                    </button>
                </div>

                @if(($paymentMethods ?? collect())->isEmpty())
                    <div class="mt-3 rounded-lg border border-dashed border-gray-300 bg-white p-3 text-xs text-gray-500 sm:mt-4 sm:p-4 sm:text-sm">
                        No payment method saved yet.
                    </div>
                @else
                    <div class="mt-4 space-y-3">
                        @foreach($paymentMethods ?? [] as $method)
                            <div class="flex items-center justify-between gap-2 rounded-lg border border-gray-200 bg-white p-2 sm:gap-3 sm:p-3">
                                <div class="flex min-w-0 items-center gap-2 sm:gap-3">
                                    @if($method->type === 'card')
                                        @if($method->card_type === 'gcash')
                                            <img src="https://www.thefastmode.com/media/k2/items/src/03160998318f697230a7e611fb0fa87d.jpg?t=20200629_013741" alt="GCash" class="h-9 w-11 rounded-lg bg-white object-cover shadow-sm ring-1 ring-gray-200 sm:h-12 sm:w-14">
                                        @elseif($method->card_type === 'maya')
                                            <img src="https://cdn.manilastandard.net/wp-content/uploads/2025/02/maya-logo-black.png" alt="Maya" class="h-9 w-11 rounded-lg bg-white object-cover shadow-sm ring-1 ring-gray-200 sm:h-12 sm:w-14">
                                        @elseif($method->card_type === 'bdo')
                                            <img src="https://i.pinimg.com/originals/17/a6/de/17a6de136da9aa796bca4bf04315a0a1.png" alt="BDO" class="h-9 w-11 rounded-lg bg-white object-cover shadow-sm ring-1 ring-gray-200 sm:h-12 sm:w-14">
                                        @else
                                            <i class="fas fa-credit-card text-xl text-gray-600 sm:text-2xl"></i>
                                        @endif
                                    @elseif($method->type === 'bank')
                                        <i class="fas fa-university text-xl text-gray-600 sm:text-2xl"></i>
                                    @else
                                        <i class="fas fa-money-check text-xl text-gray-600 sm:text-2xl"></i>
                                    @endif

                                    <div class="min-w-0">
                                        <div class="truncate text-sm font-medium text-gray-800 sm:text-base">
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

                                <div class="flex shrink-0 items-center gap-1 sm:gap-2">
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
