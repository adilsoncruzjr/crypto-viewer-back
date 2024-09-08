<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

//Route::get('/user', function (Request $request) {
//    return $request->user();
//});

route::get('/external-api', [App\Http\Controllers\ExternalApiController::class, 'getData']);