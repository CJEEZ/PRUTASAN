<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Models\PaymentMethod;
use App\Models\Address;
use App\Models\DriverApplication;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasFactory, Notifiable, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'phone_number',
        'shipping_address',
        'profile_photo_path',
        'date_of_birth',
        'gender',
        'provider',
        'provider_id',
        'seller_status',
        'seller_rejection_reason',
        'seller_request_date',
        'is_approved',
        'email_verified_at',
        'driver_available',
        'last_seen_at',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'seller_request_date' => 'datetime',
        'deleted_at' => 'datetime',
        'password' => 'hashed',
        'last_seen_at' => 'datetime',
    ];

    public function isOnline(): bool
    {
        return $this->last_seen_at?->gt(now()->subMinutes(2)) ?? false;
    }

    public function awayMinutes(): ?int
    {
        if (! $this->last_seen_at || $this->isOnline()) {
            return null;
        }

        return max(1, (int) $this->last_seen_at->diffInMinutes(now()));
    }

    /**
     * Get the computed seller status for the admin dashboard.
     *
     * @return string|null
     */
    public function getComputedSellerStatusAttribute(): ?string
    {
        if ($this->role !== 'seller') {
            return null;
        }

        if ($this->seller_status) {
            return $this->seller_status;
        }

        if ($this->is_approved || $this->email_verified_at) {
            return 'approved';
        }

        return 'pending';
    }

    public function getProfilePhotoUrlAttribute(): string
    {
        if (empty($this->profile_photo_path)) {
            return '';
        }

        return asset('storage/' . ltrim($this->profile_photo_path, '/'));
    }

    // Role check helper
    public function isAdmin(): bool
    {
        return strtolower((string) $this->role) === 'admin';
    }

    // Relationship to Cart
    public function cart()
    {
        return $this->hasOne(Cart::class);
    }

    // Relationship to Orders
    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    // Relationship to Notifications
    public function notifications()
    {
        return $this->hasMany(Notification::class);
    }

    // Relationship to Products (when user is a seller)
    public function products()
    {
        return $this->hasMany(\App\Models\Product::class, 'seller_id');
    }

    // Relationship to Payment Methods
    public function paymentMethods()
    {
        return $this->hasMany(PaymentMethod::class);
    }

    // Get default payment method
    public function defaultPaymentMethod()
    {
        return $this->hasOne(PaymentMethod::class)->where('is_default', true);
    }

    // Relationship to saved addresses
    public function addresses()
    {
        return $this->hasMany(Address::class);
    }

    public function defaultAddress()
    {
        return $this->hasOne(Address::class)->where('is_default', true);
    }

    public function driverApplications()
    {
        return $this->hasMany(DriverApplication::class);
    }

    public function driverShipments()
    {
        return $this->hasMany(\App\Models\Shipment::class, 'driver_id');
    }
}
