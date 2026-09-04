<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Conversation extends Model
{
    protected $fillable = [
        'user_id_1',
        'user_id_2',
        'thread_key',
        'subject',
        'target_role',
        'last_message_at',
    ];

    protected $casts = [
        'last_message_at' => 'datetime',
    ];

    public function user1(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id_1');
    }

    public function user2(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id_2');
    }

    public function messages(): HasMany
    {
        return $this->hasMany(Inquiry::class, 'conversation_id');
    }

    public function getOtherUserId(int $currentUserId): ?int
    {
        if ($this->user_id_1 === $currentUserId) {
            return $this->user_id_2;
        }
        if ($this->user_id_2 === $currentUserId) {
            return $this->user_id_1;
        }
        return null;
    }

    public function getOtherUser(int $currentUserId): ?User
    {
        $otherId = $this->getOtherUserId($currentUserId);
        return $otherId ? User::find($otherId) : null;
    }
}
