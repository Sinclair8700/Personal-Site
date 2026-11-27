<?php

use Illuminate\Support\Facades\Route;
use App\Models\Project;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\EducationController;
use App\Http\Controllers\ApiSandboxController;
use App\Http\Controllers\AccountController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\SitemapController;
use App\Http\Controllers\CvController;

Route::get('/', function () {
    return view('index', [
        'title' => 'Alex Davies | Full-Stack Software Developer in Stoke-on-Trent',
        'heading' => 'Alex Davies',
        'description' => 'Alex Davies is a full-stack software developer in Stoke-on-Trent, UK, with a First in Computer Science from Keele University. See my projects and CV.',
    ]);
})->name('index');

Route::controller(ProjectController::class)->group(function () {
    Route::get('/projects', 'index')->name('projects.index');
    Route::get('/projects/create', 'create')->middleware('can:create,'.Project::class)->name('projects.create');
    Route::get('/projects/{project}', 'show')->name('projects.show');
    Route::post('/projects', 'store')->middleware('can:create,'.Project::class)->name('projects.store');
    Route::get('/projects/{project}/edit', 'edit')->middleware('can:update,project')->name('projects.edit');
    Route::put('/projects/{project}', 'update')->middleware('can:update,project')->name('projects.update');
    Route::delete('/projects/{project}', 'destroy')->middleware('can:delete,project')->name('projects.destroy');
});

Route::controller(CvController::class)->group(function () {
    Route::get('/cv', 'show')->name('cv.show');
    Route::get('/cv/download.md', 'markdown')->name('cv.markdown');
    Route::get('/cv/edit', 'edit')->middleware(['auth', 'can:manage-cv'])->name('cv.edit');
    Route::put('/cv', 'update')->middleware(['auth', 'can:manage-cv'])->name('cv.update');
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
    Route::post('/sign-out', 'signOut')->name('logout');
    Route::get('/sign-up', 'signUpForm')->name('register-form');
    Route::post('/sign-up', 'signUp')->name('register');
});

Route::get('/sitemap.xml', [SitemapController::class, 'index']);

Route::get('/contact', [ContactController::class, 'index'])->name('contact.index');
Route::post('/contact/send', [ContactController::class, 'store'])->middleware('throttle:contact')->name('contact.store');