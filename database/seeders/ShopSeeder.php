<?php

namespace Database\Seeders;

use App\Enums\UserRole;
use App\Models\Shop;
use App\Models\ShopSetting;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ShopSeeder extends Seeder
{
    public function run(): void
    {
        $users = User::where('role', UserRole::USER)->take(5)->get();

        $shopNames = [
            'BikeParts Pro',
            'Motorcycle Warehouse',
            'Auto Parts Direct',
            'Cycle World',
            'Parts Express',
        ];

        foreach ($users as $index => $user) {
            if (isset($shopNames[$index])) {
                $shop = Shop::firstOrCreate(
                    ['owner_id' => $user->id],
                    [
                        'name' => $shopNames[$index],
                        'slug' => Str::slug($shopNames[$index]),
                        'description' => "Quality parts and accessories for your vehicle needs.",
                        'city' => 'Paris',
                        'country' => 'France',
                        'cover_image_url' => 'https://picsum.photos/800/400?random=' . ($index + 1),
                        'status' => 'approved',
                        'approved_at' => now(),
                    ]
                );

                if (! $shop->owner->isSeller()) {
                    $shop->owner->forceFill(['role' => UserRole::SELLER])->save();
                }

                // Create shop settings
                ShopSetting::firstOrCreate(
                    ['shop_id' => $shop->id],
                    [
                        'currency' => 'EUR',
                        'shipping_policy' => 'Free shipping on orders over €50',
                        'return_policy' => '30-day return policy',
                    ]
                );
            }
        }
    }
}