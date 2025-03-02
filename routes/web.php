<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\EducationController;
use App\Http\Controllers\ApiSandboxController;
use App\Http\Controllers\AccountController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\SitemapController;

Route::get('/', function () {
    return view('index', ['title' => 'Home']);
})->name('index');

Route::controller(ProjectController::class)->group(function () {
    Route::get('/projects', 'index')->name('projects.index');
    Route::get('/projects/{slug}', 'show')->name('projects.show');
    Route::get('/projects/create', 'create')->name('projects.create');
    Route::post('/projects', 'store')->name('projects.store');
    Route::get('/projects/{slug}/edit', 'edit')->name('projects.edit');
    Route::put('/projects/{slug}', 'update')->name('projects.update');
    Route::delete('/projects/{slug}', 'destroy')->name('projects.destroy');
});

Route::get('/education', [EducationController::class, 'index'])->name('education.index');
Route::get('/education/{slug}', [EducationController::class, 'show'])->name('education.show');

Route::get('/api-sandbox', [ApiSandboxController::class, 'index'])->name('api-sandbox.index');


Route::group(['middleware' => 'auth'], function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
});


Route::controller(AccountController::class)->group(function () {
    Route::get('/account/sign-in', 'signInForm')->name('login');
    Route::post('/account/sign-in', 'signIn')->name('login');
    Route::get('/account/sign-out', 'signOut')->name('logout');
    Route::get('/account/sign-up', 'signUpForm')->name('register');
    Route::post('/account/sign-up', 'signUp')->name('register');
});

Route::get('/sitemap.xml', [SitemapController::class, 'index']);
