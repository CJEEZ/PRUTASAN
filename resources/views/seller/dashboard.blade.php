@extends('layouts.app')

@section('hideHeader')@endsection
@section('content')
<div class="flex w-full flex-col gap-6 p-3 sm:p-4 lg:flex-row lg:gap-6 lg:p-6" style="min-height:70vh;">
    <aside class="hidden w-full shrink-0 rounded bg-white p-4 shadow lg:block lg:w-64">
        @include('seller._sidebar')
    </aside>

    <div class="flex-1">
        @include('seller._mobile_nav')

        <div class="mb-4 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div class="flex items-center gap-3">
                <img src="{{ asset('ORNOSFARM_LOGOS.png') }}" alt="Ornos Farm" class="h-10 w-auto object-contain sm:h-12 lg:h-16">
                <div>
                    <h1 class="text-xl font-bold sm:text-2xl">Seller Dashboard</h1>
                    <p class="text-xs text-gray-600 sm:text-sm">Overview of sales and product inventory.</p>
                </div>
            </div>
        </div>

        <!-- Wallet / Balance Overview -->
        <div class="mb-6 rounded bg-white p-4 shadow sm:p-6">
            <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <div class="text-sm text-gray-500">Wallet Balance</div>
                    <div class="text-2xl font-semibold sm:text-3xl">₱{{ number_format($totalSales, 2) }}</div>
                </div>
                <div class="flex flex-col gap-2 sm:gap-3">
                    <a href="#" class="min-h-[40px] rounded bg-orange-600 px-4 py-2 text-center text-sm font-semibold text-white transition hover:bg-orange-700 sm:px-6">Withdraw</a>
                    <div class="text-xs text-gray-500 sm:text-sm">My Bank Account<br>
                        @if($paymentMethods->isNotEmpty())
                            <span class="font-medium text-gray-800">
                                @php
                                    $defaultPayment = $paymentMethods->firstWhere('is_default', true) ?? $paymentMethods->first();
                                @endphp
                                @if($defaultPayment && $defaultPayment->type === 'card')
                                    {{ ucfirst($defaultPayment->card_type ?? 'Card') }} • {{ $defaultPayment->card_last_four ?? '••••' }}
                                @elseif($defaultPayment)
                                    {{ $defaultPayment->bank_name ?? 'Bank account' }}
                                @endif
                            </span>
                        @else
                            <span class="font-medium text-gray-800">Not linked</span>
                        @endif
                    </div>
                </div>
            </div>

            <div class="mt-6">
                <h3 class="mb-3 text-sm font-semibold text-gray-700">Recent Transactions</h3>
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm">
                        <thead class="text-gray-500">
                            <tr>
                                <th class="py-2">Date</th>
                                <th class="py-2">Type / Description</th>
                                <th class="py-2">Amount</th>
                                <th class="py-2">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentOrders as $order)
                            <tr class="border-t">
                                <td class="py-2">{{ $order->created_at->format('Y-m-d') }}</td>
                                <td class="py-2">Income from Order #{{ $order->order_number ?? $order->id }}</td>
                                <td class="py-2 text-green-600">+ ₱{{ number_format($order->total, 2) }}</td>
                                <td class="py-2">Completed</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="py-4 text-center text-gray-500">No transactions yet.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="seller-summary flex gap-2 overflow-x-auto pb-1 md:grid md:grid-cols-3 md:overflow-visible mb-6">
            <div class="min-w-[130px] shrink-0 rounded-lg bg-white p-3 shadow md:min-w-0">
                <div class="text-xs text-gray-500">Total Sales</div>
                <div class="mt-1 text-lg font-semibold">₱{{ number_format($totalSales, 2) }}</div>
            </div>
            <div class="min-w-[130px] shrink-0 rounded-lg bg-white p-3 shadow md:min-w-0">
                <div class="text-xs text-gray-500">Total Orders</div>
                <div class="mt-1 text-lg font-semibold">{{ $ordersCount }}</div>
            </div>
            <div class="min-w-[130px] shrink-0 rounded-lg bg-white p-3 shadow md:min-w-0">
                <div class="text-xs text-gray-500">Products</div>
                <div class="mt-1 text-lg font-semibold">{{ $inventory->count() }}</div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <section class="bg-white p-4 shadow rounded">
                <h2 class="font-semibold mb-3">Product Inventory</h2>
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm">
                        <thead class="text-gray-600">
                            <tr>
                                <th class="py-2">#</th>
                                <th class="py-2">Name</th>
                                <th class="py-2">Stock</th>
                                <th class="py-2">Price</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($inventory as $product)
                            <tr class="border-t">
                                <td class="py-2">{{ $product->id }}</td>
                                <td class="py-2">{{ $product->name }}</td>
                                <td class="py-2">{{ $product->stock }}</td>
                                <td class="py-2">₱{{ number_format($product->price, 2) }}</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="py-4 text-center text-gray-500">No products found.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </section>

            <section class="bg-white p-4 shadow rounded">
                <h2 class="font-semibold mb-3">Recent Orders</h2>
                @if($recentOrders->isEmpty())
                    <p class="text-gray-500">No recent orders.</p>
                @else
                    <ul>
                        @foreach($recentOrders as $order)
                        <li class="border-b py-2">
                            <div class="flex justify-between">
                                <div>
                                    <div class="font-medium">Order #{{ $order->order_number ?? $order->id }}</div>
                                    <div class="text-sm text-gray-600">Placed: {{ $order->created_at->format('Y-m-d H:i') }}</div>
                                </div>
                                <div class="text-right">
                                    <div class="font-semibold">₱{{ number_format($order->total, 2) }}</div>
                                    <div class="text-sm text-gray-600">{{ ucfirst($order->status) }}</div>
                                </div>
                            </div>
                        </li>
                        @endforeach
                    </ul>
                @endif
            </section>
        </div>
    </div>
</div>

@include('profile.payment-method-modal')
@endsection

