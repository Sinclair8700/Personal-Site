<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\ApiSandboxController;

Route::get('/', function () {
    return view('index', ['title' => 'Home']);
});

Route::get('/projects', [ProjectController::class, 'index']);
Route::get('/projects/{slug}', [ProjectController::class, 'show']);

Route::get('/api-sandbox', [ApiSandboxController::class, 'index']);
