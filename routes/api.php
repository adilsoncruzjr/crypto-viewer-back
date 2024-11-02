<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\ExternalApiController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\WalletController;
use App\Http\Controllers\SearchController;

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
Route::get('/external-api', [ExternalApiController::class, 'getData']);
Route::get('/cryptos/suggestions', [SearchController::class, 'getSuggestions']);