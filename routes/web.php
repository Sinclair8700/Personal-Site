<?php

use Illuminate\Support\Facades\Route;
use App\Models\Project;
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
    Route::get('/projects/create', 'create')->middleware('auth')->middleware('can:create,'.Project::class)->name('projects.create');
    Route::get('/projects/{slug}', 'show')->name('projects.show');
    Route::post('/projects', 'store')->middleware('can:create,'.Project::class)->name('projects.store');
    Route::get('/projects/{slug}/edit', 'edit')->middleware('can:update,project')->name('projects.edit');
    Route::put('/projects/{slug}', 'update')->middleware('can:update,project')->name('projects.update');
    Route::delete('/projects/{slug}', 'destroy')->middleware('can:delete,project')->name('projects.destroy');
});

Route::get('/education', [EducationController::class, 'index'])->name('education.index');
Route::get('/education/{slug}', [EducationController::class, 'show'])->name('education.show');

Route::get('/api-sandbox', [ApiSandboxController::class, 'index'])->name('api-sandbox.index');

Route::get('/physics-paint', function () {
    return view('physics-paint.index', ['title' => 'Physics Paint']);
})->name('physics-paint.index');

Route::group(['middleware' => 'auth'], function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
});


Route::controller(AccountController::class)->group(function () {
    Route::get('/sign-in', 'signInForm')->name('login-form');
    Route::post('/sign-in', 'signIn')->name('login');
    Route::get('/sign-out', 'signOut')->name('logout');
    Route::get('/sign-up', 'signUpForm')->name('register-form');
    Route::post('/sign-up', 'signUp')->name('register');
});

Route::get('/sitemap.xml', [SitemapController::class, 'index']);
