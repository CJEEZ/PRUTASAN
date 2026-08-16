<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'name',
        'price',
        'description',
        'image_url',
        'category_id',
        'unit',
        'stock',
        'seller_id',
        'is_seasonal', // Added for filters
        'is_exotic',   // Added for filters
        'is_arindo',
        'arindo_status',
        'loan_amount',
        'term_years',
        'expiration_date',
        'location',
        'map_location',
        'crop_yield_description',
        'land_photo_urls',
        'soil_report_url',
        'legal_document_url',
        'arindo_verified_at',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'price' => 'decimal:2',
        'is_seasonal' => 'boolean',
        'is_exotic' => 'boolean',
        'is_arindo' => 'boolean',
        'land_photo_urls' => 'array',
        'expiration_date' => 'date',
        'arindo_verified_at' => 'datetime',
    ];

    /**
     * A product belongs to a Category.
     */
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * A product belongs to a seller (user)
     */
    public function seller()
    {
        return $this->belongsTo(\App\Models\User::class, 'seller_id');
    }
}
