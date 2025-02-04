<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ApiSandboxMessageController;
use App\Http\Middleware\PersistApiSandboxToken;

Route::post('/message', [ApiSandboxMessageController::class, 'store'])->middleWare([PersistApiSandboxToken::class]);

Route::get('/message', [ApiSandboxMessageController::class, 'index'])->middleWare([PersistApiSandboxToken::class]);


Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');
