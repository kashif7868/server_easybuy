<?php
namespace App\Http\Controllers;

use App\Models\Slider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SliderController extends Controller
{
    // Store a new slider (image upload)
    public function store(Request $request)
    {
        // Validate the incoming request with file validation
        $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:1048576',  
        ]);

        // Handle the uploaded image file
        $imagePath = $request->file('image')->store('sliders', 'public');  // Store in the 'sliders' folder inside the 'public' disk

        // Create a new slider entry with the image path
        $slider = Slider::create([
            'image' => $imagePath,  // Store the file path in the database (not the actual file)
        ]);

        // Return the created slider information as JSON
        return response()->json($slider, 201);
    }

    // Get all sliders
    public function index()
    {
        $sliders = Slider::all();
        return response()->json($sliders);  // Return all sliders as JSON
    }

    // Get a single slider by ID
    public function show($id)
    {
        $slider = Slider::find($id);

        if (!$slider) {
            return response()->json(['message' => 'Slider not found'], 404);  // Return 404 if slider is not found
        }

        return response()->json($slider);  // Return the slider details as JSON
    }

    // Delete a slider by ID
    public function destroy($id)
    {
        $slider = Slider::find($id);

        if (!$slider) {
            return response()->json(['message' => 'Slider not found'], 404);  // Return 404 if slider is not found
        }

        // Delete the slider entry from the database
        $slider->delete();

        return response()->json(['message' => 'Slider deleted successfully']);  // Return success message
    }

    // Update a slider by ID (PATCH)
    public function update(Request $request, $id)
    {
        $slider = Slider::find($id);

        if (!$slider) {
            return response()->json(['message' => 'Slider not found'], 404);  // Return 404 if slider is not found
        }

        // Validate the incoming request if there is an image update
        $request->validate([
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',  // Validate file type and size (optional for update)
        ]);

        // If a new image is uploaded, handle the uploaded image file and update the slider
        if ($request->hasFile('image')) {
            // Delete the old image file from storage if it exists
            Storage::delete($slider->image);

            // Store the new image
            $imagePath = $request->file('image')->store('sliders', 'public');
            $slider->image = $imagePath;
        }

        // Save other fields if necessary (you can add additional fields here for update)
        // $slider->other_field = $request->input('other_field');
        
        // Save the updated slider to the database
        $slider->save();

        // Return the updated slider information as JSON
        return response()->json($slider);
    }
}
