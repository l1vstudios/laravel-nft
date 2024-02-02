<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;



use Illuminate\Validation\ValidationException;
use App\Models\User;


class ApiAuthController extends Controller
{
    public function __construct()
    {
        $this->middleware('disablecors')->only('getUserByToken');
    }
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        if (auth()->attempt($credentials)) {
            $user = auth()->user();
            $token = $user->createToken('api_token')->plainTextToken;

            return response()->json(['token' => $token, 'user' => $user], 200);
        }

        throw ValidationException::withMessages([
            'error' => ['Invalid credentials'],
        ]);
    }

    public function getUserByToken(Request $request)
    {
        // Mendapatkan token dari query string
        $token = $request->query('token');

        // Menemukan user berdasarkan token
        $user = Auth::guard('sanctum')->user();

        // Mengembalikan informasi user jika ditemukan
        if ($user) {
            return response()->json(['user' => [
                'id' => $user->id,
                'name' => $user->name,
                'username' => $user->username,
                'email' => $user->email,
            ]]);
        } else {
            // Mengembalikan response Unauthorized jika user tidak ditemukan
            return response()->json(['error' => 'Unauthorized'], 401);
        }
    }

    public function logoutByToken(Request $request)
    {
        try {
            $token = $request->input('token');

            // Revoke the user's token based on the provided token
            Auth::guard('sanctum')->user()->tokens()->where('token', $token)->delete();

            return response()->json(['message' => 'Token revoked successfully']);
        } catch (\Exception $e) {
            // Handle exceptions if any
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
