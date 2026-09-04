@extends('layouts.admin')

@section('page_title', 'Driver Applications')
@section('page_subtitle', 'Review documents and hire delivery drivers')
@section('content')
<div class="space-y-5">
    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div><h1 class="text-2xl font-bold text-gray-900">Driver applications</h1><p class="text-sm text-gray-500">Review license, OR, and CR documents before approving a driver.</p></div>
        <div class="flex flex-wrap gap-2 text-sm">
            @foreach(['all' => 'All', 'pending' => 'Pending', 'approved' => 'Approved', 'hired' => 'Hired', 'rejected' => 'Rejected'] as $filter => $label)
                <a href="{{ $filter === 'all' ? route('admin.drivers.index') : route('admin.drivers.index', ['status' => $filter]) }}" class="rounded-lg px-3 py-2 ring-1 ring-gray-200 {{ request('status', 'all') === $filter ? 'bg-emerald-100 text-emerald-800' : 'bg-white text-gray-700 hover:bg-gray-50' }}">{{ $label }}</a>
            @endforeach
        </div>
    </div>

    <div class="space-y-4">
        @forelse($applications as $application)
            <article class="p-4 bg-white shadow-sm rounded-2xl ring-1 ring-gray-100 sm:p-6">
                <div class="flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">
                    <div><h2 class="text-lg font-bold text-gray-900">{{ $application->user->name }}</h2><p class="text-sm text-gray-500">{{ $application->user->email }} · Submitted {{ $application->created_at->format('M d, Y') }}</p><p class="mt-2 text-sm"><span class="font-semibold">License serial:</span> {{ $application->license_serial_number }}</p></div>
                    <span class="w-fit rounded-full px-3 py-1 text-xs font-bold {{ $application->status === 'hired' ? 'bg-blue-100 text-blue-800' : ($application->status === 'approved' ? 'bg-emerald-100 text-emerald-800' : ($application->status === 'rejected' ? 'bg-red-100 text-red-800' : 'bg-amber-100 text-amber-800')) }}">{{ ucfirst($application->status) }}</span>
                </div>
                <div class="grid grid-cols-1 gap-3 mt-5 sm:grid-cols-3 sm:gap-4">
                    @foreach([['license_photo_path', 'License'], ['or_photo_path', 'OR'], ['cr_photo_path', 'CR']] as [$path, $label])
                        <a href="{{ Storage::disk('public')->url($application->{$path}) }}" target="_blank" class="overflow-hidden border border-gray-200 group rounded-xl bg-gray-50"><img src="{{ Storage::disk('public')->url($application->{$path}) }}" alt="{{ $label }} document" class="object-cover w-full transition h-28 group-hover:scale-105 sm:h-40"><span class="block px-2 py-2 text-xs font-semibold text-center text-gray-600">{{ $label }} · View</span></a>
                    @endforeach
                </div>
                <div class="flex flex-col gap-3 pt-4 mt-5 border-t border-gray-100 sm:flex-row sm:items-end sm:justify-between">
                    @if($application->status !== 'hired')
                    <form method="POST" action="{{ route('admin.drivers.update', $application) }}" class="flex flex-col flex-1 gap-2 sm:flex-row">
                        @csrf @method('PATCH')
                        <input type="hidden" name="status" value="rejected">
                        <input name="rejection_reason" placeholder="Reason if rejecting (optional)" class="min-h-[42px] flex-1 rounded-lg border border-gray-200 px-3 text-sm">
                        <button class="min-h-[42px] rounded-lg bg-red-600 px-4 text-sm font-semibold text-white hover:bg-red-700">Reject</button>
                    </form>
                    @endif
                    @if($application->status === 'hired')
                        <button type="button" disabled class="min-h-[42px] w-full cursor-not-allowed rounded-lg bg-blue-100 px-5 text-sm font-semibold text-blue-800 sm:w-auto"><i class="mr-1 fas fa-check-double"></i> Hired</button>
                    @else
                        <form method="POST" action="{{ route('admin.drivers.update', $application) }}">@csrf @method('PATCH')<input type="hidden" name="status" value="hired"><button class="min-h-[42px] w-full rounded-lg bg-blue-700 px-5 text-sm font-semibold text-white hover:bg-blue-800 sm:w-auto"><i class="mr-1 fas fa-user-check"></i> Hire driver</button></form>
                    @endif
                </div>
            </article>
        @empty
            <div class="p-10 text-center text-gray-500 bg-white shadow-sm rounded-2xl">No driver applications found.</div>
        @endforelse
    </div>
    <div>{{ $applications->links() }}</div>
</div>
@endsection
