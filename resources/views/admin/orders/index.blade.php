@extends('layouts.app')

@section('content')
<div class="mx-auto min-w-0 p-1.5 sm:p-4 lg:p-6">
    <div class="mb-4 flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
        <h1 class="text-lg font-semibold sm:text-2xl">Order Management</h1>
        <div class="flex items-center gap-2">
            <a href="{{ route('admin.dashboard') }}" class="min-h-[36px] rounded bg-orange-600 px-2.5 py-1.5 text-xs text-white transition hover:bg-orange-700 sm:min-h-[40px] sm:px-4 sm:py-2 sm:text-sm">Dashboard</a>
        </div>
    </div>

    <form method="GET" action="{{ route('admin.orders.index') }}" class="mb-3 min-w-0 space-y-1.5 rounded bg-white p-1.5 shadow sm:mb-4 sm:space-y-2 sm:p-4">
        <div class="flex min-w-0 flex-col gap-2 sm:flex-row sm:items-center">
            <input type="text" name="q" value="{{ request('q') }}" placeholder="Search order..." class="min-h-[36px] w-full min-w-0 flex-1 rounded border border-gray-300 px-2.5 py-1.5 text-xs sm:min-h-[40px] sm:w-auto sm:px-3 sm:py-2 sm:text-sm" />
            <select name="status" class="min-h-[36px] w-full rounded border border-gray-300 px-2.5 py-1.5 text-xs sm:min-h-[40px] sm:w-auto sm:px-3 sm:py-2 sm:text-sm">
                <option value="">All statuses</option>
                @foreach(['pending','confirmed','shipped','delivered','cancelled','return_requested'] as $s)
                    <option value="{{ $s }}" {{ request('status') === $s ? 'selected' : '' }}>{{ ucfirst(str_replace('_', ' ', $s)) }}</option>
                @endforeach
            </select>
            <select name="payment_status" class="min-h-[36px] w-full rounded border border-gray-300 px-2.5 py-1.5 text-xs sm:min-h-[40px] sm:w-auto sm:px-3 sm:py-2 sm:text-sm">
                <option value="">Any payment</option>
                @foreach(['pending','paid','failed'] as $p)
                    <option value="{{ $p }}" {{ request('payment_status') === $p ? 'selected' : '' }}>{{ ucfirst($p) }}</option>
                @endforeach
            </select>
            <button class="min-h-[36px] w-full rounded bg-green-600 px-2.5 py-1.5 text-xs font-medium text-white transition hover:bg-green-700 sm:min-h-[40px] sm:w-auto sm:px-4 sm:py-2 sm:text-sm">Filter</button>
            <a href="{{ route('admin.orders.index') }}" class="text-xs text-gray-600 hover:text-gray-800 sm:text-sm">Reset</a>
        </div>
    </form>

    <form method="GET" action="{{ route('admin.orders.index') }}" id="bulkExportForm" class="min-w-0 overflow-x-auto overscroll-x-contain rounded bg-white shadow">
        <input type="hidden" name="export" value="1" />
        <table class="w-full min-w-[360px] divide-y divide-gray-200 text-xs sm:min-w-0 sm:text-sm">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-1.5 py-2 sm:px-4 sm:py-3"><input type="checkbox" id="select_all" /></th>
                    <th class="hidden px-2 py-2 text-left sm:table-cell sm:px-4 sm:py-3 text-xs font-medium text-gray-600 uppercase">#</th>
                        <th class="px-1 py-1.5 text-left text-[9px] font-medium uppercase text-gray-600 sm:px-4 sm:py-3 sm:text-xs">Customer</th>
                            <th class="px-1 py-1.5 text-left text-[9px] font-medium uppercase text-gray-600 sm:px-4 sm:py-3 sm:text-xs">Total</th>
                    <th class="hidden px-2 py-2 text-left sm:table-cell sm:px-4 sm:py-3 text-xs font-medium text-gray-600 uppercase">Status</th>
                    <th class="hidden px-2 py-2 text-left lg:table-cell lg:px-4 lg:py-3 text-xs font-medium text-gray-600 uppercase">Payment</th>
                    <th class="hidden px-2 py-2 text-left lg:table-cell lg:px-4 lg:py-3 text-xs font-medium text-gray-600 uppercase">Placed</th>
                    <th class="px-1 py-1.5 text-left text-[9px] font-medium uppercase text-gray-600 sm:px-4 sm:py-3 sm:text-xs">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @foreach($orders as $order)
                    <tr class="hover:bg-gray-50">
                        <td class="px-1.5 py-2 text-center sm:px-4 sm:py-3"><input type="checkbox" name="selected[]" value="{{ $order->id }}" class="select_box" /></td>
                        <td class="hidden px-2 py-2 sm:table-cell sm:px-4 sm:py-3 text-xs">{{ $order->id }}<br/><small class="text-gray-500">{{ $order->order_number ?? '' }}</small></td>
                        <td class="max-w-[120px] truncate px-1.5 py-2 text-xs sm:max-w-none sm:px-4 sm:py-3">{{ optional($order->user)->name ?? 'Guest' }}<br/><small class="text-gray-500">{{ optional($order->user)->email }}</small></td>
                        <td class="whitespace-nowrap px-1.5 py-2 text-xs font-semibold text-orange-600 sm:px-4 sm:py-3">₱{{ number_format($order->total ?? $order->total_amount ?? 0, 2) }}</td>
                        <td class="hidden px-2 py-2 sm:table-cell sm:px-4 sm:py-3 text-xs">{{ ucfirst($order->status) }}</td>
                        <td class="hidden px-2 py-2 text-xs lg:table-cell lg:px-4 lg:py-3">{{ ucfirst($order->payment_status ?? 'pending') }}</td>
                        <td class="hidden px-2 py-2 text-xs lg:table-cell lg:px-4 lg:py-3">{{ $order->created_at->toDateString() }}</td>
                        <td class="space-y-1 whitespace-nowrap px-1.5 py-2 sm:space-y-0 sm:space-x-2 sm:px-4 sm:py-3">
                            <a href="{{ route('admin.orders.show', $order->id) }}" class="text-xs text-blue-600 hover:text-blue-800 sm:text-sm">View</a>

                            <form method="POST" action="{{ route('admin.orders.update', $order->id) }}" class="inline-block">
                                @csrf
                                @method('PATCH')
                                <select name="status" class="min-h-[32px] max-w-[92px] rounded border border-gray-300 px-1 py-0.5 text-[11px] sm:max-w-none sm:px-2 sm:py-1 sm:text-sm">
                                    @foreach(['pending','confirmed','shipped','delivered','cancelled','return_requested'] as $s)
                                        <option value="{{ $s }}" {{ $order->status === $s ? 'selected' : '' }}>{{ ucfirst(str_replace('_', ' ', $s)) }}</option>
                                    @endforeach
                                </select>
                                <button class="ml-1 rounded bg-indigo-600 px-2 py-0.5 text-xs text-white hover:bg-indigo-700 sm:px-3 sm:py-1 sm:text-sm">Update</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
            </div>
            <div>
                {{ $orders->links() }}
            </div>
        </div>
    </form>
</div>

<script>
document.getElementById('select_all')?.addEventListener('change', function(e){
    document.querySelectorAll('.select_box').forEach(cb => cb.checked = e.target.checked);
});
</script>

@endsection
