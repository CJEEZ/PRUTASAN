<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Models\PaymentMethod;
use App\Models\Address;

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
        'deleted_at' => 'datetime',
        'password' => 'hashed',
    ];

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

    // Role check helper
    public function isAdmin(): bool
    {
        return $this->role === 'admin' && strtolower((string) $this->email) === 'admin@fruitexpress.com';
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
}
