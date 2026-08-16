<!-- Messages Section in Profile Show -->
<div id="messages" class="mt-8 space-y-6">
    <div class="rounded-2xl border border-emerald-100 bg-emerald-50/70 p-6 shadow-sm">
        <div class="flex flex-col gap-3 md:flex-row md:items-start md:justify-between">
            <div>
                <p class="text-sm font-semibold uppercase tracking-[0.3em] text-emerald-700">Messages</p>
                <h3 class="mt-2 text-xl font-semibold text-gray-900">Contact support and track your conversations</h3>
                <p class="mt-2 text-sm text-gray-600">Send questions about fruit quality, delivery, returns, or bulk orders here.</p>
            </div>
            <div class="rounded-xl border border-emerald-200 bg-white px-4 py-3 text-sm text-gray-700 shadow-sm">
                <p class="font-semibold text-emerald-700">Fast response</p>
                <p class="mt-1">Most requests are replied to within one business day.</p>
            </div>
        </div>

        <form method="POST" action="{{ route('inquiries.store') }}" class="mt-6 space-y-4 rounded-2xl border border-emerald-100 bg-white p-5 shadow-sm">
            @csrf
            <input type="hidden" name="redirect_to" value="{{ route('profile.notifications') }}">
            <input type="hidden" name="target_role" value="admin">
            <div class="grid gap-4 md:grid-cols-2">
                <div>
                    <label class="mb-1 block text-sm font-medium text-gray-700">Category</label>
                    <select name="category" class="w-full rounded-xl border border-gray-200 px-3 py-2 text-sm focus:border-emerald-500 focus:outline-none">
                        <option value="product">Product question</option>
                        <option value="delivery">Delivery update</option>
                        <option value="complaint">Concern or return</option>
                        <option value="bulk">Bulk order inquiry</option>
                    </select>
                </div>
                <div>
                    <label class="mb-1 block text-sm font-medium text-gray-700">Priority</label>
                    <select name="priority" class="w-full rounded-xl border border-gray-200 px-3 py-2 text-sm focus:border-emerald-500 focus:outline-none">
                        <option value="normal">Normal</option>
                        <option value="high">High</option>
                    </select>
                </div>
            </div>
            <div>
                <label class="mb-1 block text-sm font-medium text-gray-700">Subject</label>
                <input type="text" name="subject" class="w-full rounded-xl border border-gray-200 px-3 py-2 text-sm focus:border-emerald-500 focus:outline-none" placeholder="Ask about ripeness, delivery timing, or product availability">
            </div>
            <div>
                <label class="mb-1 block text-sm font-medium text-gray-700">Message</label>
                <textarea name="message" rows="4" class="w-full rounded-xl border border-gray-200 px-3 py-2 text-sm focus:border-emerald-500 focus:outline-none" placeholder="Tell us what you need help with."></textarea>
            </div>
            <button class="rounded-xl bg-emerald-600 px-4 py-2 text-sm font-semibold text-white transition hover:bg-emerald-700">Send message</button>
        </form>
    </div>

    <div class="rounded-2xl border border-gray-200 bg-white shadow-sm overflow-hidden">
        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200 bg-gray-50">
            <h3 class="text-lg font-bold text-gray-800">Recent message history</h3>
            <span class="text-sm text-gray-500">{{ $communicationMessages->count() }} messages</span>
        </div>

        <div class="divide-y divide-gray-200">
            @if($communicationMessages->isEmpty())
                <div class="p-12 text-center">
                    <i class="fas fa-comment-dots text-5xl text-gray-300 mb-4"></i>
                    <p class="text-gray-500 text-lg">No messages yet</p>
                </div>
            @else
                @foreach($communicationMessages as $message)
                    <div class="p-4 hover:bg-gray-50 transition border-l-4 @if(($message->status ?? 'new') === 'new') border-emerald-600 bg-emerald-50/40 @else border-transparent @endif">
                        <div class="flex items-start justify-between gap-4">
                            <div>
                                <h4 class="font-semibold text-gray-800">{{ $message->subject ?: 'Message sent' }}</h4>
                                <p class="mt-1 text-sm text-gray-600">{{ $message->message }}</p>
                                <p class="mt-2 text-xs uppercase tracking-[0.2em] text-emerald-600">{{ ucfirst($message->category ?? 'general') }} • {{ ucfirst($message->status ?? 'new') }}</p>
                            </div>
                            <span class="text-xs text-gray-500">{{ $message->created_at->diffForHumans() }}</span>
                        </div>
                    </div>
                @endforeach
            @endif
        </div>
    </div>
</div>

<script>
function filterNotifications(type) {
    // Update active filter button
    document.querySelectorAll('.notification-filter').forEach(btn => {
        btn.classList.remove('border-orange-600', 'text-orange-600', 'bg-orange-50');
        btn.classList.add('border-transparent', 'text-gray-700');
    });
    document.querySelector(`.notification-filter[data-type="${type}"]`).classList.add('border-orange-600', 'text-orange-600');

    // Filter notifications
    document.querySelectorAll('.notification-item').forEach(item => {
        const itemType = item.getAttribute('data-type');
        if (type === 'all' || itemType === type) {
            item.style.display = 'block';
        } else {
            item.style.display = 'none';
        }
    });
}

function markNotificationAsRead(notificationId) {
    fetch(`/notifications/${notificationId}/read`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
        },
    }).then(response => {
        if (response.ok) {
            location.reload();
        }
    });
}

function markAllAsRead() {
    fetch('/notifications/mark-all-as-read', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
        },
    }).then(response => {
        if (response.ok) {
            location.reload();
        }
    });
}
</script>
