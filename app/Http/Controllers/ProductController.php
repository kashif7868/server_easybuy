<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    // Create a new product
// Store a new product
public function store(StoreProductRequest $request)
{
    // Handle the main image upload
    $imagePath = $request->file('images')->store('products', 'public');

    // Handle additional image uploads (if provided)
    $additionalImagesPaths = [];
    if ($request->hasFile('additional_images')) {
        foreach ($request->file('additional_images') as $image) {
            $additionalImagesPaths[] = $image->store('products', 'public');
        }
    }

    // Create new product
    $product = Product::create([
        'name' => $request->name,
        'price' => $request->price,
        'discount_price' => $request->discount_price,
        'discount_percentage' => (($request->price - $request->discount_price) / $request->price) * 100,
        'rating' => $request->rating,
        'reviews' => $request->reviews,
        'description' => $request->description,
        'images' => $imagePath,
        'additional_images' => $additionalImagesPaths,
        'color' => $request->color,
        'brand' => $request->brand,
        'meter' => $request->meter,
        'size' => $request->size,
        'items_stock' => $request->items_stock,
        'category_id' => $request->category_id,
        'subcategory_id' => $request->subcategory_id,
        'small_category_id' => $request->small_category_id,
        'featured' => $request->featured ?? false,
        'deal_of_the_day' => $request->deal_of_the_day ?? false,
        'best_seller' => $request->best_seller ?? false,
        'top_offer_product' => $request->top_offer_product ?? false,
    ]);

    return response()->json($product, 201);
}

// Update an existing product
public function update(UpdateProductRequest $request, $id)
{
    $product = Product::find($id);

    if (!$product) {
        return response()->json(['message' => 'Product not found'], 404);
    }

    // Handle image updates and other updates
    if ($request->hasFile('images')) {
        // Delete old image if exists
        if ($product->images && Storage::disk('public')->exists($product->images)) {
            Storage::disk('public')->delete($product->images);
        }
        $imagePath = $request->file('images')->store('products', 'public');
        $product->images = $imagePath;
    }

    // Handle additional image updates
    if ($request->hasFile('additional_images')) {
        foreach ($product->additional_images as $oldImage) {
            if (Storage::disk('public')->exists($oldImage)) {
                Storage::disk('public')->delete($oldImage);
            }
        }
        $additionalImagesPaths = [];
        foreach ($request->file('additional_images') as $image) {
            $additionalImagesPaths[] = $image->store('products', 'public');
        }
        $product->additional_images = $additionalImagesPaths;
    }

    // Update other fields
    $product->update($request->only([
        'name',
        'price',
        'discount_price',
        'rating',
        'reviews',
        'description',
        'color',
        'brand',
        'meter',
        'size',
        'items_stock',
        'category_id',
        'subcategory_id',
        'small_category_id',
        'featured',
        'deal_of_the_day',
        'best_seller',
        'top_offer_product',
    ]));

    // Recalculate discount percentage
    if ($request->price && $request->discount_price) {
        $product->discount_percentage = (($request->price - $request->discount_price) / $request->price) * 100;
        $product->save();
    }

    return response()->json($product);
}


    // Get all products with category, subcategory, and small category details
    public function index()
    {
        // Fetch all products with their related category, subcategory, and small category
        $products = Product::with(['category', 'subcategory', 'smallCategory'])->get();

        // Return the products with related details as a JSON response
        return response()->json($products);
    }

    // Get product by ID with category, subcategory, and small category details
    public function show($id)
    {
        // Find the product by ID and eager load the related category, subcategory, and small category
        $product = Product::with(['category', 'subcategory', 'smallCategory'])->find($id);

        if (!$product) {
            return response()->json(['message' => 'Product not found'], 404);
        }

        return response()->json($product);
    }

    // Delete a product
    public function destroy($id)
    {
        $product = Product::find($id);

        if (!$product) {
            return response()->json(['message' => 'Product not found'], 404);
        }

        // Delete the product's images
        if ($product->images && Storage::disk('public')->exists($product->images)) {
            Storage::disk('public')->delete($product->images);
        }

        // Delete additional images
        foreach ($product->additional_images as $image) {
            if (Storage::disk('public')->exists($image)) {
                Storage::disk('public')->delete($image);
            }
        }

        // Delete the product
        $product->delete();

        return response()->json(['message' => 'Product deleted successfully']);
    }
}
