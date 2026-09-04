<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ ucfirst($section) }} | Driver Hub</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>body { font-family: 'Inter', sans-serif; }</style>
</head>
<body class="min-h-screen bg-gradient-to-br from-emerald-50 via-white to-orange-50 text-gray-900">
<main class="min-h-screen px-3 py-6 sm:px-6 lg:px-8">
    <div class="mx-auto max-w-5xl">
        <header class="mb-6 flex items-center justify-between gap-4">
            <div>
                <p class="text-xs font-semibold uppercase tracking-[0.2em] text-emerald-700">Driver Hub</p>
                <h1 class="mt-1 text-2xl font-bold text-gray-900 sm:text-3xl">{{ ucfirst($section) }}</h1>
                <p class="mt-1 text-sm text-gray-500">{{ $section === 'analytics' ? 'Track your delivery performance and earnings.' : ($section === 'schedule' ? 'Plan and review your upcoming delivery assignments.' : 'Review your completed delivery history.') }}</p>
            </div>
        </header>

        @if($section === 'analytics')
            @php
                $completed = $shipments->whereIn('status', ['delivered', 'completed'])->count();
                $active = $shipments->whereNotIn('status', ['delivered', 'completed', 'cancelled'])->count();
                $deliveredThisMonth = $shipments->whereIn('status', ['delivered', 'completed'])->filter(fn ($shipment) => $shipment->updated_at && $shipment->updated_at->isCurrentMonth())->count();
            @endphp
            <div class="grid gap-4 sm:grid-cols-3">
                <div class="rounded-2xl bg-white p-5 shadow-sm ring-1 ring-gray-100"><p class="text-sm text-gray-500">Completed deliveries</p><p class="mt-2 text-3xl font-bold text-emerald-700">{{ $completed }}</p><p class="mt-1 text-xs text-gray-400">All time</p></div>
                <div class="rounded-2xl bg-white p-5 shadow-sm ring-1 ring-gray-100"><p class="text-sm text-gray-500">Active assignments</p><p class="mt-2 text-3xl font-bold text-blue-700">{{ $active }}</p><p class="mt-1 text-xs text-gray-400">Currently assigned</p></div>
                <div class="rounded-2xl bg-white p-5 shadow-sm ring-1 ring-gray-100"><p class="text-sm text-gray-500">This month</p><p class="mt-2 text-3xl font-bold text-orange-700">{{ $deliveredThisMonth }}</p><p class="mt-1 text-xs text-gray-400">Completed trips</p></div>
            </div>
            <section class="mt-6 rounded-2xl bg-white p-5 shadow-sm ring-1 ring-gray-100 sm:p-6"><h2 class="text-lg font-bold">Performance overview</h2><div class="mt-5 space-y-4"><div><div class="mb-2 flex justify-between text-sm"><span class="text-gray-500">Completion rate</span><span class="font-semibold">{{ $shipments->count() ? round(($completed / $shipments->count()) * 100) : 0 }}%</span></div><div class="h-3 overflow-hidden rounded-full bg-gray-100"><div class="h-full rounded-full bg-emerald-600" style="width: {{ $shipments->count() ? round(($completed / $shipments->count()) * 100) : 0 }}%"></div></div></div><div class="rounded-xl bg-gray-50 p-4 text-sm text-gray-600"><i class="fas fa-chart-line mr-2 text-emerald-600"></i>Trip earnings will appear here when payout tracking is connected to completed shipments.</div></div></section>
        @elseif($section === 'schedule')
            <section class="rounded-2xl bg-white p-5 shadow-sm ring-1 ring-gray-100 sm:p-6"><div class="flex items-center justify-between"><div><h2 class="text-lg font-bold">Upcoming assignments</h2><p class="mt-1 text-sm text-gray-500">Shipments assigned to you that still need delivery.</p></div><span class="rounded-full bg-blue-50 px-3 py-1 text-xs font-bold text-blue-700">{{ $shipments->count() }} scheduled</span></div><div class="mt-5 space-y-3">@forelse($shipments as $shipment)<div class="flex flex-col gap-3 rounded-xl border border-gray-100 bg-gray-50 p-4 sm:flex-row sm:items-center sm:justify-between"><div><p class="font-semibold">Order #{{ $shipment->order->order_number ?? $shipment->order_id }}</p><p class="mt-1 text-sm text-gray-500">{{ $shipment->order->full_name ?? 'Customer' }}<span class="mx-1">to</span>{{ $shipment->order->city ?? 'Address pending' }}</p></div><div class="text-left sm:text-right"><span class="rounded-full bg-amber-100 px-2 py-1 text-xs font-semibold text-amber-800">{{ str_replace('_', ' ', $shipment->status) }}</span><p class="mt-2 text-xs text-gray-500">{{ $shipment->shipped_at ? $shipment->shipped_at->format('M d, Y g:i A') : 'Awaiting schedule' }}</p></div></div>@empty<div class="rounded-xl border border-dashed border-gray-200 bg-gray-50 px-5 py-12 text-center"><i class="fas fa-calendar-check text-3xl text-blue-600"></i><p class="mt-3 font-semibold">Your schedule is clear</p><p class="mt-1 text-sm text-gray-500">New assignments will appear here.</p></div>@endforelse</div></section>
        @else
            <section class="rounded-2xl bg-white p-5 shadow-sm ring-1 ring-gray-100 sm:p-6"><div class="flex items-center justify-between"><div><h2 class="text-lg font-bold">Completed deliveries</h2><p class="mt-1 text-sm text-gray-500">A record of your finished assignments.</p></div><span class="rounded-full bg-emerald-50 px-3 py-1 text-xs font-bold text-emerald-700">{{ $shipments->count() }} trips</span></div><div class="mt-5 overflow-x-auto">@forelse($shipments as $shipment)<div class="flex min-w-[420px] items-center justify-between border-b border-gray-100 py-4"><div><p class="font-semibold">Order #{{ $shipment->order->order_number ?? $shipment->order_id }}</p><p class="mt-1 text-sm text-gray-500">{{ $shipment->order->full_name ?? 'Customer' }}<span class="mx-1">on</span>{{ $shipment->updated_at->format('M d, Y') }}</p></div><span class="font-semibold text-emerald-700"><i class="fas fa-check-circle mr-1"></i>{{ ucfirst($shipment->status) }}</span></div>@empty<div class="rounded-xl border border-dashed border-gray-200 bg-gray-50 px-5 py-12 text-center"><i class="fas fa-clock-rotate-left text-3xl text-emerald-600"></i><p class="mt-3 font-semibold">No completed trips yet</p><p class="mt-1 text-sm text-gray-500">Your delivery history will build as you finish assignments.</p></div>@endforelse</div></section>
        @endif

        <nav class="mt-6 grid grid-cols-2 gap-2 rounded-2xl bg-white p-2 shadow-sm ring-1 ring-gray-100 sm:grid-cols-5"><a href="{{ route('driver.dashboard') }}" class="rounded-xl px-2 py-3 text-center text-xs font-semibold text-gray-500 hover:bg-gray-50"><i class="fas fa-truck mb-1 block text-base"></i>Deliveries</a><a href="{{ route('driver.analytics') }}" class="rounded-xl px-2 py-3 text-center text-xs font-semibold {{ $section === 'analytics' ? 'bg-emerald-50 text-emerald-700' : 'text-gray-500 hover:bg-gray-50' }}"><i class="fas fa-chart-column mb-1 block text-base"></i>Analytics</a><a href="{{ route('driver.schedule') }}" class="rounded-xl px-2 py-3 text-center text-xs font-semibold {{ $section === 'schedule' ? 'bg-emerald-50 text-emerald-700' : 'text-gray-500 hover:bg-gray-50' }}"><i class="fas fa-calendar mb-1 block text-base"></i>Schedule</a><a href="{{ route('driver.history') }}" class="rounded-xl px-2 py-3 text-center text-xs font-semibold {{ $section === 'history' ? 'bg-emerald-50 text-emerald-700' : 'text-gray-500 hover:bg-gray-50' }}"><i class="fas fa-clock-rotate-left mb-1 block text-base"></i>History</a><form method="POST" action="{{ route('logout') }}" class="contents">@csrf<button type="submit" class="min-h-[52px] rounded-xl px-2 py-3 text-center text-xs font-semibold text-gray-500 hover:bg-red-50 hover:text-red-600"><i class="fas fa-sign-out-alt mb-1 block text-base"></i>Logout</button></form></nav>
    </div>
</main>
</body>
</html>
