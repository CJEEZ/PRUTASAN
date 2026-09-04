<?php

namespace App\Console\Commands;

use App\Models\Conversation;
use App\Models\Inquiry;
use Illuminate\Console\Command;

class RepairMessagingThreads extends Command
{
    protected $signature = 'app:repair-messaging-threads';

    protected $description = 'Rebuild stale role-based conversations into real user-to-user threads when both participants are known.';

    public function handle(): int
    {
        $updated = 0;
        $deleted = 0;

        $staleConversations = Conversation::where(function ($query) {
            $query->where(function ($subQuery) {
                $subQuery->whereNotNull('user_id_1')->whereNull('user_id_2');
            })->orWhere(function ($subQuery) {
                $subQuery->whereNull('user_id_1')->whereNotNull('user_id_2');
            });
        })->get();

        foreach ($staleConversations as $conversation) {
            $participantIds = Inquiry::query()
                ->where('thread_key', $conversation->thread_key)
                ->orWhere('conversation_id', $conversation->id)
                ->pluck('sender_id')
                ->merge(Inquiry::query()->where('thread_key', $conversation->thread_key)->pluck('recipient_id'))
                ->filter()
                ->unique()
                ->values();

            if ($participantIds->count() < 2) {
                continue;
            }

            $senderId = min($participantIds->all());
            $recipientId = max($participantIds->all());
            $threadKey = 'users:' . $senderId . ':' . $recipientId;

            $existing = Conversation::where('thread_key', $threadKey)->first();

            if ($existing && $existing->id !== $conversation->id) {
                Inquiry::where('conversation_id', $conversation->id)->update(['conversation_id' => $existing->id, 'thread_key' => $threadKey]);
                $conversation->delete();
                $deleted++;
                continue;
            }

            $conversation->update([
                'user_id_1' => $senderId,
                'user_id_2' => $recipientId,
                'thread_key' => $threadKey,
                'subject' => $conversation->subject ?: 'Message',
                'target_role' => $conversation->target_role,
                'last_message_at' => $conversation->last_message_at ?? now(),
            ]);

            $updated++;
        }

        $this->info("Repaired {$updated} stale conversation(s). Removed {$deleted} duplicate legacy thread(s).");

        return self::SUCCESS;
    }
}
