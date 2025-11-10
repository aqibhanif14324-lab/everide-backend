<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Str;

class OptionValue extends Model
{
    protected $fillable = [
        'option_id',
        'value',
        'slug',
        'meta',
    ];

    protected function casts(): array
    {
        return [
            'meta' => 'array',
        ];
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($optionValue) {
            if (empty($optionValue->slug)) {
                $optionValue->slug = Str::slug($optionValue->value) . '-' . Str::random(4);
            }
        });
    }

    public function option(): BelongsTo
    {
        return $this->belongsTo(Option::class);
    }

    public function listings(): BelongsToMany
    {
        return $this->belongsToMany(Listing::class, 'listing_option_values');
    }

    public function variants(): BelongsToMany
    {
        return $this->belongsToMany(ListingVariant::class, 'variant_option_values');
    }
}