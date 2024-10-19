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
        $coins = $request->input('coins');
        $user->wallet->coins = $coins;
        $user->wallet->save();

        return response()->json(['message' => 'Coins added successfully'], 200);
    }
}
