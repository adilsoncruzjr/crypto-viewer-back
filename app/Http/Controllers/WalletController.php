<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\User;


class WalletController extends Controller
{
    public function addCoins($id, Request $request)
{
    $user = User::findOrFail($id);
    $coinName = $request->input('coin_name');  // Nome da moeda

    // Obtém a carteira do usuário
    $wallet = $user->wallet;

    // Se já existir moedas salvas, decodifique para array, caso contrário, crie um novo array
    $coins = json_decode($wallet->coins, true) ?: [];

    // Adiciona o nome da moeda
    $coins[] = ['name' => $coinName];

    // Salva o array de moedas como string JSON
    $wallet->coins = json_encode($coins);
    $wallet->save();

    return response()->json(['message' => 'Moeda adicionada com sucesso'], 200);
}

public function getCoins($userId)
{
    // Encontra o usuário
    $user = User::findOrFail($userId);
    
    // Obtém a carteira do usuário
    $wallet = $user->wallet;

    // Se o campo 'coins' for uma string JSON, converta para array
    $coins = json_decode($wallet->coins, true);

    // Se a conversão falhar, retornamos um erro
    if (json_last_error() !== JSON_ERROR_NONE) {
        return response()->json(['error' => 'Erro ao processar as moedas'], 500);
    }

    // Retorna apenas o nome das moedas (removendo o 'value')
    $coins = array_map(function($coin) {
        return ['name' => $coin['name']];
    }, $coins);

    return response()->json($coins, 200);
}
}
