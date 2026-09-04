<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'order_number',
        'status',
        'payment_method',
        'payment_status',
        'gcash_reference',
        'payment_confirmed_at',
        'full_name',
        'phone',
        'street_address',
        'barangay',
        'city',
        'province',
        'postal_code',
        'latitude',
        'longitude',
        'driver_latitude',
        'driver_longitude',
        'driver_location_updated_at',
        'subtotal',
        'shipping',
        'total',
        'tracking_number',
        'tracking_status',
        'current_location',
    ];

    protected $casts = [
        'subtotal' => 'decimal:2',
        'shipping' => 'decimal:2',
        'total' => 'decimal:2',
        'payment_confirmed_at' => 'datetime',
        'driver_location_updated_at' => 'datetime',
    ];

    /**
     * Relationship: An order belongs to a user.
     * Use withTrashed so soft-deleted users still display for past orders.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class)->withTrashed();
    }

    /**
     * Relationship: An order has many order items.
     */
    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    /**
     * Relationship: An order may have one shipment record.
     */
    public function shipment(): HasOne
    {
        return $this->hasOne(Shipment::class);
    }

    /**
     * Relationship: An order has many tracking history records.
     */
    public function trackingHistory(): HasMany
    {
        return $this->hasMany(TrackingHistory::class)->orderBy('timestamp', 'desc');
    }

    /**
     * Check if this order has any items that belong to the given seller.
     */
    public function hasSeller(int $sellerId): bool
    {
        return $this->items()->whereHas('product', function ($q) use ($sellerId) {
            $q->where('seller_id', $sellerId);
        })->exists();
    }

    public const TRACKABLE_STATUSES = [
        'preparing',
        'ready_for_pickup',
        'shipped',
        'in_transit',
        'out_for_delivery',
        'to_receive',
        'delivered',
        'completed',
    ];

    public function isTrackable(): bool
    {
        return in_array($this->status, self::TRACKABLE_STATUSES, true);
    }
}
