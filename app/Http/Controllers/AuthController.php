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
                if ($user->role == 'admin') {
                    return response()->json([
                        'status' => true,
                        'message' => 'Berhasil Login',
                        'data' => $user,
                        'token' => $user->createToken($user->name, ['update'])->plainTextToken
                    ]);
                } else {
                    return response()->json([
                        'status' => true,
                        'message' => 'Berhasil Login',
                        'data' => $user,
                        'token' => $user->createToken($user->name, ['read'])->plainTextToken
                    ]);
                }
            }
            return response()->error('Password salah!');
        }
        return response()->error('Email belum terdaftar');
    }

    public function logout(Request $request)
    {
        $request->user()->tokens()->delete();
        return response()->json([
            'status' => true,
            'message' => 'Berhasil Logout'
        ]);
    }
    public function about(Request $request)
    {
        // dd($request->user()->tokenCan('update'));
        if ($request->user()->tokenCan('update')) {
            $request->validate([
                'name' => 'required|min:3|max:20'
            ]);
            $request->user()->update([
                'name' => $request->name
            ]);
            return response()->json([
                'status' => true,
                'message' => 'Data Berhasil Ditambahkan',
                'data' => $request->user()
            ]);
        }
        if ($request->user()->tokenCan('read')) {
            return response()->json([
                'status' => true,
                'data' => $request->user()
            ]);
        }
    }
}
