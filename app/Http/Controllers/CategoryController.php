<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CategoryController extends Controller
{
    // Create a new category
    public function store(Request $request)
    {
        $request->validate([
            'category_name' => 'required|string',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:1048576',  // Validate image
        ]);

        // Store the uploaded image
        $imagePath = $request->file('image')->store('categories', 'public');

        // Create a new category in the database
        $category = Category::create([
            'category_name' => $request->category_name,
            'image' => $imagePath,
        ]);

        return response()->json($category, 201);
    }

    // Get all categories
    public function index()
    {
        $categories = Category::all();
        return response()->json($categories);
    }

    // Get category by ID
    public function show($id)
    {
        $category = Category::find($id);

        if (!$category) {
            return response()->json(['message' => 'Category not found'], 404);
        }

        return response()->json($category);
    }

    // Delete category by ID
    public function destroy($id)
    {
        $category = Category::find($id);

        if (!$category) {
            return response()->json(['message' => 'Category not found'], 404);
        }

        // Delete the image file from storage
        Storage::disk('public')->delete($category->image);

        // Delete the category record from the database
        $category->delete();

        return response()->json(['message' => 'Category deleted successfully']);
    }

    // Update category by ID (PATCH)
    public function update(Request $request, $id)
    {
        $category = Category::find($id);

        if (!$category) {
            return response()->json(['message' => 'Category not found'], 404);  // Return 404 if category is not found
        }

        // Validate incoming request (image is optional for update)
        $request->validate([
            'category_name' => 'nullable|string',  // category_name is optional for update
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',  // Image is optional for update
        ]);

        // If a new image is uploaded, handle the uploaded image file and update the category
        if ($request->hasFile('image')) {
            // Delete the old image file from storage if it exists
            Storage::delete($category->image);

            // Store the new image
            $imagePath = $request->file('image')->store('categories', 'public');
            $category->image = $imagePath;
        }

        // Update category name if provided
        if ($request->has('category_name')) {
            $category->category_name = $request->category_name;
        }

        // Save the updated category to the database
        $category->save();

        // Return the updated category information as JSON
        return response()->json($category);
    }
}
