<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

use Illuminate\Support\Facades\Http;

class ExternalApiController extends Controller
{
    public function getData(Request $request)
    {
        $apiUrl = env('EXTERNAL_API_URL');
        $apiKey = env('EXTERNAL_API_KEY');
        
        // Recebe o parâmetro 'query' da requisição
        $query = $request->query('query');

        // Faz a requisição para a API da CoinGecko com o termo de busca
        $response = Http::withHeaders([
            'x_cg_demo_api_key' => $apiKey,
        ])->get($apiUrl, [
            'query' => $query
        ]);

        if ($response->successful()) {
            return response()->json($response->json());
        }

        return response()->json([
            'error' => 'Erro ao consumir API externa',
            'status' => $response->status()
        ], 500);
    }

    public function getAllCoins()
    {
        $apiUrl = 'https://api.coingecko.com/api/v3/coins/list?include_platform=false';
        
        // Faz a requisição para a API da CoinGecko
        $response = Http::get($apiUrl);

        if ($response->successful()) {
            return response()->json($response->json());
        }

        return response()->json([
            'error' => 'Erro ao consumir API externa',
            'status' => $response->status()
        ], 500);
    }

    public function getMarketData(Request $request, $coinId)
    {
        $days = $request->query('days', 30); // Pega o parâmetro days, padrão 30
        Log::info('Buscando dados de mercado para a moeda com ID:', ['coin_id' => $coinId, 'days' => $days]);
    
        $url = "https://api.coingecko.com/api/v3/coins/{$coinId}/market_chart?vs_currency=usd&days={$days}";
    
        Log::info('Requisitando dados de mercado na API externa:', ['url' => $url]);
    
        $response = Http::get($url);
    
        if ($response->successful()) {
            Log::info('Resposta da API recebida com sucesso:', ['data' => $response->json()]);
            return response()->json($response->json());
        }
    
        Log::error('Falha ao obter dados da API:', ['error' => $response->body()]);
        return response()->json(['error' => 'Failed to fetch data'], 500);
    }

}
