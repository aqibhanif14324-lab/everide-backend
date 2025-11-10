<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VariantImage extends Model
{
    protected $fillable = [
        'variant_id',
        'url',
        'position',
    ];

    protected function casts(): array
    {
        return [
            'position' => 'integer',
        ];
    }

    public function variant(): BelongsTo
    {
        return $this->belongsTo(ListingVariant::class);
    }
}