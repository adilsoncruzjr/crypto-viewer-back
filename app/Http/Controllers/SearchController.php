<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class SearchController extends Controller
{
     // Método para retornar sugestões de criptomoedas
     public function getSuggestions(Request $request)
     {
         $query = $request->input('query');
 
         // Validação básica para garantir que a consulta não esteja vazia
         if (empty($query)) {
             return response()->json([], 400); // Retorna um erro 400 se a consulta estiver vazia
         }
 
         // Chamar a API da CoinGecko para obter as sugestões
         $apiUrl = env('EXTERNAL_API_URL') . '/coins/markets'; // Use o endpoint correto da API da CoinGecko
         $response = Http::get($apiUrl, [
             'vs_currency' => 'usd', // ou a moeda que você preferir
             'order' => 'market_cap_desc',
             'per_page' => 10,
             'page' => 1,
             'sparkline' => 'false',
         ]);
 
         if ($response->successful()) {
             // Filtrar os resultados para encontrar sugestões que contenham a consulta
             $suggestions = collect($response->json())->filter(function ($crypto) use ($query) {
                 return stripos($crypto['name'], $query) !== false || stripos($crypto['symbol'], $query) !== false;
             })->take(10); // Limita as sugestões a 10
 
             return response()->json($suggestions);
         }
 
         return response()->json(['error' => 'Erro ao consumir API externa'], 500);
     }
}
