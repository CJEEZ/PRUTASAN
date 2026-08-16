@extends('layouts.admin')

@section('page_title', 'Messages')
@section('page_subtitle', 'Manage communications for customers, sellers, and platform updates')

@section('content')
<div class="space-y-6">
    <div class="bg-white rounded-xl shadow p-6">
        <div class="flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">
            <div>
                <p class="text-sm font-semibold uppercase tracking-[0.3em] text-emerald-600">Messages</p>
                <h2 class="mt-2 text-2xl font-bold text-gray-900">Send a message and review recent communications</h2>
                <p class="mt-2 text-sm text-gray-600">Use this area for platform updates, support issues, or policy changes.</p>
            </div>
            <div class="rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700">
                <p class="font-semibold">Fast follow-up</p>
                <p class="mt-1">Most requests are responded to within one business day</p>
            </div>
        </div>

        <div class="mt-6 grid gap-6 lg:grid-cols-[1.05fr_0.95fr]">
            <div class="rounded-2xl border border-gray-200 bg-gray-50 p-5">
                <h3 class="text-lg font-semibold text-gray-900">Send a message</h3>
                <form method="POST" action="{{ route('inquiries.store') }}" class="mt-4 space-y-4">
                    @csrf
                    <input type="hidden" name="redirect_to" value="{{ route('admin.messages') }}">
                    <input type="hidden" name="target_role" value="admin">
                    <div class="grid gap-4 sm:grid-cols-2">
                        <div>
                            <label class="mb-1 block text-sm font-medium text-gray-700">Category</label>
                            <select name="category" class="w-full rounded-xl border border-gray-200 bg-white px-3 py-2 text-sm focus:border-emerald-500 focus:outline-none">
                                <option value="announcement">Announcement</option>
                                <option value="policy">Policy update</option>
                                <option value="support">Support issue</option>
                            </select>
                        </div>
                        <div>
                            <label class="mb-1 block text-sm font-medium text-gray-700">Priority</label>
                            <select name="priority" class="w-full rounded-xl border border-gray-200 bg-white px-3 py-2 text-sm focus:border-emerald-500 focus:outline-none">
                                <option value="normal">Normal</option>
                                <option value="high">High</option>
                            </select>
                        </div>
                    </div>
                    <div>
                        <label class="mb-1 block text-sm font-medium text-gray-700">Subject</label>
                        <input type="text" name="subject" class="w-full rounded-xl border border-gray-200 bg-white px-3 py-2 text-sm focus:border-emerald-500 focus:outline-none" placeholder="Share a platform update or policy change">
                    </div>
                    <div>
                        <label class="mb-1 block text-sm font-medium text-gray-700">Message</label>
                        <textarea name="message" rows="4" class="w-full rounded-xl border border-gray-200 bg-white px-3 py-2 text-sm focus:border-emerald-500 focus:outline-none" placeholder="Describe the update or communication that should go out."></textarea>
                    </div>
                    <button class="rounded-xl bg-emerald-600 px-4 py-2 text-sm font-semibold text-white transition hover:bg-emerald-700">Send message</button>
                </form>
            </div>

            <div class="space-y-4">
                <div class="rounded-2xl border border-gray-200 bg-white p-5">
                    <h3 class="text-lg font-semibold text-gray-900">Recent messages</h3>
                    @php
                        $communicationInquiries = \App\Models\Inquiry::latest()->take(5)->get();
                    @endphp
                    @forelse($communicationInquiries as $item)
                        <div class="mt-3 rounded-xl border border-gray-200 bg-gray-50 p-3">
                            <p class="text-sm font-semibold text-gray-900">{{ $item->subject ?: 'Untitled update' }}</p>
                            <p class="mt-1 text-sm text-gray-600">{{ \Illuminate\Support\Str::limit($item->message, 90) }}</p>
                            <p class="mt-2 text-xs uppercase tracking-[0.2em] text-emerald-600">{{ ucfirst($item->category ?? 'general') }} • {{ ucfirst($item->status ?? 'new') }}</p>
                        </div>
                    @empty
                        <p class="mt-3 text-sm text-gray-600">No messages have been logged yet.</p>
                    @endforelse
                </div>

                <div class="rounded-2xl border border-gray-200 bg-white p-5">
                    <h3 class="text-lg font-semibold text-gray-900">Suggested topics</h3>
                    <ul class="mt-3 space-y-2 text-sm text-gray-600">
                        <li>• Customer product and delivery concerns</li>
                        <li>• Seller payout and policy questions</li>
                        <li>• Platform announcements and compliance updates</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
