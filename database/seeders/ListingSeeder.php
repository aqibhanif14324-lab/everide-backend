<?php

namespace Database\Seeders;

use App\Models\Listing;
use App\Models\ListingImage;
use App\Models\Shop;
use App\Models\Category;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ListingSeeder extends Seeder
{
    public function run(): void
    {
        $shops = Shop::all();
        $categories = Category::all();

        $listings = [
            ['title' => 'Mountain Bike Frame', 'brand' => 'Trek', 'model' => 'X-Caliber', 'year' => 2023, 'price' => 299.99],
            ['title' => 'Road Bike Wheels', 'brand' => 'Shimano', 'model' => 'WH-R501', 'year' => 2022, 'price' => 149.99],
            ['title' => 'Bicycle Helmet', 'brand' => 'Giro', 'model' => 'Synthe', 'year' => 2023, 'price' => 89.99],
            ['title' => 'Bike Chain', 'brand' => 'SRAM', 'model' => 'PC-1110', 'year' => 2023, 'price' => 24.99],
            ['title' => 'Bicycle Pedals', 'brand' => 'Shimano', 'model' => 'PD-M520', 'year' => 2022, 'price' => 39.99],
            ['title' => 'Motorcycle Exhaust', 'brand' => 'Akrapovic', 'model' => 'Slip-On', 'year' => 2023, 'price' => 599.99],
            ['title' => 'Car Brake Pads', 'brand' => 'Brembo', 'model' => 'P06074', 'year' => 2023, 'price' => 79.99],
            ['title' => 'Bicycle Saddle', 'brand' => 'Brooks', 'model' => 'B17', 'year' => 2023, 'price' => 129.99],
            ['title' => 'Bike Light Set', 'brand' => 'Cateye', 'model' => 'AMPP 800', 'year' => 2023, 'price' => 49.99],
            ['title' => 'Bicycle Lock', 'brand' => 'Kryptonite', 'model' => 'New York Lock', 'year' => 2023, 'price' => 69.99],
        ];

        foreach ($listings as $index => $listingData) {
            $shop = $shops->random();
            $category = $categories->random();
            $user = $shop->owner;

            $listing = Listing::create([
                'shop_id' => $shop->id,
                'user_id' => $user->id,
                'title' => $listingData['title'],
                'slug' => Str::slug($listingData['title']) . '-' . Str::random(6),
                'description' => "High-quality {$listingData['title']} in excellent condition. Perfect for your needs.",
                'category_id' => $category->id,
                'brand' => $listingData['brand'],
                'model' => $listingData['model'],
                'year' => $listingData['year'],
                'condition' => ['new', 'very_good', 'good'][array_rand(['new', 'very_good', 'good'])],
                'status' => 'published',
                'published_at' => now(),
                'default_price' => $listingData['price'],
                'currency' => 'EUR',
                'location_city' => 'Paris',
                'location_country' => 'France',
            ]);

            // Add images
            for ($i = 0; $i < 3; $i++) {
                ListingImage::create([
                    'listing_id' => $listing->id,
                    'url' => "https://picsum.photos/800/600?random=" . ($index * 10 + $i),
                    'position' => $i,
                ]);
            }
        }

        // Create more listings (30 total)
        for ($i = count($listings); $i < 30; $i++) {
            $shop = $shops->random();
            $category = $categories->random();
            $user = $shop->owner;

            $listing = Listing::create([
                'shop_id' => $shop->id,
                'user_id' => $user->id,
                'title' => "Product " . ($i + 1),
                'slug' => 'product-' . ($i + 1) . '-' . Str::random(6),
                'description' => "Description for product " . ($i + 1),
                'category_id' => $category->id,
                'brand' => 'Generic',
                'condition' => ['new', 'very_good', 'good'][array_rand(['new', 'very_good', 'good'])],
                'status' => 'published',
                'published_at' => now(),
                'default_price' => rand(10, 500) + (rand(0, 99) / 100),
                'currency' => 'EUR',
                'location_city' => 'Paris',
                'location_country' => 'France',
            ]);

            ListingImage::create([
                'listing_id' => $listing->id,
                'url' => "https://picsum.photos/800/600?random=" . ($i + 100),
                'position' => 0,
            ]);
        }
    }
}