<?php

namespace Database\Seeders;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Listing;
use App\Models\ListingVariant;
use App\Models\User;
use App\Models\Shop;
use Illuminate\Database\Seeder;

class OrderSeeder extends Seeder
{
    public function run(): void
    {
        $buyers = User::whereHas('role', function($q) {
            $q->where('slug', 'user');
        })->take(5)->get();

        $shops = Shop::all();
        $listings = Listing::with('variants')->where('status', 'published')->get();

        if ($listings->isEmpty() || $buyers->isEmpty()) {
            return;
        }

        // Create a few orders
        for ($i = 0; $i < 5; $i++) {
            $buyer = $buyers->random();
            $shop = $shops->random();
            $shopListings = $listings->where('shop_id', $shop->id)->take(rand(1, 3));

            if ($shopListings->isEmpty()) {
                continue;
            }

            $subtotal = 0;
            $order = Order::create([
                'buyer_id' => $buyer->id,
                'shop_id' => $shop->id,
                'status' => ['pending', 'paid', 'shipped', 'delivered'][array_rand(['pending', 'paid', 'shipped', 'delivered'])],
                'subtotal' => 0,
                'shipping_fee' => 5.99,
                'tax' => 0,
                'total' => 0,
                'currency' => 'EUR',
            ]);

            foreach ($shopListings as $listing) {
                $variant = $listing->variants->first();
                $quantity = rand(1, 3);
                $price = $variant ? ($variant->price ?? $listing->default_price) : $listing->default_price;
                $lineTotal = $price * $quantity;
                $subtotal += $lineTotal;

                $selectedAttributes = [];
                if ($variant) {
                    $optionValues = $variant->optionValues()->with('option')->get();
                    foreach ($optionValues as $optionValue) {
                        $selectedAttributes[$optionValue->option->name] = $optionValue->value;
                    }
                }

                OrderItem::create([
                    'order_id' => $order->id,
                    'listing_id' => $listing->id,
                    'variant_id' => $variant?->id,
                    'title_snapshot' => $listing->title,
                    'sku_snapshot' => $variant?->sku ?? 'N/A',
                    'unit_price' => $price,
                    'quantity' => $quantity,
                    'line_total' => $lineTotal,
                    'selected_attributes' => $selectedAttributes,
                ]);
            }

            $total = $subtotal + $order->shipping_fee;
            $order->update([
                'subtotal' => $subtotal,
                'total' => $total,
            ]);
        }
    }
}