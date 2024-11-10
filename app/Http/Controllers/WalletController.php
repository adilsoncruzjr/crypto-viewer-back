<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\User;
use Illuminate\Support\Facades\Log;



class WalletController extends Controller
{
    public function addCoins($id, Request $request)
{
    $user = User::findOrFail($id);
    $coinName = $request->input('coin_name');  // Nome da moeda
    $coinId = $request->input('coin_id');  // ID da moeda

    // Obtém a carteira do usuário
    $wallet = $user->wallet;

    // Se já existir moedas salvas, decodifique para array, caso contrário, crie um novo array
    $coins = json_decode($wallet->coins, true) ?: [];

    // Adiciona a moeda com o nome e o ID
    $coins[] = [
        'name' => $coinName,
        'id' => $coinId,  // Salva o ID da moeda também
    ];

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
    $coins = json_decode($wallet->coins, true) ?: [];

    // Se a conversão falhar, retornamos um erro
    if (json_last_error() !== JSON_ERROR_NONE) {
        return response()->json(['error' => 'Erro ao processar as moedas'], 500);
    }

    // Retorna tanto o nome quanto o id das moedas
    $coins = array_map(function($coin) {
        return [
            'name' => $coin['name'],   // Nome da moeda
            'id' => $coin['id']        // ID da moeda (agora incluído)
        ];
    }, $coins);

    return response()->json($coins, 200);
}

public function deleteCoin($id, Request $request)
{
    // Verifique se o nome da moeda foi passado corretamente
    $coinName = $request->input('coin_name');

    if (!$coinName) {
        return response()->json(['error' => 'O nome da moeda não foi fornecido.'], 400);
    }

    // Continuar com o restante do código
    $user = User::findOrFail($id);
    $wallet = $user->wallet;
    $coins = json_decode($wallet->coins, true) ?: [];

    // Filtrando e removendo a moeda
    $coins = array_filter($coins, function($coin) use ($coinName) {
        return $coin['name'] !== $coinName;
    });

    // Reindexando o array para manter os índices contíguos
    $coins = array_values($coins);

    // Atualizando a carteira do usuário
    $wallet->coins = json_encode($coins);
    $wallet->save();

    return response()->json(['message' => 'Moeda removida com sucesso'], 200);
}

}
