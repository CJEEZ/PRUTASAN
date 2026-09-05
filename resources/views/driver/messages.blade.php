<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Driver Messages | {{ config('app.name', 'FruitExpress') }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>body { font-family: 'Inter', sans-serif; }</style>
</head>
<body class="min-h-screen bg-gradient-to-br from-emerald-50 via-white to-orange-50 text-gray-900">
@php
    $driver = auth()->user();
    $driverName = $driver?->name ?? 'Driver';
    $profileUsers = \App\Models\User::query()
        ->where('id', '!=', $driver?->id)
        ->whereNotNull('name')
        ->orderByRaw("CASE LOWER(COALESCE(role, ''))
            WHEN 'admin' THEN 1
            WHEN 'seller' THEN 2
            WHEN 'driver' THEN 3
            WHEN 'customer' THEN 4
            WHEN 'user' THEN 5
            ELSE 99
        END ASC")
        ->orderBy('name')
        ->limit(10)
        ->get();

    // Get conversations where the driver is one of the participants
    $conversations = \App\Models\Conversation::query()
        ->where(function ($query) use ($driver) {
            if ($driver) {
                $query->where('user_id_1', $driver->id)
                    ->orWhere('user_id_2', $driver->id)
                    ->orWhereExists(function ($subQuery) use ($driver) {
                        $subQuery->from('inquiries')
                            ->whereColumn('inquiries.conversation_id', 'conversations.id')
                            ->where(function ($messageQuery) use ($driver) {
                                $messageQuery->where('inquiries.sender_id', $driver?->id)
                                    ->orWhere('inquiries.recipient_id', $driver?->id);
                            });
                    });
            }

            $allowedDriverRoles = ['driver', 'admin'];

            $query->orWhere(function ($roleQuery) use ($driver, $allowedDriverRoles) {
                $roleQuery->whereNull('user_id_1')
                    ->whereNull('user_id_2')
                    ->whereRaw('LOWER(target_role) IN (?, ?)', $allowedDriverRoles)
                    ->whereExists(function ($subQuery) use ($driver) {
                        $subQuery->from('inquiries')
                            ->whereColumn('inquiries.conversation_id', 'conversations.id')
                            ->where(function ($messageQuery) use ($driver) {
                                $messageQuery->where('inquiries.sender_id', $driver?->id)
                                    ->orWhere('inquiries.recipient_id', $driver?->id);
                            });
                    });
            });
        })
        ->orderByDesc('last_message_at')
        ->get();

    $conversationActivityByUserId = [];

    foreach ($conversations as $conversation) {
        foreach ([$conversation->user_id_1, $conversation->user_id_2] as $participantId) {
            if (!$participantId) {
                continue;
            }

            $timestamp = $conversation->last_message_at ?? now();
            if (!isset($conversationActivityByUserId[$participantId]) || $timestamp > $conversationActivityByUserId[$participantId]) {
                $conversationActivityByUserId[$participantId] = $timestamp;
            }
        }
    }

    $contactMap = [];

    foreach ($conversations as $conversation) {
        $peer = null;
        $peerEmail = null;

        // Determine the conversation partner
        if ($driver && $conversation->user_id_1 === $driver->id) {
            $peer = $conversation->user2;
            $peerEmail = $conversation->user2?->email;
        } elseif ($driver && $conversation->user_id_2 === $driver->id) {
            $peer = $conversation->user1;
            $peerEmail = $conversation->user1?->email;
        }

        // Fallback: create a generic peer object
        if (!$peer) {
            $peer = (object) [
                'id' => $conversation->user_id_2 ?? $conversation->user_id_1 ?? 0,
                'name' => $conversation->subject ?: 'Support team',
                'email' => $peerEmail ?: 'support@fruitexpress.com',
                'role' => ucfirst($conversation->target_role ?: 'support'),
            ];
        }

        $peerRole = strtolower($peer->role ?? '');
        $conversationRole = strtolower($conversation->target_role ?? '');
        $allowedDriverRoles = ['driver', 'admin'];

        if (!in_array($peerRole, $allowedDriverRoles, true) && !in_array($conversationRole, $allowedDriverRoles, true)) {
            continue;
        }

        $key = (($peer->id ?? null) && $peer->id !== 0)
            ? ($peer->email ?? $peer->id)
            : ('conversation:' . $conversation->id);

        if (!isset($contactMap[$key])) {
            $contactMap[$key] = [
                'conversation_id' => $conversation->id,
                'user_id' => $peer->id ?? $conversation->user_id_2 ?? $conversation->user_id_1 ?? null,
                'name' => $peer->name ?: 'Support team',
                'email' => $peer->email ?: 'support@fruitexpress.com',
                'role' => $peer->role ?: ucfirst($conversation->target_role ?: 'Support'),
                'status' => $peer instanceof \App\Models\User && $peer->isOnline() ? 'online' : 'away',
                'away_minutes' => $peer instanceof \App\Models\User ? $peer->awayMinutes() : null,
                'badge' => '',
                'profile_photo' => $peer?->profile_photo_url ?: null,
                'avatar' => strtoupper(substr(($peer->name ?: 'Support team'), 0, 2)),
                'messageCount' => 0,
                'lastActive' => $conversation->last_message_at ?? now(),
                'messages' => [],
            ];
        }

        // Load messages for this conversation
        $messages = $conversation->messages()->latest()->limit(50)->get()->reverse();

        foreach ($messages as $msg) {
            $isOutgoing = $msg->sender_id === $driver?->id;
            $contactMap[$key]['messages'][] = [
                'direction' => $isOutgoing ? 'outgoing' : 'incoming',
                'text' => $msg->message,
                'time' => $msg->created_at?->format('H:i') ?? 'Now',
            ];
            $contactMap[$key]['messageCount']++;
        }
    }

    $conversationUserIds = [];
    foreach ($conversations as $conversation) {
        $conversationUserIds[$conversation->user_id_1] = true;
        $conversationUserIds[$conversation->user_id_2] = true;
    }

    foreach ($profileUsers as $profileUser) {
        if (empty($profileUser->id) || isset($contactMap[$profileUser->email ?? $profileUser->id])) {
            continue;
        }

        $contactMap[$profileUser->email ?? $profileUser->id] = [
            'conversation_id' => null,
            'user_id' => $profileUser->id,
            'name' => $profileUser->name,
            'email' => $profileUser->email,
            'role' => ucfirst($profileUser->role ?? 'member'),
            'status' => $profileUser->isOnline() ? 'online' : 'away',
            'away_minutes' => $profileUser->awayMinutes(),
            'badge' => '',
            'profile_photo' => $profileUser->profile_photo_url ?: null,
            'avatar' => strtoupper(substr($profileUser->name, 0, 2)),
            'messageCount' => 0,
            'lastActive' => $conversationActivityByUserId[$profileUser->id] ?? now(),
            'messages' => [],
        ];
    }

    $contacts = collect(array_values($contactMap))->sortByDesc(function ($contact) {
        return strtotime($contact['lastActive'] ?? now());
    })->values()->all();

    $activeContact = $contacts[0] ?? [
        'name' => 'Support team',
        'email' => 'support@fruitexpress.com',
        'role' => 'admin',
        'status' => 'away',
        'away_minutes' => null,
        'badge' => '',
        'profile_photo' => null,
        'avatar' => 'ST',
        'messages' => [],
    ];
@endphp

<main class="px-3 py-4 sm:px-5 lg:px-6">
    <div class="mx-auto max-w-7xl overflow-hidden rounded-[28px] border border-emerald-100 bg-white shadow-[0_18px_60px_rgba(15,23,42,0.08)]">
        <div class="border-b border-emerald-100 bg-gradient-to-r from-emerald-800 via-emerald-700 to-emerald-500 p-4 text-white sm:p-5">
            <div class="flex items-center justify-between gap-3">
                <div class="flex items-center gap-3">
                    @if($driver?->profile_photo_url)
                        <img src="{{ $driver->profile_photo_url }}" alt="{{ $driverName }}" class="h-12 w-12 rounded-full object-cover ring-2 ring-white/30">
                    @else
                        <div class="flex h-12 w-12 items-center justify-center rounded-full bg-white/15 text-sm font-bold ring-2 ring-white/30">
                            {{ strtoupper(substr($driverName, 0, 2)) }}
                        </div>
                    @endif
                    <div>
                        <p class="text-[10px] uppercase tracking-[0.25em] text-emerald-100">Driver</p>
                        <h3 class="text-lg font-bold">{{ $driverName }}</h3>
                    </div>
                </div>
                <a href="{{ route('driver.dashboard') }}" class="inline-flex items-center justify-center gap-2 rounded-xl bg-white/10 px-3 py-2 text-xs font-semibold text-white hover:bg-white/15"><i class="fas fa-arrow-left"></i> Driver hub</a>
            </div>



            <div data-contact-search-wrapper class="relative mt-4 md:block">
                <i class="fas fa-magnifying-glass pointer-events-none absolute left-4 top-1/2 -translate-y-1/2 text-sm text-emerald-100"></i>
                <input data-contact-search type="search" placeholder="Search users" class="w-full rounded-2xl border border-white/20 bg-white/10 py-3 pl-11 pr-4 text-sm text-white placeholder:text-emerald-100 focus:border-white/30 focus:outline-none">
            </div>
        </div>

        <div class="grid md:grid-cols-[240px_minmax(0,1fr)]">
            <aside data-contact-index class="border-b border-emerald-100 bg-emerald-50/40 md:border-b-0 md:border-r">
                <div class="flex items-center justify-between border-b border-emerald-100 px-4 py-3">
                    <h4 class="text-xs font-bold uppercase tracking-[0.25em] text-slate-500">Inbox</h4>
                    <span class="rounded-full bg-emerald-100 px-2 py-1 text-[10px] font-bold text-emerald-700">{{ count($contacts) }}</span>
                </div>

                <div class="space-y-2 p-3">
                    @if(count($contacts) > 0)
                        @foreach($contacts as $contact)
                            <button type="button" data-contact-item data-user-id="{{ $contact['user_id'] ?? '' }}" data-name="{{ $contact['name'] }}" data-email="{{ $contact['email'] ?? '' }}" data-role="{{ $contact['role'] ?? 'Support' }}" data-score="{{ $contact['score'] ?? 0 }}" data-avatar-image="{{ $contact['profile_photo'] ?? '' }}" data-thread-messages='{{ json_encode($contact['messages'] ?? [], JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) }}' class="{{ $loop->first ? 'bg-white shadow-sm ring-1 ring-emerald-100' : 'bg-transparent hover:bg-white/60' }} flex w-full items-center gap-3 rounded-2xl px-3 py-3 text-left transition">
                                <div class="relative">
                                    @if(!empty($contact['profile_photo']))
                                        <img src="{{ $contact['profile_photo'] }}" alt="{{ $contact['name'] }}" class="h-11 w-11 rounded-full object-cover">
                                    @else
                                        <div class="flex h-11 w-11 items-center justify-center rounded-full bg-gradient-to-br from-emerald-600 to-amber-500 text-xs font-bold text-white">
                                            {{ $contact['avatar'] }}
                                        </div>
                                    @endif
                                    <span class="absolute -bottom-0.5 -right-0.5 h-3 w-3 rounded-full border-2 border-white {{ $contact['status'] === 'online' ? 'bg-emerald-500' : 'bg-amber-400' }}"></span>
                                </div>
                                <div class="min-w-0 flex-1">
                                    <div class="flex items-center justify-between gap-2">
                                        <p class="truncate text-sm font-semibold text-slate-800">{{ $contact['name'] }}</p>
                                        @if($contact['badge'])
                                            <span class="rounded-full bg-emerald-600 px-1.5 py-0.5 text-[10px] font-bold text-white">{{ $contact['badge'] }}</span>
                                        @endif
                                    </div>
                                    <p class="truncate text-xs text-slate-500">{{ $contact['role'] }}</p>
                                </div>
                            </button>
                        @endforeach
                    @else
                        <div class="flex min-h-[180px] flex-col items-center justify-center rounded-2xl border border-dashed border-emerald-200 bg-white/70 p-5 text-center text-slate-500">
                            <div class="mb-3 flex h-12 w-12 items-center justify-center rounded-full bg-emerald-100 text-emerald-600"><i class="fas fa-inbox"></i></div>
                            <p class="text-sm font-semibold text-slate-700">No conversations yet</p>
                            <p class="mt-1 text-xs text-slate-500">Messages from users or drivers will appear here.</p>
                        </div>
                    @endif
                </div>
            </aside>

            <main data-chat-panel class="hidden min-h-[360px] flex-col bg-white md:flex">
                <header class="flex items-center justify-between gap-3 border-b border-emerald-100 px-4 py-4 sm:px-5">
                    <div class="flex min-w-0 items-center gap-2">
                        <button type="button" data-mobile-messages-back class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full text-slate-600 hover:bg-slate-100 md:hidden" aria-label="Back to messages">
                            <i class="fas fa-arrow-left"></i>
                        </button>
                        <div class="flex h-11 w-11 items-center justify-center overflow-hidden rounded-full bg-gradient-to-br from-slate-700 to-slate-900 text-xs font-bold text-white">
                            @if(!empty($activeContact['profile_photo']))
                                <img src="{{ $activeContact['profile_photo'] }}" alt="{{ $activeContact['name'] }}" class="h-full w-full object-cover">
                            @else
                                {{ $activeContact['avatar'] }}
                            @endif
                        </div>
                        <div>
                            <h4 id="driver-profile-name" data-testid="driver-profile-name" class="font-semibold text-slate-900">{{ $activeContact['name'] }}</h4>
                            <p data-profile-status class="text-xs text-slate-500">{{ $activeContact['role'] }} • {{ $activeContact['status'] === 'online' ? 'Online now' : ($activeContact['away_minutes'] ? 'Away for ' . $activeContact['away_minutes'] . ' min' : 'Away') }}</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-2 text-slate-500">
                        @php
                            $driverThreadKey = !empty($activeContact['user_id'])
                                ? 'user:' . $activeContact['user_id'] . ':role:' . strtolower((string) ($activeContact['role'] ?? 'driver'))
                                : (!empty($activeContact['email'])
                                    ? 'email:' . strtolower((string) $activeContact['email']) . ':role:' . strtolower((string) ($activeContact['role'] ?? 'driver'))
                                    : ('name:' . strtolower((string) ($activeContact['name'] ?? 'support team')) . ':role:' . strtolower((string) ($activeContact['role'] ?? 'driver'))));
                        @endphp
                        @if(!empty($activeContact['conversation_id']) || !empty($activeContact['user_id']) || !empty($activeContact['email']) || !empty($activeContact['name']))
                            <form method="POST" action="{{ !empty($activeContact['conversation_id']) ? route('inquiries.conversations.destroy', $activeContact['conversation_id']) : '#' }}" data-delete-thread-form data-thread-key="{{ $driverThreadKey }}" data-has-conversation="{{ !empty($activeContact['conversation_id']) ? 'true' : 'false' }}" data-legacy-thread-key="{{ $activeContact['user_id'] ?? $activeContact['email'] ?? $activeContact['name'] ?? '' }}" onsubmit="return confirm('Delete this conversation?');" class="inline-block">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="flex h-10 w-10 items-center justify-center rounded-full bg-red-50 text-red-600 hover:bg-red-100" title="Delete conversation">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        @endif
                    </div>
                </header>

                <div data-chat-thread class="h-[360px] min-h-[240px] max-h-[55vh] space-y-4 overflow-y-auto bg-[radial-gradient(circle_at_top,_rgba(16,185,129,0.08),_transparent_30%)] p-4 sm:h-[420px] sm:max-h-[60vh] sm:p-5">
                    @foreach($activeContact['messages'] as $message)
                        <div class="flex {{ $message['direction'] === 'incoming' ? 'justify-start' : 'justify-end' }}">
                            <div class="max-w-[80%] rounded-2xl px-4 py-3 shadow-sm {{ $message['direction'] === 'incoming' ? 'bg-gray-100 text-slate-700' : 'bg-emerald-600 text-white' }}">
                                <p class="text-sm leading-6">{{ $message['text'] }}</p>
                                <span class="mt-2 block text-[10px] font-medium {{ $message['direction'] === 'incoming' ? 'text-slate-500' : 'text-emerald-100' }}">{{ $message['time'] }}</span>
                            </div>
                        </div>
                    @endforeach

                    <div data-chat-typing class="hidden flex justify-start">
                        <div class="rounded-2xl bg-gray-100 px-4 py-3 text-sm text-slate-500 shadow-sm">
                            <div class="flex items-center gap-2">
                                <span class="h-2 w-2 animate-bounce rounded-full bg-slate-400 [animation-delay:0ms]"></span>
                                <span class="h-2 w-2 animate-bounce rounded-full bg-slate-400 [animation-delay:150ms]"></span>
                                <span class="h-2 w-2 animate-bounce rounded-full bg-slate-400 [animation-delay:300ms]"></span>
                            </div>
                        </div>
                    </div>
                </div>

                <form data-chat-form data-recipient-id="{{ $activeContact['user_id'] ?? '' }}" data-recipient-name="{{ $activeContact['name'] }}" data-recipient-email="{{ $activeContact['email'] ?? '' }}" data-recipient-role="{{ $activeContact['role'] ?? 'Support' }}" method="POST" action="{{ route('inquiries.store') }}" class="border-t border-emerald-100 bg-white p-3 sm:p-4">
                    @csrf
                    <input type="hidden" name="recipient_id" value="{{ $activeContact['user_id'] ?? '' }}">
                    <input type="hidden" name="recipient_name" value="{{ $activeContact['name'] }}">
                    <input type="hidden" name="recipient_email" value="{{ $activeContact['email'] ?? '' }}">
                    <input type="hidden" name="recipient_role" value="{{ $activeContact['role'] ?? 'Support' }}">
                    <div class="flex items-end gap-3 rounded-2xl border border-emerald-100 bg-emerald-50/40 p-2">
                        <textarea name="message" data-chat-input rows="1" placeholder="Write a message..." class="max-h-32 min-h-[44px] flex-1 resize-none border-0 bg-transparent px-3 py-2 text-sm text-slate-700 placeholder:text-slate-400 focus:outline-none"></textarea>
                        <button type="submit" class="flex h-11 w-11 items-center justify-center rounded-xl bg-emerald-600 text-white shadow-lg shadow-emerald-600/20 transition hover:bg-emerald-500"><i class="fas fa-paper-plane"></i></button>
                    </div>
                </form>
            </main>
        </div>
    </div>
</main>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const searchInput = document.querySelector('[data-contact-search]');
        const contactItems = Array.from(document.querySelectorAll('[data-contact-item]'));
        const profileItems = Array.from(document.querySelectorAll('[data-profile-item]'));
        const form = document.querySelector('[data-chat-form]');
        const input = document.querySelector('[data-chat-input]');
        const thread = document.querySelector('[data-chat-thread]');
        const typing = document.querySelector('[data-chat-typing]');
        const contactIndex = document.querySelector('[data-contact-index]');
        const chatPanel = document.querySelector('[data-chat-panel]');
        const mobileMessagesBack = document.querySelector('[data-mobile-messages-back]');
        const contactSearchWrapper = document.querySelector('[data-contact-search-wrapper]');
        const nameEl = document.querySelector('#driver-profile-name');
        const subtitleEl = document.querySelector('main header p');
        const avatarEl = document.querySelector('main header .flex.h-11.w-11');
        const storageKey = 'fruitexpress_message_threads';
        const threadCache = (() => {
            try {
                return JSON.parse(localStorage.getItem(storageKey) || '{}');
            } catch (error) {
                return {};
            }
        })();

        const persistThreadCache = function () {
            try {
                localStorage.setItem(storageKey, JSON.stringify(threadCache));
            } catch (error) {
                // Ignore storage issues silently.
            }
        };

        const pruneStaleThreadCache = function () {
            const validKeys = new Set();

            Array.from(document.querySelectorAll('[data-contact-item]')).forEach(function (item) {
                const userId = item.dataset.userId || '';
                const email = item.dataset.email || '';
                const name = item.dataset.name || '';
                const role = item.dataset.role || 'driver';

                if (userId) {
                    validKeys.add('user:' + String(userId) + ':role:' + String(role).trim().toLowerCase());
                }

                if (email) {
                    validKeys.add('email:' + String(email).trim().toLowerCase() + ':role:' + String(role).trim().toLowerCase());
                }

                if (name) {
                    validKeys.add('name:' + String(name).trim().toLowerCase() + ':role:' + String(role).trim().toLowerCase());
                }
            });

            Object.keys(threadCache).forEach(function (key) {
                if (!validKeys.has(String(key))) {
                    delete threadCache[key];
                }
            });

            persistThreadCache();
        };

        const buildThreadKey = function (userId, email, name, role) {
            const rawUserId = userId || '';
            const rawEmail = email || '';
            const rawName = name || '';
            const rawRole = role || 'driver';

            if (rawUserId) {
                return 'user:' + String(rawUserId) + ':role:' + String(rawRole).toLowerCase();
            }

            if (rawEmail) {
                return 'email:' + String(rawEmail).toLowerCase() + ':role:' + String(rawRole).toLowerCase();
            }

            if (rawName) {
                return 'name:' + String(rawName).trim().toLowerCase() + ':role:' + String(rawRole).toLowerCase();
            }

            return 'role:' + String(rawRole).toLowerCase();
        };

        const clearThreadCacheEntry = function (threadKey, legacyThreadKey) {
            const keysToDelete = new Set();

            if (threadKey) {
                keysToDelete.add(String(threadKey));
            }

            if (legacyThreadKey) {
                keysToDelete.add(String(legacyThreadKey));
            }

            if (threadKey && legacyThreadKey && threadKey !== legacyThreadKey) {
                const threadKeyParts = String(threadKey).split(':role:');
                const legacyParts = String(legacyThreadKey).split(':role:');

                if (threadKeyParts.length === 2) {
                    keysToDelete.add(threadKeyParts[0]);
                }

                if (legacyParts.length === 2) {
                    keysToDelete.add(legacyParts[0]);
                }
            }

            keysToDelete.forEach(function (key) {
                delete threadCache[key];
            });

            persistThreadCache();
        };

        const getThreadMessagesFromItem = function (item) {
            if (!item) {
                return [];
            }

            try {
                const parsed = JSON.parse(item.dataset.threadMessages || '[]');
                return Array.isArray(parsed) ? parsed : [];
            } catch (error) {
                return [];
            }
        };

        const renderThreadMessages = function (messages) {
            if (!thread) {
                return;
            }

            if (!Array.isArray(messages) || messages.length === 0) {
                thread.innerHTML = '<div class="flex min-h-[180px] items-center justify-center text-sm text-slate-500">No messages yet.</div>';
                return;
            }

            thread.innerHTML = messages.map(function (message) {
                const direction = message.direction === 'incoming' ? 'justify-start' : 'justify-end';
                const bubble = message.direction === 'incoming' ? 'bg-slate-100 text-slate-700' : 'bg-emerald-600 text-white';
                const timeClass = message.direction === 'incoming' ? 'text-slate-500' : 'text-emerald-100';
                return '<div class="flex ' + direction + '"><div class="max-w-[80%] rounded-2xl px-4 py-3 shadow-sm ' + bubble + '"><p class="text-sm leading-6">' + (message.text || '') + '</p><span class="mt-2 block text-[10px] font-medium ' + timeClass + '">' + (message.time || 'Now') + '</span></div></div>';
            }).join('');
        };

        const updateSelectedProfile = function (name, role, avatar, avatarImage, presence, awayMinutes) {
            if (nameEl) {
                nameEl.textContent = name;
            }
            if (subtitleEl) {
                subtitleEl.textContent = role + ' • ' + (presence === 'online' ? 'Online now' : (awayMinutes ? 'Away for ' + awayMinutes + ' min' : 'Away'));
            }
            if (avatarEl) {
                if (avatarImage) {
                    avatarEl.innerHTML = '<img src="' + avatarImage + '" alt="' + name + '" class="h-full w-full rounded-full object-cover">';
                    return;
                }
                avatarEl.innerHTML = '<span class="flex h-full w-full items-center justify-center text-xs font-bold">' + avatar + '</span>';
            }
        };

        const showMobileChat = function () {
            if (!contactIndex || !chatPanel || !window.matchMedia('(max-width: 767px)').matches) {
                return;
            }

            contactIndex.classList.add('hidden');
            chatPanel.classList.remove('hidden');
            chatPanel.classList.add('flex');
            contactSearchWrapper?.classList.add('hidden');
        };

        const showMobileContacts = function () {
            if (!contactIndex || !chatPanel || !window.matchMedia('(max-width: 767px)').matches) {
                return;
            }

            chatPanel.classList.add('hidden');
            chatPanel.classList.remove('flex');
            contactIndex.classList.remove('hidden');
            contactSearchWrapper?.classList.remove('hidden');
        };

        const refreshPresence = function (item) {
            const userId = item && item.dataset.userId;
            if (!userId) return;
            fetch('{{ route('presence.show', ['user' => '__USER__']) }}'.replace('__USER__', userId), { headers: { 'Accept': 'application/json' } })
                .then(function (response) { return response.ok ? response.json() : null; })
                .then(function (presence) {
                    if (!presence) return;
                    item.dataset.presence = presence.status;
                    item.dataset.awayMinutes = presence.away_minutes || '';
                    const dot = item.querySelector('span.absolute');
                    if (dot) {
                        dot.classList.toggle('bg-emerald-500', presence.status === 'online');
                        dot.classList.toggle('bg-amber-400', presence.status !== 'online');
                    }
                    const status = document.querySelector('[data-profile-status]');
                    if (status && item.classList.contains('ring-1')) status.textContent = item.dataset.role + ' • ' + (presence.status === 'online' ? 'Online now' : (presence.away_minutes ? 'Away for ' + presence.away_minutes + ' min' : 'Away'));
                }).catch(function () {});
        };

        const ensureInboxContact = function (item) {
            if (!item) {
                return;
            }

            const entryId = (item.dataset.userId || item.dataset.email || item.dataset.name || '').toString().trim();
            if (!entryId) {
                return;
            }

            const alreadyListed = contactItems.some(function (contactItem) {
                const contactId = (contactItem.dataset.userId || contactItem.dataset.email || contactItem.dataset.name || '').toString().trim();
                return contactId && contactId === entryId;
            });

            if (alreadyListed) {
                return;
            }

            const list = document.querySelector('aside .space-y-2');
            if (!list) {
                return;
            }

            const newItem = document.createElement('button');
            const avatarImage = item.dataset.avatarImage || '';
            const name = (item.dataset.name || 'Support team').toString();
            const role = (item.dataset.role || 'driver').toString();
            const avatar = (name.substring(0, 2) || 'ST').toUpperCase();

            newItem.type = 'button';
            newItem.dataset.contactItem = '';
            newItem.dataset.userId = item.dataset.userId || '';
            newItem.dataset.name = name;
            newItem.dataset.email = item.dataset.email || '';
            newItem.dataset.role = role;
            newItem.dataset.score = '0';
            newItem.dataset.avatarImage = avatarImage;
            newItem.dataset.threadMessages = '[]';
            newItem.className = 'flex w-full items-center gap-3 rounded-2xl bg-white px-3 py-3 text-left shadow-sm ring-1 ring-emerald-100 transition';
            newItem.innerHTML = '<div class="relative">' + (avatarImage ? '<img src="' + avatarImage + '" alt="' + name + '" class="h-11 w-11 rounded-full object-cover">' : '<div class="flex h-11 w-11 items-center justify-center rounded-full bg-gradient-to-br from-emerald-600 to-green-500 text-xs font-bold text-white">' + avatar + '</div>') + '<span class="absolute -bottom-0.5 -right-0.5 h-3 w-3 rounded-full border-2 border-white bg-emerald-500"></span></div><div class="min-w-0 flex-1"><div class="flex items-center justify-between gap-2"><p class="truncate text-sm font-semibold text-slate-800">' + name + '</p></div><p class="truncate text-xs text-slate-500">' + role + '</p></div>';

            newItem.addEventListener('click', function () {
                setRecipient(this);
            });

            list.insertBefore(newItem, list.firstChild);
            contactItems.push(newItem);
        };

        setInterval(function () {
            document.querySelectorAll('[data-contact-item]').forEach(refreshPresence);
        }, 30000);

        const setRecipient = function (item) {
            if (!item || !form) {
                return;
            }

            refreshPresence(item);

            const itemUserId = (item.dataset.userId || '').toString().trim();
            const itemEmail = (item.dataset.email || '').toString().trim();
            const itemName = (item.dataset.name || '').toString().trim();

            if (!itemUserId && !itemEmail && !itemName) {
                return;
            }

            ensureInboxContact(item);

            const userId = itemUserId || form.dataset.recipientId || '';
            const name = itemName || form.dataset.recipientName || 'Support team';
            const email = itemEmail || form.dataset.recipientEmail || '';
            const role = item.dataset.role || form.dataset.recipientRole || 'driver';
            const avatarImage = item.dataset.avatarImage || '';
            const threadKey = buildThreadKey(userId, email, name, role);
            const threadMessages = getThreadMessagesFromItem(item);
            const restoredMessages = threadKey ? (threadCache[threadKey] || threadMessages) : threadMessages;

            if (threadKey) {
                threadCache[threadKey] = restoredMessages;
                persistThreadCache();
            }

            form.dataset.recipientId = userId;
            form.dataset.threadKey = threadKey;
            form.dataset.recipientName = name;
            form.dataset.recipientEmail = email;
            form.dataset.recipientRole = role;

            const recipientIdInput = form.querySelector('input[name="recipient_id"]');
            const recipientNameInput = form.querySelector('input[name="recipient_name"]');
            const recipientEmailInput = form.querySelector('input[name="recipient_email"]');
            const recipientRoleInput = form.querySelector('input[name="recipient_role"]');

            if (recipientIdInput) recipientIdInput.value = userId;
            if (recipientNameInput) recipientNameInput.value = name;
            if (recipientEmailInput) recipientEmailInput.value = email;
            if (recipientRoleInput) recipientRoleInput.value = role;

            updateSelectedProfile(name, role, item.dataset.avatar || name.substring(0, 2).toUpperCase() || 'ST', avatarImage, item.dataset.presence || 'away', item.dataset.awayMinutes || '');
            renderThreadMessages(restoredMessages);
            applyActiveContactStyles();
            updateSelectedContactState();
            showMobileChat();
        };

        const sortContactsByScore = function () {
            const container = contactItems[0]?.parentNode;
            if (!container) {
                return;
            }

            contactItems
                .sort((a, b) => Number(b.dataset.score || 0) - Number(a.dataset.score || 0))
                .forEach(function (item) {
                    container.appendChild(item);
                });
        };

        const getItemIdentifier = function (item) {
            return (item?.dataset?.userId || item?.dataset?.email || '').toString().trim();
        };

        const matchesUserQuery = function (item, query) {
            if (!item) {
                return false;
            }

            const name = (item.dataset.name || '').toLowerCase();
            const role = (item.dataset.role || '').toLowerCase();
            const email = (item.dataset.email || '').toLowerCase();
            const normalizedQuery = (query || '').trim().toLowerCase();

            if (!normalizedQuery) {
                return true;
            }

            return name.includes(normalizedQuery)
                || role.includes(normalizedQuery)
                || email.includes(normalizedQuery)
                || name.startsWith(normalizedQuery)
                || role.startsWith(normalizedQuery)
                || email.startsWith(normalizedQuery)
                || name.includes(' ' + normalizedQuery)
                || name.includes(normalizedQuery + ' ');
        };

        const applyActiveContactStyles = function () {
            const activeId = (form?.dataset?.recipientId || '').toString().trim();

            contactItems.forEach(function (item) {
                const itemId = getItemIdentifier(item);
                const isSelected = !!activeId && itemId === activeId;

                item.classList.toggle('bg-white', isSelected);
                item.classList.toggle('shadow-sm', isSelected);
                item.classList.toggle('ring-1', isSelected);
                item.classList.toggle('ring-emerald-100', isSelected);
                item.classList.toggle('bg-transparent', !isSelected);
                item.classList.toggle('hover:bg-white/60', !isSelected);
                item.classList.toggle('hover:bg-white/80', !isSelected);
            });
        };

        const updateSelectedContactState = function () {
            const activeId = (form?.dataset?.recipientId || '').toString().trim();
            const currentQuery = (searchInput?.value || '').trim();

            contactItems.forEach(function (item) {
                const itemId = getItemIdentifier(item);
                const isSelected = !!activeId && itemId === activeId;
                const shouldShow = matchesUserQuery(item, currentQuery) || isSelected;
                item.style.display = shouldShow ? 'flex' : 'none';
            });

            profileItems.forEach(function (item) {
                const itemId = getItemIdentifier(item);
                const isSelected = !!activeId && itemId === activeId;
                const shouldShow = matchesUserQuery(item, currentQuery) || isSelected;
                item.style.display = shouldShow ? 'flex' : 'none';
            });

            applyActiveContactStyles();
        };

        sortContactsByScore();
        updateSelectedContactState();
        pruneStaleThreadCache();

        document.querySelectorAll('[data-delete-thread-form]').forEach(function (deleteForm) {
            deleteForm.addEventListener('submit', function (event) {
                const hasConversation = String(deleteForm.dataset.hasConversation || 'false').toLowerCase() === 'true';
                const threadKey = deleteForm.dataset.threadKey || '';

                if (!hasConversation) {
                    event.preventDefault();

                    if (threadKey) {
                        delete threadCache[threadKey];
                        persistThreadCache();
                    }

                    const selectedContact = document.querySelector('[data-contact-item].bg-white, [data-contact-item].ring-1') || document.querySelector('[data-contact-item]');
                    if (selectedContact) {
                        selectedContact.remove();
                    }

                    if (thread) {
                        thread.innerHTML = '<div class="flex min-h-[180px] items-center justify-center text-sm text-slate-500">No messages yet.</div>';
                    }

                    return false;
                }

                clearThreadCacheEntry(deleteForm.dataset.threadKey || '', deleteForm.dataset.legacyThreadKey || '');
            });
        });

        if (searchInput) {
            searchInput.addEventListener('input', updateSelectedContactState);
        }

        if (mobileMessagesBack) {
            mobileMessagesBack.addEventListener('click', showMobileContacts);
        }

        profileItems.forEach(function (item) {
            item.addEventListener('click', function () {
                setRecipient(this);
            });
        });

        if (form && input && thread) {
            document.querySelectorAll('[data-contact-item]').forEach(function (item) {
                item.addEventListener('click', function () {
                    setRecipient(item);
                });
            });

            form.addEventListener('submit', function (event) {
                event.preventDefault();
                const value = input.value.trim();
                if (!value) {
                    return;
                }

                const formData = new FormData(form);
                const recipientId = form.dataset.recipientId || form.querySelector('input[name="recipient_id"]').value || '';
                const recipientEmail = form.dataset.recipientEmail || form.querySelector('input[name="recipient_email"]').value || '';
                const recipientName = form.dataset.recipientName || form.querySelector('input[name="recipient_name"]').value || 'Support team';
                const recipientRole = form.dataset.recipientRole || form.querySelector('input[name="recipient_role"]').value || 'driver';
                const threadKey = form.dataset.threadKey || buildThreadKey(recipientId, recipientEmail, recipientName, recipientRole);
                const currentThread = threadKey && threadCache[threadKey] ? threadCache[threadKey] : [];
                formData.set('message', value);
                formData.set('recipient_id', recipientId);
                formData.set('recipient_name', recipientName);
                formData.set('recipient_email', recipientEmail);
                formData.set('recipient_role', recipientRole);
                formData.set('target_role', recipientRole);

                if (typing) {
                    typing.classList.remove('hidden');
                }

                fetch(form.action, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: formData
                })
                    .then(function (response) {
                        if (!response.ok) {
                            throw new Error('Message failed to send');
                        }
                        return response.json();
                    })
                    .then(function () {
                        const time = new Date().toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
                        const outgoing = {
                            direction: 'outgoing',
                            text: value,
                            time: time
                        };

                        if (threadKey && threadCache[threadKey]) {
                            threadCache[threadKey].push(outgoing);
                            persistThreadCache();
                            renderThreadMessages(threadCache[threadKey]);
                        } else {
                            const newThread = [outgoing];
                            if (threadKey) {
                                threadCache[threadKey] = newThread;
                                persistThreadCache();
                            }
                            renderThreadMessages(newThread);
                        }

                        input.value = '';
                        thread.scrollTop = thread.scrollHeight;
                    })
                    .catch(function () {
                        alert('Message could not be sent. Please try again.');
                    })
                    .finally(function () {
                        if (typing) {
                            typing.classList.add('hidden');
                        }
                    });
            });
        }
    });
</script>
</body>
</html>
