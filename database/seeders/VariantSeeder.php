<?php

namespace Database\Seeders;

use App\Models\Listing;
use App\Models\ListingVariant;
use App\Models\Option;
use App\Models\OptionValue;
use Illuminate\Database\Seeder;

class VariantSeeder extends Seeder
{
    public function run(): void
    {
        $listings = Listing::all();
        $colorOption = Option::where('slug', 'color')->first();
        $sizeOption = Option::where('slug', 'size')->first();

        if (!$colorOption || !$sizeOption) {
            return;
        }

        $colors = OptionValue::where('option_id', $colorOption->id)->get();
        $sizes = OptionValue::where('option_id', $sizeOption->id)->get();

        foreach ($listings->take(10) as $listing) {
            // Attach options to listing
            $listing->options()->sync([$colorOption->id, $sizeOption->id]);
            
            // Attach option values
            $listing->optionValues()->sync($colors->pluck('id')->merge($sizes->pluck('id'))->toArray());

            // Create variants with combinations
            $variantCount = 0;
            foreach ($colors->take(2) as $color) {
                foreach ($sizes->take(2) as $size) {
                    $variant = ListingVariant::create([
                        'listing_id' => $listing->id,
                        'sku' => strtoupper(substr($listing->slug, 0, 5)) . '-' . strtoupper($color->value) . '-' . strtoupper($size->value),
                        'price' => $listing->default_price + ($variantCount * 10),
                        'stock_qty' => rand(5, 50),
                        'is_default' => $variantCount === 0,
                        'status' => 'active',
                    ]);

                    $variant->optionValues()->sync([$color->id, $size->id]);
                    $variantCount++;
                }
            }
        }

        // For remaining listings, create simple variants without options
        foreach ($listings->skip(10) as $listing) {
            ListingVariant::create([
                'listing_id' => $listing->id,
                'sku' => strtoupper(substr($listing->slug, 0, 8)) . '-DEFAULT',
                'price' => $listing->default_price,
                'stock_qty' => rand(5, 50),
                'is_default' => true,
                'status' => 'active',
            ]);
        }
    }
}