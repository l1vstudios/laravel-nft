<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use App\Models\User;

class RegisterController extends Controller
{
    public function __construct()
    {
        $this->middleware('disablecors')->only('customRegister');
    }

    public function customRegister(Request $request)
    {
        Log::info('Trying to store user data.');

        $validatedData = $request->validate([
            'name' => 'required|string',
            'username' => 'required|string',
            'email' => 'required|string|email',
            'password' => 'required|string',
        ]);

        // Hash the password using bcrypt
        $validatedData['password'] = bcrypt($validatedData['password']);

        Log::info('Data stored successfully.');

        $user = User::create($validatedData);

        return response()->json(['message' => 'User Berhasil Dibuat', 'data' => $user], 200);
    }
}
