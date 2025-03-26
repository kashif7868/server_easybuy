<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    // Register a new user
    public function register(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6',
        ]);

        $user = User::create([
            'name' => $validatedData['name'],
            'email' => $validatedData['email'],
            'password' => bcrypt($validatedData['password']),
        ]);

        $token = auth('api')->login($user);
        return $this->respondWithToken($token, $user);
    }

    // Login a user
    public function login()
    {
        $credentials = request(['email', 'password']);

        if (! $token = auth('api')->attempt($credentials)) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $user = auth('api')->user();
        return $this->respondWithToken($token, $user);
    }

    // Get the current authenticated user
    public function me()
    {
        return response()->json(auth()->user());
    }

    // Logout the user
    public function logout()
    {
        auth()->logout();
        return response()->json(['message' => 'Successfully logged out']);
    }

    // Refresh the user token
    public function refresh()
    {
        $user = auth('api')->user();
        return $this->respondWithToken(auth()->refresh(), $user);
    }

    // Respond with token and user details
    protected function respondWithToken($token, $user)
    {
        return response()->json([
            'user' => [
                'image' => $user->image ? asset('storage/' . $user->image) : null,  // Return image URL or null
                'role' => 'user',
                'name' => $user->name,
                'email' => $user->email,
                'id' => (string) $user->id
            ],
            'tokens' => [
                'access' => [
                    'token' => $token,
                    'expires' => now()->addMinutes(auth('api')->factory()->getTTL())->toIso8601String(),
                    'uuid' => (string) Str::uuid()
                ],
                'refresh' => [
                    'token' => auth('api')->refresh(),
                    'expires' => now()->addMinutes(auth('api')->factory()->getTTL() * 2)->toIso8601String(),
                    'uuid' => (string) Str::uuid()
                ]
            ]
        ]);
    }

    // Profile Update (including image update)
   // Profile Update (including image update) using PATCH
public function updateUser(Request $request, $id)
{
    // Find the user by ID
    $user = User::find($id);

    if (!$user) {
        return response()->json(['message' => 'User not found'], 404);
    }

    // Validation for profile data
    $validatedData = $request->validate([
        'name' => 'nullable|string|max:255',
        'email' => 'nullable|email|unique:users,email,' . $user->id,
        'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp|max:1048576', // Image validation
    ]);

    // Update name and email if provided
    if ($request->has('name')) {
        $user->name = $validatedData['name'];
    }
    if ($request->has('email')) {
        $user->email = $validatedData['email'];
    }

    // Handle image upload if present
    if ($request->hasFile('image')) {
        // Delete the old image if it exists
        if ($user->image) {
            $oldImagePath = storage_path('app/public/' . $user->image);
            if (file_exists($oldImagePath)) {
                unlink($oldImagePath); // Delete old image
            }
        }

        // Store the new image
        $imagePath = $request->file('image')->store('users', 'public');
        $user->image = $imagePath;
    }

    // Save the updated user
    $user->save();

    return response()->json([
        'message' => 'Profile updated successfully',
        'user' => [
            'name' => $user->name,
            'email' => $user->email,
            'image' => $user->image ? asset('storage/' . $user->image) : null, // Return the updated image URL
        ]
    ]);
}


    // Get all users
    public function getAllUsers()
    {
        $users = User::all();
        return response()->json($users);
    }

    // Get a specific user by ID
    public function getUserById($id)
    {
        $user = User::find($id);

        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        return response()->json($user);
    }

    // Delete a user by ID
    public function deleteUser($id)
    {
        $user = User::find($id);

        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        // If the user has an image, delete it from storage
        if ($user->image) {
            $imagePath = storage_path('app/public/' . $user->image);
            if (file_exists($imagePath)) {
                unlink($imagePath); // Delete the image
            }
        }

        $user->delete();

        return response()->json(['message' => 'User successfully deleted']);
    }
}
