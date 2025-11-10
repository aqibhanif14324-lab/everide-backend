<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderItem extends Model
{
    protected $fillable = [
        'order_id',
        'listing_id',
        'variant_id',
        'title_snapshot',
        'sku_snapshot',
        'unit_price',
        'quantity',
        'line_total',
        'selected_attributes',
    ];

    protected function casts(): array
    {
        return [
            'unit_price' => 'decimal:2',
            'quantity' => 'integer',
            'line_total' => 'decimal:2',
            'selected_attributes' => 'array',
        ];
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function listing(): BelongsTo
    {
        return $this->belongsTo(Listing::class);
    }

    public function variant(): BelongsTo
    {
        return $this->belongsTo(ListingVariant::class);
    }
}