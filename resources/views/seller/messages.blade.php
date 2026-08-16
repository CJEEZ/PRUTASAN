@extends('layouts.app')

@section('hideHeader')@endsection

@section('content')
<div class="min-h-screen bg-[#07170f] text-slate-100">
    <div class="flex flex-col xl:flex-row gap-6 p-6">
        <aside class="hidden xl:block w-80 rounded-[2rem] bg-[#0d2f1b]/90 border border-slate-800 shadow-2xl p-6 backdrop-blur-xl">
            @include('seller._sidebar')
        </aside>

        <div class="flex-1 space-y-6">
            <div class="rounded-[2rem] border border-slate-800 bg-[#102c1f] p-6 shadow-2xl">
                <div class="flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">
                    <div>
                        <p class="text-sm uppercase tracking-[0.3em] text-emerald-300">Messages</p>
                        <h1 class="mt-3 text-3xl font-semibold text-white">Send a message and keep your conversation history in one place</h1>
                        <p class="mt-2 text-sm text-slate-400">Use this section for stock, payout, policy, or support questions.</p>
                    </div>
                    <div class="rounded-2xl border border-emerald-500/20 bg-emerald-500/10 px-4 py-3 text-sm text-emerald-200">
                        <p class="font-semibold">Fast response</p>
                        <p class="mt-1">Most requests are answered within one business day</p>
                    </div>
                </div>

                <div class="mt-6 grid gap-6 lg:grid-cols-[1.1fr_0.9fr]">
                    <div class="rounded-3xl border border-slate-800 bg-[#0f291f] p-5">
                        <h2 class="text-lg font-semibold text-white">Send a message</h2>
                        <form method="POST" action="{{ route('inquiries.store') }}" class="mt-4 space-y-4">
                            @csrf
                            <input type="hidden" name="redirect_to" value="{{ route('seller.messages') }}">
                            <input type="hidden" name="target_role" value="seller">
                            <div class="grid gap-4 sm:grid-cols-2">
                                <div>
                                    <label class="mb-1 block text-sm text-slate-300">Category</label>
                                    <select name="category" class="w-full rounded-2xl border border-slate-700 bg-[#122f24] px-3 py-2 text-sm text-white focus:border-emerald-500 focus:outline-none">
                                        <option value="inventory">Inventory</option>
                                        <option value="payout">Payout</option>
                                        <option value="policy">Policy</option>
                                        <option value="support">Support</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="mb-1 block text-sm text-slate-300">Priority</label>
                                    <select name="priority" class="w-full rounded-2xl border border-slate-700 bg-[#122f24] px-3 py-2 text-sm text-white focus:border-emerald-500 focus:outline-none">
                                        <option value="normal">Normal</option>
                                        <option value="high">High</option>
                                    </select>
                                </div>
                            </div>
                            <div>
                                <label class="mb-1 block text-sm text-slate-300">Subject</label>
                                <input type="text" name="subject" class="w-full rounded-2xl border border-slate-700 bg-[#122f24] px-3 py-2 text-sm text-white focus:border-emerald-500 focus:outline-none" placeholder="Share a concern about stock, payout, or listing approval">
                            </div>
                            <div>
                                <label class="mb-1 block text-sm text-slate-300">Message</label>
                                <textarea name="message" rows="4" class="w-full rounded-2xl border border-slate-700 bg-[#122f24] px-3 py-2 text-sm text-white focus:border-emerald-500 focus:outline-none" placeholder="Explain the issue and the help you need."></textarea>
                            </div>
                            <button class="rounded-2xl bg-emerald-600 px-4 py-2 text-sm font-semibold text-white transition hover:bg-emerald-700">Send message</button>
                        </form>
                    </div>

                    <div class="space-y-4">
                        <div class="rounded-3xl border border-slate-800 bg-[#122f25] p-5">
                            <h3 class="text-lg font-semibold text-white">Recent messages</h3>
                            @php
                                $communicationTickets = \App\Models\Inquiry::where(function ($query) {
                                    $query->where('user_id', auth()->id())
                                        ->orWhere('email', auth()->user()->email)
                                        ->orWhere('target_role', 'seller');
                                })->latest()->take(4)->get();
                            @endphp

                            @forelse($communicationTickets as $ticket)
                                <div class="mt-3 rounded-2xl border border-slate-700 bg-[#0f291f] p-3">
                                    <p class="text-sm font-semibold text-white">{{ $ticket->subject ?: 'Support request' }}</p>
                                    <p class="mt-1 text-sm text-slate-400">{{ \Illuminate\Support\Str::limit($ticket->message, 90) }}</p>
                                    <p class="mt-2 text-xs uppercase tracking-[0.2em] text-emerald-300">{{ ucfirst($ticket->category ?? 'general') }} • {{ ucfirst($ticket->status ?? 'new') }}</p>
                                </div>
                            @empty
                                <p class="mt-3 text-sm text-slate-400">No recent messages yet.</p>
                            @endforelse
                        </div>

                        <div class="rounded-3xl border border-slate-800 bg-[#122f25] p-5">
                            <h3 class="text-lg font-semibold text-white">Suggested topics</h3>
                            <ul class="mt-3 space-y-2 text-sm text-slate-400">
                                <li>• Stock or inventory updates</li>
                                <li>• Payout or settlement questions</li>
                                <li>• Listing or policy guidance</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
