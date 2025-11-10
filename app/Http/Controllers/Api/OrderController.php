<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Listing;
use App\Models\ListingVariant;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();
        
        $query = Order::with(['shop', 'items.listing', 'items.variant']);

        if ($user->isAdmin() || $user->isModerator()) {
            // Admins and moderators can see all orders
        } else {
            // Users see their own orders or orders from their shops
            $query->where(function($q) use ($user) {
                $q->where('buyer_id', $user->id)
                  ->orWhereHas('shop', function($shopQuery) use ($user) {
                      $shopQuery->where('owner_id', $user->id);
                  });
            });
        }

        $orders = $query->paginate(15);

        return response()->json([
            'data' => $orders->items(),
            'errors' => null,
            'meta' => [
                'current_page' => $orders->currentPage(),
                'last_page' => $orders->lastPage(),
                'per_page' => $orders->perPage(),
                'total' => $orders->total(),
            ],
        ]);
    }

    public function show(Request $request, string $id): JsonResponse
    {
        $order = Order::with(['shop', 'items.listing', 'items.variant', 'payment', 'shipment'])->findOrFail($id);
        
        $this->authorize('view', $order);

        return response()->json([
            'data' => $order,
            'errors' => null,
            'meta' => null,
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $this->authorize('create', Order::class);

        $validator = Validator::make($request->all(), [
            'items' => ['required', 'array', 'min:1'],
            'items.*.listing_id' => ['required', 'exists:listings,id'],
            'items.*.variant_id' => ['nullable', 'exists:listing_variants,id'],
            'items.*.quantity' => ['required', 'integer', 'min:1'],
            'shipping_fee' => ['nullable', 'numeric', 'min:0'],
            'address_id' => ['nullable', 'exists:addresses,id'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'data' => null,
                'errors' => $validator->errors(),
                'meta' => null,
            ], 422);
        }

        DB::beginTransaction();
        try {
            $subtotal = 0;
            $items = [];
            $shopId = null;

            foreach ($request->items as $itemData) {
                $listing = Listing::findOrFail($itemData['listing_id']);
                $variant = isset($itemData['variant_id']) ? ListingVariant::find($itemData['variant_id']) : null;
                
                if ($shopId === null) {
                    $shopId = $listing->shop_id;
                } elseif ($shopId !== $listing->shop_id) {
                    throw new \Exception('All items must be from the same shop.');
                }

                $quantity = $itemData['quantity'];
                $price = $variant ? ($variant->price ?? $listing->default_price) : $listing->default_price;
                
                // Check stock
                if ($variant && $variant->stock_qty < $quantity) {
                    throw new \Exception("Insufficient stock for variant {$variant->sku}.");
                }

                $lineTotal = $price * $quantity;
                $subtotal += $lineTotal;

                // Get selected attributes
                $selectedAttributes = [];
                if ($variant) {
                    $optionValues = $variant->optionValues()->with('option')->get();
                    foreach ($optionValues as $optionValue) {
                        $selectedAttributes[$optionValue->option->name] = $optionValue->value;
                    }
                }

                $items[] = [
                    'listing' => $listing,
                    'variant' => $variant,
                    'quantity' => $quantity,
                    'price' => $price,
                    'line_total' => $lineTotal,
                    'selected_attributes' => $selectedAttributes,
                ];
            }

            $shippingFee = $request->shipping_fee ?? 0;
            $tax = 0; // Calculate tax if needed
            $total = $subtotal + $shippingFee + $tax;

            $order = Order::create([
                'buyer_id' => $request->user()->id,
                'shop_id' => $shopId,
                'status' => 'pending',
                'subtotal' => $subtotal,
                'shipping_fee' => $shippingFee,
                'tax' => $tax,
                'total' => $total,
                'currency' => 'EUR',
            ]);

            foreach ($items as $item) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'listing_id' => $item['listing']->id,
                    'variant_id' => $item['variant']?->id,
                    'title_snapshot' => $item['listing']->title,
                    'sku_snapshot' => $item['variant']?->sku ?? 'N/A',
                    'unit_price' => $item['price'],
                    'quantity' => $item['quantity'],
                    'line_total' => $item['line_total'],
                    'selected_attributes' => $item['selected_attributes'],
                ]);

                // Update stock
                if ($item['variant']) {
                    $item['variant']->decrement('stock_qty', $item['quantity']);
                }
            }

            DB::commit();

            return response()->json([
                'data' => $order->load(['shop', 'items.listing', 'items.variant']),
                'errors' => null,
                'meta' => null,
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'data' => null,
                'errors' => ['message' => $e->getMessage()],
                'meta' => null,
            ], 422);
        }
    }

    public function updateStatus(Request $request, string $id): JsonResponse
    {
        $order = Order::findOrFail($id);
        $this->authorize('updateStatus', $order);

        $validator = Validator::make($request->all(), [
            'status' => ['required', 'in:pending,awaiting_payment,paid,shipped,delivered,refunded,cancelled'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'data' => null,
                'errors' => $validator->errors(),
                'meta' => null,
            ], 422);
        }

        $order->update([
            'status' => $request->status,
            'shipped_at' => $request->status === 'shipped' ? now() : $order->shipped_at,
            'delivered_at' => $request->status === 'delivered' ? now() : $order->delivered_at,
        ]);

        return response()->json([
            'data' => $order->load(['shop', 'items']),
            'errors' => null,
            'meta' => null,
        ]);
    }
}