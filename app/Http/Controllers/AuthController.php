<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $request->validate([
            'role' => 'required',
            'name' => 'required|min:3|max:12',
            'email' => 'required|email',
            'password' => [
                'required',
                Password::min(6)
                    ->letters()
                    ->mixedCase()
                    ->numbers()
                    ->symbols()
            ]
        ]);

        $user = User::create([
            'role' => $request->role,
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password)
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Berhasil Register',
            'data' => $user
        ]);
    }

    public function login(Request $request)
    {
        $user = User::where('email', $request->email)->first();
        if ($user) {
            if (Hash::check($request->password, $user->password)) {
                return response()->json([
                    'status' => true,
                    'message' => 'Berhasil Login',
                    'data' => $user,
                    'token' => $user->createToken($user->name)->plainTextToken
                ]);
            }
            return response()->json([
                'status' => false,
                'message' => 'Password salah!'
            ]);
        }
        return response()->json([
            'status' => false,
            'message' => 'Email belum terdaftar'
        ]);
    }
}
