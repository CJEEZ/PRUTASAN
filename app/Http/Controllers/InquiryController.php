<?php

namespace App\Http\Controllers;

use App\Models\Inquiry;
use App\Models\Conversation;
use App\Models\User;
use App\Models\Notification;
use Illuminate\Http\Request;

class InquiryController extends Controller
{
    public function create()
    {
        return view('inquiry.create');
    }

    public function store(Request $request)
    {
        $user = $request->user();

        $data = $request->validate([
            'subject' => ['nullable', 'string', 'max:255'],
            'message' => ['required', 'string'],
            'category' => ['nullable', 'string', 'max:50'],
            'target_role' => ['nullable', 'string', 'max:30'],
            'recipient_role' => ['nullable', 'string', 'max:30'],
            'recipient_name' => ['nullable', 'string', 'max:255'],
            'recipient_email' => ['nullable', 'email', 'max:255'],
            'recipient_id' => ['nullable', 'integer', 'exists:users,id'],
            'priority' => ['nullable', 'string', 'max:20'],
            'name' => ['nullable', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255'],
        ]);

        $recipientEmail = $data['recipient_email'] ?? $data['email'] ?? ($user?->email ?? null);
        $recipientRole = $this->normalizeChatRoleForUser($user, $data['recipient_role'] ?? $data['target_role'] ?? ($user?->role ?? 'admin'));
        $senderName = $user ? $user->name : ($data['name'] ?? $data['recipient_name'] ?? 'Guest');
        $recipientName = $data['recipient_name'] ?? $data['name'] ?? null;

        $recipientId = $data['recipient_id'] ?? null;
        $recipientUser = $recipientId
            ? User::find($recipientId)
            : ($recipientEmail ? User::where('email', $recipientEmail)->first() : null);

        $senderId = $user ? $user->id : null;
        $recipientId = $recipientUser?->id ?? $recipientId;
        $recipientName = $recipientName ?? $recipientUser?->name ?? null;

        if ($recipientUser && ! empty($recipientUser->role)) {
            $recipientRole = strtolower((string) $recipientUser->role);
        }

        $threadKey = $this->buildThreadKey($senderId, $recipientId, $recipientEmail, $recipientRole, $recipientName);

        // Find or create the conversation thread
        $conversation = $this->findOrCreateConversation($senderId, $recipientId, $threadKey, $recipientRole, $data['subject'] ?? 'Message', $recipientName);

        Inquiry::create([
            'conversation_id' => $conversation->id,
            'user_id' => $senderId,
            'sender_id' => $senderId,
            'recipient_id' => $recipientId,
            'name' => $senderName,
            'email' => $user?->email ?? ($data['email'] ?? $recipientEmail),
            'recipient_email' => $recipientEmail,
            'subject' => $data['subject'] ?? 'Message',
            'category' => $data['category'] ?? 'general',
            'target_role' => $recipientRole,
            'priority' => $data['priority'] ?? 'normal',
            'message' => $data['message'],
            'is_read' => false,
            'status' => 'pending',
            'thread_key' => $threadKey,
        ]);

        if ($recipientUser && $recipientUser->id !== $senderId) {
            Notification::create([
                'user_id' => $recipientUser->id,
                'type' => 'message',
                'title' => 'New message from ' . $senderName,
                'message' => $data['message'],
            ]);
        }

        // Update conversation's last message timestamp
        $conversation->update(['last_message_at' => now()]);

        if ($request->expectsJson() || $request->wantsJson()) {
            return response()->json(['success' => true, 'message' => 'Your message has been sent.']);
        }

        if ($request->filled('redirect_to')) {
            return redirect($request->input('redirect_to'))->with('success', 'Your message has been sent.');
        }

        return redirect()->route('home')->with('success', 'Your message has been sent.');
    }

    public function destroyConversation(Request $request, Conversation $conversation)
    {
        $user = $request->user();

        if (! $user || ! $this->canAccessConversation($user, $conversation)) {
            abort(403, 'You do not have access to this conversation.');
        }

        $conversation->messages()->delete();
        $conversation->delete();

        if ($request->expectsJson() || $request->wantsJson()) {
            return response()->json(['success' => true, 'message' => 'Conversation deleted.']);
        }

        return back()->with('success', 'Conversation deleted successfully.');
    }

    protected function canAccessConversation($user, Conversation $conversation): bool
    {
        if (! $user) {
            return false;
        }

        if ($user->can('access-admin')) {
            return true;
        }

        return $conversation->user_id_1 == $user->id
            || $conversation->user_id_2 == $user->id
            || $conversation->messages()->where('sender_id', $user->id)->exists();
    }

    protected function normalizeChatRoleForUser(?object $user, mixed $requestedRole): string
    {
        $requested = strtolower((string) ($requestedRole ?? ''));
        $validRoles = ['admin', 'seller', 'driver', 'customer', 'user'];

        if (in_array($requested, $validRoles, true)) {
            return $requested;
        }

        if ($user && in_array(strtolower((string) $user->role), $validRoles, true)) {
            return strtolower((string) $user->role);
        }

        return 'admin';
    }

    protected function buildThreadKey(?int $senderId, ?int $recipientId, ?string $recipientEmail, ?string $recipientRole, ?string $recipientName = null): string
    {
        $normalizedRole = strtolower((string) ($recipientRole ?: 'support'));
        $normalizedName = is_string($recipientName) ? trim($recipientName) : '';

        if ($senderId && $recipientId) {
            return 'users:' . min($senderId, $recipientId) . ':' . max($senderId, $recipientId);
        }

        if ($senderId && $normalizedName) {
            return 'name:' . $senderId . ':' . strtolower($normalizedName) . ':' . $normalizedRole;
        }

        if ($senderId && $recipientEmail) {
            return 'email:' . $senderId . ':' . strtolower($recipientEmail);
        }

        if ($senderId && $recipientRole) {
            return 'role:' . $senderId . ':' . $normalizedRole;
        }

        if ($normalizedName) {
            return 'guest:' . strtolower($normalizedName) . ':' . $normalizedRole;
        }

        if ($recipientEmail) {
            return 'email:' . ($senderId ?: 'guest') . ':' . strtolower($recipientEmail);
        }

        return 'role:' . ($senderId ?: 'guest') . ':' . $normalizedRole;
    }

    protected function findOrCreateConversation(?int $senderId, ?int $recipientId, string $threadKey, ?string $targetRole, ?string $subject, ?string $recipientName = null): Conversation
    {
        $conversation = Conversation::where('thread_key', $threadKey)->first();

        if ($conversation) {
            return $conversation;
        }

        if ($senderId && $recipientId) {
            $userThreadConversation = Conversation::where(function ($query) use ($senderId, $recipientId) {
                $query->where(function ($subQuery) use ($senderId, $recipientId) {
                    $subQuery->where('user_id_1', min($senderId, $recipientId))
                        ->where('user_id_2', max($senderId, $recipientId));
                })->orWhere(function ($subQuery) use ($senderId, $recipientId) {
                    $subQuery->where('user_id_1', max($senderId, $recipientId))
                        ->where('user_id_2', min($senderId, $recipientId));
                })->orWhere(function ($subQuery) use ($senderId) {
                    $subQuery->where('user_id_1', $senderId)
                        ->whereNull('user_id_2');
                })->orWhere(function ($subQuery) use ($senderId) {
                    $subQuery->where('user_id_2', $senderId)
                        ->whereNull('user_id_1');
                })->orWhereExists(function ($subQuery) use ($senderId, $recipientId) {
                    $subQuery->from('inquiries')
                        ->whereColumn('inquiries.conversation_id', 'conversations.id')
                        ->where(function ($inquiryQuery) use ($senderId, $recipientId) {
                            $inquiryQuery->where(function ($pairQuery) use ($senderId, $recipientId) {
                                $pairQuery->where('inquiries.sender_id', $senderId)
                                    ->where('inquiries.recipient_id', $recipientId);
                            })->orWhere(function ($pairQuery) use ($senderId, $recipientId) {
                                $pairQuery->where('inquiries.sender_id', $recipientId)
                                    ->where('inquiries.recipient_id', $senderId);
                            });
                        });
                });
            })->orderByDesc('last_message_at')->first();

            if ($userThreadConversation) {
                $userThreadConversation->update([
                    'user_id_1' => min($senderId, $recipientId),
                    'user_id_2' => max($senderId, $recipientId),
                    'thread_key' => $threadKey,
                    'subject' => $subject,
                    'target_role' => $targetRole,
                    'last_message_at' => now(),
                ]);

                Conversation::where(function ($query) use ($senderId, $recipientId) {
                    $query->where(function ($subQuery) use ($senderId, $recipientId) {
                        $subQuery->where('user_id_1', min($senderId, $recipientId))
                            ->where('user_id_2', max($senderId, $recipientId));
                    })->orWhere(function ($subQuery) use ($senderId, $recipientId) {
                        $subQuery->where('user_id_1', max($senderId, $recipientId))
                            ->where('user_id_2', min($senderId, $recipientId));
                    });
                })->whereKeyNot($userThreadConversation->id)->delete();

                return $userThreadConversation->fresh();
            }

            return Conversation::create([
                'user_id_1' => min($senderId, $recipientId),
                'user_id_2' => max($senderId, $recipientId),
                'thread_key' => $threadKey,
                'subject' => $subject,
                'target_role' => $targetRole,
                'last_message_at' => now(),
            ]);
        }

        if ($senderId && $targetRole && empty(trim((string) ($recipientName ?? ''))) && empty($recipientId)) {
            $roleConversation = Conversation::where('thread_key', $threadKey)->first();

            if ($roleConversation) {
                return $roleConversation;
            }

            $roleConversation = Conversation::where(function ($query) use ($senderId, $targetRole) {
                $query->where(function ($participantQuery) use ($senderId) {
                    $participantQuery->where(function ($selfQuery) use ($senderId) {
                        $selfQuery->where('user_id_1', $senderId)
                            ->whereNull('user_id_2');
                    })->orWhere(function ($selfQuery) use ($senderId) {
                        $selfQuery->where('user_id_2', $senderId)
                            ->whereNull('user_id_1');
                    });
                })->where('target_role', $targetRole);
            })->orderByDesc('last_message_at')->first();

            if ($roleConversation) {
                return $roleConversation;
            }
        }

        return Conversation::create([
            'user_id_1' => $senderId,
            'user_id_2' => null,
            'thread_key' => $threadKey,
            'subject' => $subject,
            'target_role' => $targetRole,
            'last_message_at' => now(),
        ]);
    }
}

