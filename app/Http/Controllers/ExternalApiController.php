<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

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
}
