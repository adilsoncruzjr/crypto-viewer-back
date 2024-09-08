<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Http;

class ExternalApiController extends Controller
{
    public function getData()
    {
        $apiUrl = env('EXTERNAL_API_URL');
        $apiKey = env('EXTERNAL_API_KEY');

        $response = Http::withHeaders([
            'x_cg_demo_api_key' => $apiKey,
        ])->get($apiUrl);

        if ($response->successful()) {
            return response()->json($response->json());
        }

        return response()->json([
            'error' => 'Erro ao consumir API externa',
            'status' => $response->status()
        ], 500);
    }
}
