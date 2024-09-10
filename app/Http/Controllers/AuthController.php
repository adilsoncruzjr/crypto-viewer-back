<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use Illuminate\Support\Facades\Auth;


class AuthController extends Controller
{
    // Registro de usuário
    public function register(Request $request)
    {
        // Validação dos dados de entrada
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
        ]);

        // Retorna erros de validação, se houver
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        // Criação do usuário
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        // Retorna o usuário criado
        return response()->json(['user' => $user], 201);
    }

    // Login de usuário
    public function login(Request $request)
    {
        // Validação dos dados de entrada
        $validator = Validator::make($request->all(), [
            'email' => 'required|string|email',
            'password' => 'required|string',
        ]);

        // Retorna erros de validação, se houver
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        // Credenciais para autenticação
        $credentials = $request->only('email', 'password');

        // Verifica se as credenciais estão corretas
        if (!Auth::attempt($credentials)) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        // Obtém o usuário autenticado
        $user = Auth::user();

        // Cria um token de acesso pessoal
        $token = $user->createToken('Personal Access Token')->plainTextToken;

        // Retorna o token e o usuário
        return response()->json(['token' => $token, 'user' => $user]);
    }

    // Logout de usuário (opcional)
    public function logout(Request $request)
    {
        // Remove o token do usuário
        $user = Auth::user();
        $user->tokens()->delete();

        // Retorna uma resposta de sucesso
        return response()->json(['message' => 'Logged out successfully']);
    }
}
