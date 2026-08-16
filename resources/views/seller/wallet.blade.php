@extends('layouts.app')

@section('hideHeader')@endsection

@section('content')
<div class="flex w-full flex-col gap-6 p-3 sm:p-4 lg:flex-row lg:gap-6 lg:p-6" style="min-height:70vh;">
    <!-- Sidebar (hidden on small screens) -->
    <aside class="hidden lg:block w-64 rounded bg-white p-4 shadow">
        @include('seller._sidebar')
    </aside>

    <!-- Mobile nav -->
    <div class="lg:hidden w-full">
        @include('seller._mobile_nav')
    </div>

    <!-- Main content -->
    <div class="flex-1">
        <div class="space-y-6 rounded bg-white p-4 shadow sm:p-6">
            <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h2 class="text-lg font-semibold sm:text-xl">Balance Overview</h2>
                    <p class="text-xs text-gray-500 sm:text-sm">Wallet summary and transactions</p>
                </div>
                <div class="text-xs text-gray-500 sm:text-sm">
                    My Bank Account<br>
                    <span class="font-medium text-gray-800">{{ $bankAccountLinked ? 'Linked' : 'Not linked' }}</span>
                </div>
            </div>

            <div class="grid gap-4 lg:grid-cols-3">
                <div class="flex flex-col justify-between rounded-3xl border border-slate-200 bg-slate-50 p-4 shadow-sm sm:p-6">
                    <div>
                        <div class="text-sm text-gray-500">Wallet Balance</div>
                        <div class="mt-2 text-2xl font-semibold text-slate-900 sm:mt-3 sm:text-4xl">₱{{ number_format($totalSales ?? 0, 2) }}</div>
                        <div class="mt-1 text-xs text-gray-500 sm:mt-2 sm:text-sm">Your available seller wallet balance.</div>
                    </div>
                    <div class="mt-4 flex flex-col gap-2 sm:mt-6 sm:flex-row sm:items-center">
                        <form method="POST" action="{{ route('seller.withdraw') }}" class="w-full sm:w-auto">
                            @csrf
                            <button type="submit" class="min-h-[40px] w-full rounded-lg bg-orange-600 px-4 py-2 text-sm font-semibold text-white transition hover:bg-orange-700 sm:w-auto">Withdraw</button>
                        </form>
                        <a href="#" class="text-xs text-slate-500 hover:text-slate-700 sm:text-sm">More →</a>
                    </div>
                </div>

                <div class="rounded-3xl border border-slate-200 bg-slate-50 p-4 shadow-sm sm:p-6">
                    <div class="mb-3 flex items-center justify-between sm:mb-4">
                        <div>
                            <div class="text-sm text-gray-500">Monthly Sales Trend</div>
                            <div class="text-sm font-semibold text-slate-900 sm:text-base">Last 6 Months</div>
                        </div>
                        <span class="inline-flex items-center rounded-full bg-emerald-100 px-2 py-1 text-xs font-medium text-emerald-700 sm:px-3">Beta</span>
                    </div>
                    @php
                        $maxMonthly = max(array_column($monthlySales, 'amount')) ?: 1;
                    @endphp
                    <div class="grid grid-cols-6 gap-2 items-end h-32 sm:h-44">
                        @foreach($monthlySales as $item)
                            @php
                                $height = intval(($item['amount'] / $maxMonthly) * 100);
                                $height = max($height, 10);
                            @endphp
                            <div class="flex flex-col items-center gap-1">
                                <div class="relative w-full rounded-2xl bg-slate-200 overflow-hidden" style="height: 80px;">
                                    <div class="absolute bottom-0 left-0 w-full bg-emerald-500 rounded-t-2xl" style="height: {{ $height }}%;"></div>
                                </div>
                                <div class="text-xs text-gray-500">{{ $item['label'] }}</div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <div class="rounded-3xl border border-slate-200 bg-white p-4 shadow-sm sm:p-6">
                    <div class="flex gap-2 sm:gap-3">
                        <div>
                            <div class="text-sm text-gray-500">My Bank Account</div>
                            <div class="mt-2 text-base font-semibold text-slate-900 sm:mt-3 sm:text-lg">{{ $bankAccountLinked ? 'Linked' : 'Not linked' }}</div>
                            <div class="mt-1 text-xs text-gray-500 sm:mt-2 sm:text-sm">Connect your payout account to withdraw funds faster.</div>
                        </div>
                    </div>
                    <div class="mt-6 grid gap-3">
                        <div class="rounded-2xl bg-slate-50 p-4 border border-slate-200">
                            <div class="text-xs font-semibold uppercase text-gray-500">Account status</div>
                            <div class="mt-2 text-sm text-slate-900">{{ $bankAccountLinked ? 'Connected' : 'Not linked' }}</div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div class="bg-white border border-slate-200 rounded-3xl p-5 shadow-sm">
                    <div class="text-sm text-gray-500">Pending Orders</div>
                    <div class="mt-3 text-3xl font-semibold text-slate-900">{{ $pendingOrdersCount ?? 0 }}</div>
                    <div class="mt-2 text-sm text-gray-500">Orders waiting for processing.</div>
                </div>
                <div class="bg-white border border-slate-200 rounded-3xl p-5 shadow-sm">
                    <div class="text-sm text-gray-500">Low Stock Items</div>
                    <div class="mt-3 text-3xl font-semibold text-slate-900">{{ $lowStockItems ?? 0 }}</div>
                    <div class="mt-2 text-sm text-gray-500">Products with stock below 5 units.</div>
                </div>
                <div class="bg-white border border-slate-200 rounded-3xl p-5 shadow-sm">
                    <div class="text-sm text-gray-500">Total Revenue (Last 30 Days)</div>
                    <div class="mt-3 text-3xl font-semibold text-slate-900">₱{{ number_format($totalRevenueLast30Days ?? 0, 2) }}</div>
                    <div class="mt-2 text-sm text-gray-500">Recent payout-eligible sales.</div>
                </div>
            </div>

            <div>
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <h3 class="text-sm font-semibold text-slate-900">Recent Transactions</h3>
                        <p class="text-sm text-gray-500">Latest seller orders and payment activity.</p>
                    </div>
                </div>
                <div class="overflow-x-auto rounded-3xl border border-slate-200 bg-white shadow-sm">
                    <table class="min-w-full divide-y divide-slate-200 text-sm">
                        <thead class="bg-slate-50 text-slate-600">
                            <tr>
                                <th class="px-4 py-3 text-left font-medium">Date</th>
                                <th class="px-4 py-3 text-left font-medium">Type / Description</th>
                                <th class="px-4 py-3 text-left font-medium">Amount</th>
                                <th class="px-4 py-3 text-left font-medium">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-200">
                            @forelse($recentOrders as $order)
                                <tr>
                                    <td class="px-4 py-4">{{ $order->created_at->format('Y-m-d') }}</td>
                                    <td class="px-4 py-4">Order Payment #{{ $order->order_number ?? $order->id }}</td>
                                    <td class="px-4 py-4 text-emerald-600 font-semibold">+ ₱{{ number_format($order->total, 2) }}</td>
                                    <td class="px-4 py-4">
                                        <span class="inline-flex items-center rounded-full bg-emerald-100 px-3 py-1 text-xs font-semibold text-emerald-700">{{ ucfirst($order->status) }}</span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-4 py-10 text-center text-gray-500">No transactions yet.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
