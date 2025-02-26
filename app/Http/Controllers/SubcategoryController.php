<?php

namespace App\Http\Controllers;

use App\Models\Subcategory;
use App\Models\Category;
use Illuminate\Http\Request;

class SubcategoryController extends Controller
{
    // Create a new subcategory
    public function store(Request $request)
    {
        $request->validate([
            'subCategoryName' => 'required|string',
            'category_name' => 'required|string',
            'category_id' => 'required|exists:categories,id',  // Ensure the category ID exists
        ]);

        // Create a new subcategory
        $subcategory = Subcategory::create([
            'subCategoryName' => $request->subCategoryName,
            'category_name' => $request->category_name,
            'category_id' => $request->category_id,
        ]);

        return response()->json($subcategory, 201);
    }

    // Get all subcategories
    public function index()
    {
        $subcategories = Subcategory::all();
        return response()->json($subcategories);
    }

    // Get a subcategory by ID
    public function show($id)
    {
        $subcategory = Subcategory::find($id);

        if (!$subcategory) {
            return response()->json(['message' => 'Subcategory not found'], 404);
        }

        return response()->json($subcategory);
    }

    // Delete a subcategory by ID
    public function destroy($id)
    {
        $subcategory = Subcategory::find($id);

        if (!$subcategory) {
            return response()->json(['message' => 'Subcategory not found'], 404);
        }

        // Delete the subcategory
        $subcategory->delete();

        return response()->json(['message' => 'Subcategory deleted successfully']);
    }

    // Update subcategory by ID (PATCH)
    public function update(Request $request, $id)
    {
        $subcategory = Subcategory::find($id);

        if (!$subcategory) {
            return response()->json(['message' => 'Subcategory not found'], 404);  // Return 404 if subcategory is not found
        }

        // Validate the incoming request (fields are optional for update)
        $request->validate([
            'subCategoryName' => 'nullable|string',
            'category_name' => 'nullable|string',
            'category_id' => 'nullable|exists:categories,id',  // Ensure the category ID exists (optional for update)
        ]);

        // Update subcategory name if provided
        if ($request->has('subCategoryName')) {
            $subcategory->subCategoryName = $request->subCategoryName;
        }

        // Update category name if provided
        if ($request->has('category_name')) {
            $subcategory->category_name = $request->category_name;
        }

        // Update category_id if provided and the category exists
        if ($request->has('category_id') && Category::find($request->category_id)) {
            $subcategory->category_id = $request->category_id;
        }

        // Save the updated subcategory to the database
        $subcategory->save();

        // Return the updated subcategory information as JSON
        return response()->json($subcategory);
    }
}
