<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ApiSandboxMessageController;
use App\Http\Middleware\PersistApiSandboxToken;

Route::controller(ApiSandboxMessageController::class)->group(function () {
    Route::post('/message', 'store')->middleware([PersistApiSandboxToken::class])->name('api-sandbox.api.message.store');

    Route::get('/message', 'index')->middleware([PersistApiSandboxToken::class])->name('api-sandbox.api.message.index');
});


Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');
