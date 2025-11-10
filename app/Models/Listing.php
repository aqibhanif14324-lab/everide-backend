<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Listing extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'shop_id',
        'user_id',
        'title',
        'slug',
        'description',
        'category_id',
        'brand',
        'model',
        'year',
        'condition',
        'status',
        'published_at',
        'default_price',
        'currency',
        'location_city',
        'location_country',
    ];

    protected function casts(): array
    {
        return [
            'year' => 'integer',
            'published_at' => 'datetime',
            'default_price' => 'decimal:2',
        ];
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($listing) {
            if (empty($listing->slug)) {
                $listing->slug = Str::slug($listing->title) . '-' . Str::random(6);
            }
        });
    }

    public function shop(): BelongsTo
    {
        return $this->belongsTo(Shop::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function images(): HasMany
    {
        return $this->hasMany(ListingImage::class)->orderBy('position');
    }

    public function variants(): HasMany
    {
        return $this->hasMany(ListingVariant::class);
    }

    public function options(): BelongsToMany
    {
        return $this->belongsToMany(Option::class, 'listing_options');
    }

    public function optionValues(): BelongsToMany
    {
        return $this->belongsToMany(OptionValue::class, 'listing_option_values');
    }

    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }
}