<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Str;

class Option extends Model
{
    protected $fillable = [
        'name',
        'slug',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($option) {
            if (empty($option->slug)) {
                $option->slug = Str::slug($option->name);
            }
        });
    }

    public function values(): HasMany
    {
        return $this->hasMany(OptionValue::class);
    }

    public function listings(): BelongsToMany
    {
        return $this->belongsToMany(Listing::class, 'listing_options');
    }
}