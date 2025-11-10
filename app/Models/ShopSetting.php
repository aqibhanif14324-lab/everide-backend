<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ShopSetting extends Model
{
    protected $fillable = [
        'shop_id',
        'currency',
        'shipping_policy',
        'return_policy',
        'logo_url',
    ];

    public function shop(): BelongsTo
    {
        return $this->belongsTo(Shop::class);
    }
}