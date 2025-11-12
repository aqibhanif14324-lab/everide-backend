<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Listing;
use App\Models\ListingImage;
use App\Models\Option;
use App\Models\OptionValue;
use App\Models\ListingVariant;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class ListingController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = Listing::with(['shop.owner', 'category', 'images', 'variants'])
            ->where('status', 'published');

        // Filters
        if ($request->has('q')) {
            $query->where(function($q) use ($request) {
                $q->where('title', 'like', '%' . $request->q . '%')
                  ->orWhere('description', 'like', '%' . $request->q . '%');
            });
        }

        if ($request->has('category')) {
            $query->where('category_id', $request->category);
        }

        if ($request->has('brand')) {
            $query->where('brand', $request->brand);
        }

        if ($request->has('model')) {
            $query->where('model', $request->model);
        }

        if ($request->has('year')) {
            $query->where('year', $request->year);
        }

        if ($request->has('min_price')) {
            $query->where('default_price', '>=', $request->min_price);
        }

        if ($request->has('max_price')) {
            $query->where('default_price', '<=', $request->max_price);
        }

        if ($request->has('location')) {
            $query->where('location_city', 'like', '%' . $request->location . '%');
        }

        // Sort
        $sort = $request->get('sort', 'published_at');
        $direction = $request->get('direction', 'desc');
        $query->orderBy($sort, $direction);

        $listings = $query->paginate($request->get('per_page', 15));

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

    public function show(string $slug): JsonResponse
    {
        $listing = Listing::with([
            'shop.owner',
            'category',
            'images',
            'options.values',
            'optionValues',
            'variants.optionValues.option',
            'variants.images',
        ])->where('slug', $slug)->firstOrFail();

        $this->authorize('view', $listing);

        return response()->json([
            'data' => $listing,
            'errors' => null,
            'meta' => null,
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        Gate::authorize('listing.create');
        $this->authorize('create', Listing::class);

        $validator = Validator::make($request->all(), [
            'shop_id' => ['required', 'exists:shops,id'],
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'category_id' => ['nullable', 'exists:categories,id'],
            'brand' => ['nullable', 'string'],
            'model' => ['nullable', 'string'],
            'year' => ['nullable', 'integer'],
            'condition' => ['nullable', 'in:new,very_good,good,for_parts'],
            'default_price' => ['required', 'numeric', 'min:0'],
            'currency' => ['nullable', 'string'],
            'location_city' => ['nullable', 'string'],
            'location_country' => ['nullable', 'string'],
            'images' => ['nullable', 'array'],
            'images.*' => ['url'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'data' => null,
                'errors' => $validator->errors(),
                'meta' => null,
            ], 422);
        }

        $listing = Listing::create([
            'shop_id' => $request->shop_id,
            'user_id' => $request->user()->id,
            'title' => $request->title,
            'slug' => Str::slug($request->title) . '-' . Str::random(6),
            'description' => $request->description,
            'category_id' => $request->category_id,
            'brand' => $request->brand,
            'model' => $request->model,
            'year' => $request->year,
            'condition' => $request->condition ?? 'good',
            'default_price' => $request->default_price,
            'currency' => $request->currency ?? 'EUR',
            'location_city' => $request->location_city,
            'location_country' => $request->location_country,
            'status' => 'draft',
        ]);

        // Add images
        if ($request->has('images')) {
            foreach ($request->images as $index => $url) {
                ListingImage::create([
                    'listing_id' => $listing->id,
                    'url' => $url,
                    'position' => $index,
                ]);
            }
        }

        return response()->json([
            'data' => $listing->load(['shop', 'category', 'images']),
            'errors' => null,
            'meta' => null,
        ], 201);
    }

    public function update(Request $request, string $id): JsonResponse
    {
        $listing = Listing::findOrFail($id);
        Gate::authorize('listing.update', $listing);
        $this->authorize('update', $listing);

        $validator = Validator::make($request->all(), [
            'title' => ['sometimes', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'category_id' => ['nullable', 'exists:categories,id'],
            'brand' => ['nullable', 'string'],
            'model' => ['nullable', 'string'],
            'year' => ['nullable', 'integer'],
            'condition' => ['nullable', 'in:new,very_good,good,for_parts'],
            'default_price' => ['sometimes', 'numeric', 'min:0'],
            'currency' => ['nullable', 'string'],
            'location_city' => ['nullable', 'string'],
            'location_country' => ['nullable', 'string'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'data' => null,
                'errors' => $validator->errors(),
                'meta' => null,
            ], 422);
        }

        $listing->update($request->only([
            'title', 'description', 'category_id', 'brand', 'model', 'year',
            'condition', 'default_price', 'currency', 'location_city', 'location_country',
        ]));

        return response()->json([
            'data' => $listing->load(['shop', 'category', 'images']),
            'errors' => null,
            'meta' => null,
        ]);
    }

    public function destroy(string $id): JsonResponse
    {
        $listing = Listing::findOrFail($id);
        Gate::authorize('listing.update', $listing);
        $this->authorize('delete', $listing);

        $listing->delete();

        return response()->json([
            'data' => ['message' => 'Listing deleted successfully.'],
            'errors' => null,
            'meta' => null,
        ]);
    }

    public function publish(Request $request, string $id): JsonResponse
    {
        $listing = Listing::findOrFail($id);
        Gate::authorize('listing.update', $listing);
        $this->authorize('publish', $listing);

        $listing->update([
            'status' => 'published',
            'published_at' => now(),
        ]);

        return response()->json([
            'data' => $listing,
            'errors' => null,
            'meta' => null,
        ]);
    }

    public function archive(Request $request, string $id): JsonResponse
    {
        $listing = Listing::findOrFail($id);
        Gate::authorize('listing.archive', $listing);
        $this->authorize('archive', $listing);

        $listing->update(['status' => 'archived']);

        return response()->json([
            'data' => $listing,
            'errors' => null,
            'meta' => null,
        ]);
    }

    public function attachOptions(Request $request, string $id): JsonResponse
    {
        $listing = Listing::findOrFail($id);
        Gate::authorize('listing.update', $listing);
        $this->authorize('update', $listing);

        $validator = Validator::make($request->all(), [
            'option_ids' => ['required', 'array'],
            'option_ids.*' => ['exists:options,id'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'data' => null,
                'errors' => $validator->errors(),
                'meta' => null,
            ], 422);
        }

        $listing->options()->sync($request->option_ids);

        return response()->json([
            'data' => $listing->load('options'),
            'errors' => null,
            'meta' => null,
        ]);
    }

    public function attachOptionValues(Request $request, string $id): JsonResponse
    {
        $listing = Listing::findOrFail($id);
        Gate::authorize('listing.update', $listing);
        $this->authorize('update', $listing);

        $validator = Validator::make($request->all(), [
            'option_value_ids' => ['required', 'array'],
            'option_value_ids.*' => ['exists:option_values,id'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'data' => null,
                'errors' => $validator->errors(),
                'meta' => null,
            ], 422);
        }

        $listing->optionValues()->sync($request->option_value_ids);

        return response()->json([
            'data' => $listing->load('optionValues'),
            'errors' => null,
            'meta' => null,
        ]);
    }

    public function createVariant(Request $request, string $id): JsonResponse
    {
        $listing = Listing::findOrFail($id);
        Gate::authorize('listing.update', $listing);
        $this->authorize('update', $listing);

        $validator = Validator::make($request->all(), [
            'sku' => ['required', 'string', 'unique:listing_variants,sku'],
            'price' => ['nullable', 'numeric'],
            'compare_at_price' => ['nullable', 'numeric'],
            'cost_price' => ['nullable', 'numeric'],
            'stock_qty' => ['required', 'integer', 'min:0'],
            'is_default' => ['nullable', 'boolean'],
            'weight_grams' => ['nullable', 'integer'],
            'dimensions' => ['nullable', 'array'],
            'barcode' => ['nullable', 'string'],
            'option_value_ids' => ['required', 'array'],
            'option_value_ids.*' => ['exists:option_values,id'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'data' => null,
                'errors' => $validator->errors(),
                'meta' => null,
            ], 422);
        }

        $variant = ListingVariant::create([
            'listing_id' => $listing->id,
            'sku' => $request->sku,
            'price' => $request->price,
            'compare_at_price' => $request->compare_at_price,
            'cost_price' => $request->cost_price,
            'stock_qty' => $request->stock_qty,
            'is_default' => $request->is_default ?? false,
            'weight_grams' => $request->weight_grams,
            'dimensions' => $request->dimensions,
            'barcode' => $request->barcode,
            'status' => 'active',
        ]);

        $variant->optionValues()->sync($request->option_value_ids);

        return response()->json([
            'data' => $variant->load('optionValues'),
            'errors' => null,
            'meta' => null,
        ], 201);
    }

    public function getVariants(string $id): JsonResponse
    {
        $listing = Listing::findOrFail($id);
        Gate::authorize('listing.update', $listing);
        $variants = $listing->variants()->with('optionValues.option')->get();

        return response()->json([
            'data' => $variants,
            'errors' => null,
            'meta' => null,
        ]);
    }
}