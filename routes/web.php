<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\ApiSandboxController;

Route::get('/', function () {
    return view('index', ['title' => 'Home']);
})->name('index');

Route::get('/projects', [ProjectController::class, 'index'])->name('projects.index');
Route::get('/projects/{slug}', [ProjectController::class, 'show'])->name('projects.show');

Route::get('/api-sandbox', [ApiSandboxController::class, 'index'])->name('api-sandbox.index');
