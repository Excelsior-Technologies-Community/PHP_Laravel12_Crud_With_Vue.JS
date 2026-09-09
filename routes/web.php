<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PostController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

/*
|--------------------------------------------------------------------------
| Welcome Page
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

/*
|--------------------------------------------------------------------------
| Dashboard
|--------------------------------------------------------------------------
*/

Route::get('/dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

/*
|--------------------------------------------------------------------------
| Authenticated Routes
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'verified'])->group(function () {

    /*
    |--------------------------------------------------------------------------
    | Profile
    |--------------------------------------------------------------------------
    */

    Route::get('/profile', [ProfileController::class, 'edit'])
        ->name('profile.edit');

    Route::patch('/profile', [ProfileController::class, 'update'])
        ->name('profile.update');

    Route::delete('/profile', [ProfileController::class, 'destroy'])
        ->name('profile.destroy');

    /*
    |--------------------------------------------------------------------------
    | Post Statistics
    |--------------------------------------------------------------------------
    */

    Route::get('/post-statistics', [PostController::class, 'statistics'])
        ->name('posts.statistics');

    /*
    |--------------------------------------------------------------------------
    | Post Export
    |--------------------------------------------------------------------------
    */

    Route::get('/posts-export', [PostController::class, 'export'])
        ->name('posts.export');

    /*
    |--------------------------------------------------------------------------
    | Post Restore
    |--------------------------------------------------------------------------
    */

    Route::post('/posts/{post}/restore', [PostController::class, 'restore'])
        ->name('posts.restore');

    /*
    |--------------------------------------------------------------------------
    | Bulk Delete
    |--------------------------------------------------------------------------
    */

    Route::post('/posts/bulk-delete', [PostController::class, 'bulkDelete'])
        ->name('posts.bulk-delete');

    /*
    |--------------------------------------------------------------------------
    | Posts CRUD
    |--------------------------------------------------------------------------
    */

    Route::resource('posts', PostController::class);
});

require __DIR__ . '/auth.php';
