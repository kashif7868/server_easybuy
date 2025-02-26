<?php

namespace App\Http\Controllers;

use App\Models\SmallCategory;
use App\Models\Category;
use App\Models\Subcategory;
use Illuminate\Http\Request;

class SmallCategoryController extends Controller
{
    // Create a new small category
    public function store(Request $request)
    {
        $request->validate([
            'small_category_name' => 'required|string',
            'category_id' => 'required|exists:categories,id',  // Ensure the category exists
            'subcategory_id' => 'required|exists:subcategories,id',  // Ensure the subcategory exists
        ]);

        // Create a new small category
        $smallCategory = SmallCategory::create([
            'small_category_name' => $request->small_category_name,
            'category_id' => $request->category_id,
            'subcategory_id' => $request->subcategory_id,
        ]);

        return response()->json($smallCategory, 201);
    }

    // Get all small categories
    public function index()
    {
        $smallCategories = SmallCategory::with('category', 'subcategory')->get();
        return response()->json($smallCategories);
    }

    // Get small category by ID
    public function show($id)
    {
        $smallCategory = SmallCategory::with('category', 'subcategory')->find($id);

        if (!$smallCategory) {
            return response()->json(['message' => 'Small Category not found'], 404);
        }

        return response()->json($smallCategory);
    }

    // Delete small category by ID
    public function destroy($id)
    {
        $smallCategory = SmallCategory::find($id);

        if (!$smallCategory) {
            return response()->json(['message' => 'Small Category not found'], 404);
        }

        $smallCategory->delete();

        return response()->json(['message' => 'Small Category deleted successfully']);
    }

    // Update small category by ID (PATCH)
    public function update(Request $request, $id)
    {
        $smallCategory = SmallCategory::find($id);

        if (!$smallCategory) {
            return response()->json(['message' => 'Small Category not found'], 404);  // Return 404 if small category is not found
        }

        // Validate the incoming request (fields are optional for update)
        $request->validate([
            'small_category_name' => 'nullable|string',
            'category_id' => 'nullable|exists:categories,id',  // Ensure the category exists (optional for update)
            'subcategory_id' => 'nullable|exists:subcategories,id',  // Ensure the subcategory exists (optional for update)
        ]);

        // Update small category name if provided
        if ($request->has('small_category_name')) {
            $smallCategory->small_category_name = $request->small_category_name;
        }

        // Update category_id if provided and the category exists
        if ($request->has('category_id') && Category::find($request->category_id)) {
            $smallCategory->category_id = $request->category_id;
        }

        // Update subcategory_id if provided and the subcategory exists
        if ($request->has('subcategory_id') && Subcategory::find($request->subcategory_id)) {
            $smallCategory->subcategory_id = $request->subcategory_id;
        }

        // Save the updated small category to the database
        $smallCategory->save();

        // Return the updated small category information as JSON
        return response()->json($smallCategory);
    }
}
