<?php

namespace App\Http\Controllers;

use App\Models\Banner;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class BannerController extends Controller
{
    // Create a new banner
    public function store(Request $request)
    {
        // Validate incoming request
        $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:1048576',  // File validation
            'category_name' => 'required|string',
            'subcategory_name' => 'required|string',
            'category_id' => 'required|integer',
        ]);

        // Store the image
        $imagePath = $request->file('image')->store('banners', 'public');  // Store in 'banners' folder

        // Create a new banner entry in the database
        $banner = Banner::create([
            'image' => $imagePath,
            'category_name' => $request->category_name,
            'subcategory_name' => $request->subcategory_name,
            'category_id' => $request->category_id,
        ]);

        return response()->json($banner, 201);
    }

    // Get all banners
    public function index()
    {
        $banners = Banner::all();
        return response()->json($banners);
    }

    // Get a banner by ID
    public function show($id)
    {
        $banner = Banner::find($id);

        if (!$banner) {
            return response()->json(['message' => 'Banner not found'], 404);
        }

        return response()->json($banner);
    }

    // Delete a banner by ID
    public function destroy($id)
    {
        $banner = Banner::find($id);

        if (!$banner) {
            return response()->json(['message' => 'Banner not found'], 404);
        }

        // Delete the banner image file
        Storage::disk('public')->delete($banner->image);

        // Delete the banner record from the database
        $banner->delete();

        return response()->json(['message' => 'Banner deleted successfully']);
    }

    // Update a banner by ID (PATCH)
    public function update(Request $request, $id)
    {
        $banner = Banner::find($id);

        if (!$banner) {
            return response()->json(['message' => 'Banner not found'], 404);  // Return 404 if banner is not found
        }

        // Validate incoming request (image is optional for update)
        $request->validate([
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',  // Image is optional for update
            'category_name' => 'nullable|string',
            'subcategory_name' => 'nullable|string',
            'category_id' => 'nullable|integer',
        ]);

        // If a new image is uploaded, handle the uploaded image file and update the banner
        if ($request->hasFile('image')) {
            // Delete the old image file from storage if it exists
            Storage::delete($banner->image);

            // Store the new image
            $imagePath = $request->file('image')->store('banners', 'public');
            $banner->image = $imagePath;
        }

        // Update other fields if provided
        if ($request->has('category_name')) {
            $banner->category_name = $request->category_name;
        }
        if ($request->has('subcategory_name')) {
            $banner->subcategory_name = $request->subcategory_name;
        }
        if ($request->has('category_id')) {
            $banner->category_id = $request->category_id;
        }

        // Save the updated banner to the database
        $banner->save();

        // Return the updated banner information as JSON
        return response()->json($banner);
    }
}
