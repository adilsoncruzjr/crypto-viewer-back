<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use App\Models\Wallet;
use App\Models\Users;


class AuthController extends Controller
{

    public function register(Request $request)
    {

        $validated = $request->validate([
            'name' => 'required|string',
            'email' => 'required|string|email|unique:users',
            'password' => 'required|string|confirmed',
        ]);

        $emailExists = User::where('email', $validated['email'])->exists();

        if ($emailExists) {
            // Retorna um erro se o e-mail já estiver cadastrado
            return response()->json(['error' => 'Este e-mail já está em uso.'], 409);
        }

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
        ]);

        Wallet::create([
            'user_id' => $user->id,
            'coins' => 0,  // Inicialmente, a carteira começa com 0 moedas
        ]);

        return response()->json(['message' => 'User registered successfully'], 201);
    
    }

    
    public function login(Request $request)
    {
        
        $validator = Validator::make($request->all(), [
            'email' => 'required|string|email',
            'password' => 'required|string',
        ]);

        
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        
        $credentials = $request->only('email', 'password');

        
        if (!Auth::attempt($credentials)) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        
        $user = Auth::user();

        
        $token = $user->createToken('Personal Access Token')->plainTextToken;

        
        return response()->json(['token' => $token, 'user' => $user]);
    }

    
    public function logout(Request $request)
    {
        
        $user = Auth::user();
        $user->tokens()->delete();

        
        return response()->json(['message' => 'Logged out successfully']);
    }
}
