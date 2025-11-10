<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['name' => 'Bicycle Parts', 'slug' => 'bicycle-parts'],
            ['name' => 'Motorcycle Parts', 'slug' => 'motorcycle-parts'],
            ['name' => 'Car Parts', 'slug' => 'car-parts'],
            ['name' => 'Wheels & Tires', 'slug' => 'wheels-tires'],
            ['name' => 'Brakes', 'slug' => 'brakes'],
            ['name' => 'Engine Parts', 'slug' => 'engine-parts'],
            ['name' => 'Body Parts', 'slug' => 'body-parts'],
            ['name' => 'Accessories', 'slug' => 'accessories'],
        ];

        foreach ($categories as $category) {
            Category::firstOrCreate(['slug' => $category['slug']], $category);
        }

        // Subcategories
        $wheelsCategory = Category::where('slug', 'wheels-tires')->first();
        if ($wheelsCategory) {
            Category::firstOrCreate(
                ['slug' => 'bicycle-wheels'],
                [
                    'name' => 'Bicycle Wheels',
                    'parent_id' => $wheelsCategory->id,
                ]
            );
        }
    }
}