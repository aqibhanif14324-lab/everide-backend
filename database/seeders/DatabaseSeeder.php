<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            AdminUserSeeder::class,
            CategorySeeder::class,
            ShopSeeder::class,
            OptionSeeder::class,
            ListingSeeder::class,
            VariantSeeder::class,
            OrderSeeder::class,
        ]);
    }
}