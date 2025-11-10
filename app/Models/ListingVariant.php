<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class ListingVariant extends Model
{
    protected $fillable = [
        'listing_id',
        'sku',
        'price',
        'compare_at_price',
        'cost_price',
        'stock_qty',
        'is_default',
        'weight_grams',
        'dimensions',
        'barcode',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'compare_at_price' => 'decimal:2',
            'cost_price' => 'decimal:2',
            'stock_qty' => 'integer',
            'is_default' => 'boolean',
            'weight_grams' => 'integer',
            'dimensions' => 'array',
        ];
    }

    public function listing(): BelongsTo
    {
        return $this->belongsTo(Listing::class);
    }

    public function optionValues(): BelongsToMany
    {
        return $this->belongsToMany(OptionValue::class, 'variant_option_values');
    }

    public function images(): HasMany
    {
        return $this->hasMany(VariantImage::class)->orderBy('position');
    }

    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function getPriceAttribute($value)
    {
        return $value ?? $this->listing->default_price;
    }
}