<?php

namespace App\Http\Controllers;

use App\Http\Requests\RegisterStoreRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function login(Request $request)
    {
       try {
            $credentials = $request->only('email', 'password');

            if (!Auth::guard('web')->attempt($credentials)) {
                return response()->json([
                    'message' => 'Unauthorized',
                    'data' => null
                ], 401);
            }
            
            $user = Auth::user();
            $token = $user->createToken('auth_token')->plainTextToken;

            return response()->json([
                'message' => 'Login successful',
                'data' => [
                    'token' => $token,
                    'user' => new UserResource($user)
                ]
            ], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Login failed: ' . $e->getMessage()], 500);
        }
    }

    public function me() {
        try {
            $user = Auth::user();

            return response()->json([
                'message' => 'User retrieved successfully',
                'data' => new UserResource($user)
            ], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Failed to retrieve user: ' . $e->getMessage()], 500);
        }
    }

    public function logout() {
        try {
            $user = Auth::user();
            $user->currentAccessToken()->delete();

            return response()->json([
                'message' => 'Logout successful',
                'data' => null
            ], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Logout failed: ' . $e->getMessage()], 500);
        }
    }

    public function register(RegisterStoreRequest $request) {
        $data = $request->validated();
        DB::beginTransaction();

        try {
            $user = new User();
            $user->name = $data['name'];
            $user->email = $data['email'];
            $user->password = Hash::make($data['password']);
            $user->save();

            $token = $user->createToken('auth_token')->plainTextToken;
            DB::commit();
            return response()->json([
                'message' => 'Registration successful',
                'data' => [
                    'token' => $token,
                    'user' => new UserResource($user)
                ]
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'Registration failed: ' . $e->getMessage()], 500);
        }
    }
}
