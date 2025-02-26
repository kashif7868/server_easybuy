<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\UserRegisterRequest;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    public function register(UserRegisterRequest $request)
    {
        $validatedData = $request->validated();

        $user = User::create([
            'name' => $validatedData['name'],
            'email' => $validatedData['email'],
            'password' => bcrypt($validatedData['password']),
        ]);

        $token = auth('api')->login($user);
        return $this->respondWithToken($token, $user);
    }

    public function login()
    {
        $credentials = request(['email', 'password']);

        if (! $token = auth('api')->attempt($credentials)) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $user = auth('api')->user();
        return $this->respondWithToken($token, $user);
    }

    public function me()
    {
        return response()->json(auth()->user());
    }

    public function logout()
    {
        auth()->logout();
        return response()->json(['message' => 'Successfully logged out']);
    }

    public function refresh()
    {
        $user = auth('api')->user();
        return $this->respondWithToken(auth()->refresh(), $user);
    }

    protected function respondWithToken($token, $user)
    {
        return response()->json([
            'user' => [
                'image' => null,
                'role' => 'user',
                'fullName' => $user->name,
                'email' => $user->email,
                'password' => $user->password,
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

    public function getAllUsers()
    {
        $users = User::all();
        return response()->json($users);
    }

    public function getUserById($id)
    {
        $user = User::find($id);

        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        return response()->json($user);
    }

    public function deleteUser($id)
    {
        $user = User::find($id);

        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        $user->delete();

        return response()->json(['message' => 'User successfully deleted']);
    }
}
