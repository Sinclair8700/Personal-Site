<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\EducationController;
use App\Http\Controllers\ApiSandboxController;

Route::get('/', function () {
    return view('index', ['title' => 'Home']);
})->name('index');

Route::get('/projects', [ProjectController::class, 'index'])->name('projects.index');
Route::get('/projects/{slug}', [ProjectController::class, 'show'])->name('projects.show');

Route::get('/education', [EducationController::class, 'index'])->name('education.index');
Route::get('/education/{slug}', [EducationController::class, 'show'])->name('education.show');

Route::get('/api-sandbox', [ApiSandboxController::class, 'index'])->name('api-sandbox.index');
