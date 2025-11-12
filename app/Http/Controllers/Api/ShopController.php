<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Shop;
use App\Models\ShopSetting;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class ShopController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $shops = Shop::with('owner')
            ->where('status', 'approved')
            ->paginate(15);

        return response()->json([
            'data' => $shops->items(),
            'errors' => null,
            'meta' => [
                'current_page' => $shops->currentPage(),
                'last_page' => $shops->lastPage(),
                'per_page' => $shops->perPage(),
                'total' => $shops->total(),
            ],
        ]);
    }

    public function show(string $slug): JsonResponse
    {
        $shop = Shop::with(['owner', 'settings'])->where('slug', $slug)->firstOrFail();
        $this->authorize('view', $shop);
        
        return response()->json([
            'data' => $shop,
            'errors' => null,
            'meta' => null,
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $this->authorize('create', Shop::class);

        $validator = Validator::make($request->all(), [
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'city' => ['nullable', 'string'],
            'country' => ['nullable', 'string'],
            'cover_image_url' => ['nullable', 'url'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'data' => null,
                'errors' => $validator->errors(),
                'meta' => null,
            ], 422);
        }

        // Check if user already has a shop
        $existingShop = Shop::where('owner_id', $request->user()->id)->first();
        if ($existingShop) {
            return response()->json([
                'data' => null,
                'errors' => ['message' => 'You already have a shop.'],
                'meta' => null,
            ], 422);
        }

        $shop = Shop::create([
            'owner_id' => $request->user()->id,
            'name' => $request->name,
            'slug' => Str::slug($request->name) . '-' . Str::random(6),
            'description' => $request->description,
            'city' => $request->city,
            'country' => $request->country,
            'cover_image_url' => $request->cover_image_url,
            'status' => 'pending',
        ]);

        // Create default settings
        ShopSetting::create([
            'shop_id' => $shop->id,
            'currency' => 'EUR',
        ]);

        return response()->json([
            'data' => $shop->load(['owner', 'settings']),
            'errors' => null,
            'meta' => null,
        ], 201);
    }

    public function update(Request $request, string $id): JsonResponse
    {
        $shop = Shop::findOrFail($id);
        Gate::authorize('shop.manage_own', $shop);
        $this->authorize('update', $shop);

        $validator = Validator::make($request->all(), [
            'name' => ['sometimes', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'city' => ['nullable', 'string'],
            'country' => ['nullable', 'string'],
            'cover_image_url' => ['nullable', 'url'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'data' => null,
                'errors' => $validator->errors(),
                'meta' => null,
            ], 422);
        }

        $shop->update($request->only(['name', 'description', 'city', 'country', 'cover_image_url']));

        return response()->json([
            'data' => $shop->load(['owner', 'settings']),
            'errors' => null,
            'meta' => null,
        ]);
    }

    public function destroy(string $id): JsonResponse
    {
        $shop = Shop::findOrFail($id);
        Gate::authorize('shop.manage_own', $shop);
        $this->authorize('delete', $shop);

        $shop->delete();

        return response()->json([
            'data' => ['message' => 'Shop deleted successfully.'],
            'errors' => null,
            'meta' => null,
        ]);
    }

    public function listings(Request $request, string $slug): JsonResponse
    {
        $shop = Shop::where('slug', $slug)->firstOrFail();
        $listings = $shop->listings()
            ->where('status', 'published')
            ->with(['category', 'images', 'variants'])
            ->paginate(15);

        return response()->json([
            'data' => $listings->items(),
            'errors' => null,
            'meta' => [
                'current_page' => $listings->currentPage(),
                'last_page' => $listings->lastPage(),
                'per_page' => $listings->perPage(),
                'total' => $listings->total(),
            ],
        ]);
    }

    public function getSettings(Request $request, string $id): JsonResponse
    {
        $shop = Shop::findOrFail($id);
        Gate::authorize('shop.manage_own', $shop);
        $this->authorize('update', $shop);

        return response()->json([
            'data' => $shop->settings,
            'errors' => null,
            'meta' => null,
        ]);
    }

    public function updateSettings(Request $request, string $id): JsonResponse
    {
        $shop = Shop::findOrFail($id);
        Gate::authorize('shop.manage_own', $shop);
        $this->authorize('update', $shop);

        $validator = Validator::make($request->all(), [
            'currency' => ['sometimes', 'string'],
            'shipping_policy' => ['nullable', 'string'],
            'return_policy' => ['nullable', 'string'],
            'logo_url' => ['nullable', 'url'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'data' => null,
                'errors' => $validator->errors(),
                'meta' => null,
            ], 422);
        }

        $settings = $shop->settings ?? ShopSetting::create(['shop_id' => $shop->id]);
        $settings->update($request->only(['currency', 'shipping_policy', 'return_policy', 'logo_url']));

        return response()->json([
            'data' => $settings,
            'errors' => null,
            'meta' => null,
        ]);
    }
}