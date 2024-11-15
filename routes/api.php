<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\ExternalApiController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\WalletController;
use App\Http\Controllers\SearchController;
use App\Models\User;

// Rotas de Autenticação
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// Rotas Protegidas
Route::middleware('auth:sanctum')->group(function () {
    
    Route::post('/logout', [AuthController::class, 'logout']);
    
    Route::prefix('users')->group(function () {

    Route::post('{id}/wallet/add-coins', [WalletController::class, 'addCoins']);
    });

});

// Rota para consumir API externa
Route::get('/coins/all', [ExternalApiController::class, 'getAllCoins']);
Route::get('/cryptos/suggestions', [SearchController::class, 'getSuggestions']);
Route::get('/coin-market-data/{coinId}', [ExternalApiController::class, 'getMarketData']);
Route::post('/wallet/{id}/add-coins', [WalletController::class, 'addCoins']);
Route::get('/wallet/{id}/coins', [WalletController::class, 'getCoins']);
Route::delete('/wallet/{userId}/coin', [WalletController::class, 'deleteCoin']);
Route::get('/check-email', function (Request $request) {
    $email = $request->query('email');
    $user = User::where('email', $email)->first();

    if ($user) {
        return response()->json(['message' => 'Email already in use'], 409);
    }

    return response()->json(['message' => 'Email available'], 200);
});


