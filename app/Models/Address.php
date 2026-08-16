<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Address extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'full_name',
        'phone',
        'street_address',
        'barangay',
        'city',
        'province',
        'postal_code',
        'address_type',
        'is_default',
    ];

    protected $casts = [
        'is_default' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getFullAddressAttribute()
    {
        return $this->street_address . ', ' . $this->barangay . ', ' . $this->city . ', ' . $this->province . ' ' . $this->postal_code;
    }

    public function getAddressTypeLabelAttribute()
    {
        return ucfirst($this->address_type);
    }

    public function scopeDefault($query)
    {
        return $query->where('is_default', true);
    }

    public function scopeHome($query)
    {
        return $query->where('address_type', 'home');
    }

    public function scopeWork($query)
    {
        return $query->where('address_type', 'work');
    }
}