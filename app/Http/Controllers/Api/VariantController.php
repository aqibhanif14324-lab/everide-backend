<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ListingVariant;
use App\Models\VariantImage;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;

class VariantController extends Controller
{
    public function update(Request $request, string $id): JsonResponse
    {
        $variant = ListingVariant::findOrFail($id);
        $this->authorize('update', $variant->listing);

        $validator = Validator::make($request->all(), [
            'price' => ['nullable', 'numeric'],
            'compare_at_price' => ['nullable', 'numeric'],
            'cost_price' => ['nullable', 'numeric'],
            'stock_qty' => ['sometimes', 'integer', 'min:0'],
            'is_default' => ['nullable', 'boolean'],
            'status' => ['nullable', 'in:active,inactive'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'data' => null,
                'errors' => $validator->errors(),
                'meta' => null,
            ], 422);
        }

        $variant->update($request->only(['price', 'compare_at_price', 'cost_price', 'stock_qty', 'is_default', 'status']));

        return response()->json([
            'data' => $variant->load('optionValues'),
            'errors' => null,
            'meta' => null,
        ]);
    }

    public function addImage(Request $request, string $id): JsonResponse
    {
        $variant = ListingVariant::findOrFail($id);
        $this->authorize('update', $variant->listing);

        $validator = Validator::make($request->all(), [
            'url' => ['required', 'url'],
            'position' => ['nullable', 'integer'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'data' => null,
                'errors' => $validator->errors(),
                'meta' => null,
            ], 422);
        }

        $image = VariantImage::create([
            'variant_id' => $variant->id,
            'url' => $request->url,
            'position' => $request->position ?? 0,
        ]);

        return response()->json([
            'data' => $image,
            'errors' => null,
            'meta' => null,
        ], 201);
    }

    public function deleteImage(Request $request, string $id, string $imageId): JsonResponse
    {
        $variant = ListingVariant::findOrFail($id);
        $this->authorize('update', $variant->listing);

        $image = VariantImage::where('variant_id', $variant->id)->findOrFail($imageId);
        $image->delete();

        return response()->json([
            'data' => ['message' => 'Image deleted successfully.'],
            'errors' => null,
            'meta' => null,
        ]);
    }
}