<?php

// app/Http/Controllers/ProductController.php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use App\Models\Subcategory;
use App\Models\SmallCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    // Create a new product
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'price' => 'required|numeric',
            'discount_price' => 'required|numeric',
            'rating' => 'required|integer',
            'reviews' => 'required|integer',
            'description' => 'required|string',
            'images' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',  // Main image
            'additional_images' => 'nullable|array',  // Additional images
            'additional_images.*' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',  // Validate additional images
            'color' => 'required|string',
            'brand' => 'required|string',
            'meter' => 'required|numeric',
            'size' => 'required|string',
            'items_stock' => 'required|integer',
            'category_id' => 'required|exists:categories,id',
            'subcategory_id' => 'required|exists:subcategories,id',
            'small_category_id' => 'required|exists:small_categories,id',
        ]);

        // Handle the main image upload
        $imagePath = $request->file('images')->store('products', 'public');  // Store the main image

        // Handle additional image uploads (if provided)
        $additionalImagesPaths = [];
        if ($request->hasFile('additional_images')) {
            foreach ($request->file('additional_images') as $image) {
                $additionalImagesPaths[] = $image->store('products', 'public');  // Store each additional image
            }
        }

        // Create the new product
        $product = Product::create([
            'name' => $request->name,
            'price' => $request->price,
            'discount_price' => $request->discount_price,
            'discount_percentage' => (($request->price - $request->discount_price) / $request->price) * 100,
            'rating' => $request->rating,
            'reviews' => $request->reviews,
            'description' => $request->description,
            'images' => $imagePath,  // Save the main image path
            'additional_images' => $additionalImagesPaths,  // Save multiple additional images
            'color' => $request->color,
            'brand' => $request->brand,
            'meter' => $request->meter,
            'size' => $request->size,
            'items_stock' => $request->items_stock,
            'category_id' => $request->category_id,
            'subcategory_id' => $request->subcategory_id,
            'small_category_id' => $request->small_category_id,
        ]);

        return response()->json($product, 201);
    }

    // Update an existing product
    public function update(Request $request, $id)
    {
        $product = Product::find($id);

        if (!$product) {
            return response()->json(['message' => 'Product not found'], 404);
        }

        $request->validate([
            'name' => 'nullable|string',
            'price' => 'nullable|numeric',
            'discount_price' => 'nullable|numeric',
            'rating' => 'nullable|integer',
            'reviews' => 'nullable|integer',
            'description' => 'nullable|string',
            'images' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',  // Main image
            'additional_images' => 'nullable|array',  // Additional images
            'additional_images.*' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',  // Validate additional images
            'color' => 'nullable|string',
            'brand' => 'nullable|string',
            'meter' => 'nullable|numeric',
            'size' => 'nullable|string',
            'items_stock' => 'nullable|integer',
            'category_id' => 'nullable|exists:categories,id',
            'subcategory_id' => 'nullable|exists:subcategories,id',
            'small_category_id' => 'nullable|exists:small_categories,id',
        ]);

        // Handle the main image upload if provided
        if ($request->hasFile('images')) {
            // Delete the old image if it exists
            if ($product->images && Storage::disk('public')->exists($product->images)) {
                Storage::disk('public')->delete($product->images);
            }

            // Store the new image
            $imagePath = $request->file('images')->store('products', 'public');
            $product->images = $imagePath;  // Update the image path in the database
        }

        // Handle the additional image uploads if provided
        if ($request->hasFile('additional_images')) {
            // Delete old additional images
            foreach ($product->additional_images as $oldImage) {
                if (Storage::disk('public')->exists($oldImage)) {
                    Storage::disk('public')->delete($oldImage);
                }
            }

            // Store new additional images
            $additionalImagesPaths = [];
            foreach ($request->file('additional_images') as $image) {
                $additionalImagesPaths[] = $image->store('products', 'public');
            }
            $product->additional_images = $additionalImagesPaths;  // Update additional images
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
        ]));

        // Recalculate the discount percentage if price or discount_price is updated
        if ($request->price && $request->discount_price) {
            $product->discount_percentage = (($request->price - $request->discount_price) / $request->price) * 100;
            $product->save();
        }

        return response()->json($product);
    }
}
