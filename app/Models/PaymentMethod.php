<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaymentMethod extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'type',
        'card_type',
        'card_holder_name',
        'card_number',
        'card_last_four',
        'card_brand',
        'expiry_month',
        'expiry_year',
        'cvv',
        'bank_name',
        'account_name',
        'account_number',
        'is_default',
    ];

    protected $hidden = [
        'cvv',
    ];

    protected $casts = [
        'is_default' => 'boolean',
        'expiry_month' => 'integer',
        'expiry_year' => 'integer',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getCardTypeIconAttribute()
    {
        return match($this->card_type) {
            'visa' => '💳',
            'mastercard' => '💳',
            'amex' => '💳',
            'discover' => '💳',
            default => '💳',
        };
    }

    public function getMaskedCardNumberAttribute()
    {
        return '**** **** **** ' . substr($this->card_number, -4);
    }

    public function getExpiryDateAttribute()
    {
        return str_pad($this->expiry_month, 2, '0', STR_PAD_LEFT) . '/' . $this->expiry_year;
    }

    public function scopeDefault($query)
    {
        return $query->where('is_default', true);
    }
}
