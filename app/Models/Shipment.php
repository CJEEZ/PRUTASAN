<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Shipment extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id', 'tracking_number', 'carrier', 'status', 'shipped_at',
    ];

    protected $casts = [
        'shipped_at' => 'datetime',
    ];

    /**
     * Relationship: A shipment belongs to an order.
     */
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    /**
     * Relationship: A shipment has many tracking history records.
     */
    public function trackingHistory(): HasMany
    {
        return $this->hasMany(TrackingHistory::class)->orderBy('timestamp', 'desc');
    }
}

