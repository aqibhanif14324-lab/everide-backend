<?php

namespace Database\Seeders;

use App\Models\Option;
use App\Models\OptionValue;
use Illuminate\Database\Seeder;

class OptionSeeder extends Seeder
{
    public function run(): void
    {
        // Color option
        $colorOption = Option::firstOrCreate(
            ['slug' => 'color'],
            ['name' => 'Color']
        );

        $colors = ['Red', 'Blue', 'Black', 'White', 'Green', 'Yellow'];
        foreach ($colors as $color) {
            OptionValue::firstOrCreate(
                [
                    'option_id' => $colorOption->id,
                    'slug' => strtolower($color),
                ],
                ['value' => $color]
            );
        }

        // Size option
        $sizeOption = Option::firstOrCreate(
            ['slug' => 'size'],
            ['name' => 'Size']
        );

        $sizes = ['S', 'M', 'L', 'XL', 'XXL'];
        foreach ($sizes as $size) {
            OptionValue::firstOrCreate(
                [
                    'option_id' => $sizeOption->id,
                    'slug' => strtolower($size),
                ],
                ['value' => $size]
            );
        }

        // Material option
        $materialOption = Option::firstOrCreate(
            ['slug' => 'material'],
            ['name' => 'Material']
        );

        $materials = ['Steel', 'Aluminum', 'Carbon Fiber', 'Titanium'];
        foreach ($materials as $material) {
            OptionValue::firstOrCreate(
                [
                    'option_id' => $materialOption->id,
                    'slug' => strtolower(str_replace(' ', '-', $material)),
                ],
                ['value' => $material]
            );
        }
    }
}