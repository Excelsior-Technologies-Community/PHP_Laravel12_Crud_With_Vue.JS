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
    | Post Autocomplete
    |--------------------------------------------------------------------------
    */

    Route::get('/posts/autocomplete', [PostController::class, 'autocomplete'])
        ->name('posts.autocomplete');

    /*
    |--------------------------------------------------------------------------
    | Authors & Tags
    |--------------------------------------------------------------------------
    */

    Route::get('/posts/authors', [PostController::class, 'getAuthors'])
        ->name('posts.authors');

    Route::get('/posts/tags', [PostController::class, 'getTags'])
        ->name('posts.tags');

    /*
    |--------------------------------------------------------------------------
    | Post Engagement
    |--------------------------------------------------------------------------
    */

    Route::post('/posts/{post}/view', [PostController::class, 'trackView'])
        ->name('posts.view');

    Route::post('/posts/{post}/like', [PostController::class, 'toggleLike'])
        ->name('posts.like');

    Route::post('/posts/{post}/bookmark', [PostController::class, 'toggleBookmark'])
        ->name('posts.bookmark');

    Route::post('/posts/{post}/comments', [PostController::class, 'storeComment'])
        ->name('posts.comments.store');

    Route::delete('/posts/comments/{comment}', [PostController::class, 'destroyComment'])
        ->name('posts.comments.destroy');

    /*
    |--------------------------------------------------------------------------
    | Filter Presets
    |--------------------------------------------------------------------------
    */

    Route::post('/filter-presets', [PostController::class, 'saveFilterPreset'])
        ->name('filter-presets.store');

    Route::get('/filter-presets', [PostController::class, 'getFilterPresets'])
        ->name('filter-presets.index');

    Route::get('/filter-presets/{filterPreset}', [PostController::class, 'loadFilterPreset'])
        ->name('filter-presets.show');

    Route::delete('/filter-presets/{filterPreset}', [PostController::class, 'deleteFilterPreset'])
        ->name('filter-presets.destroy');

    /*
    |--------------------------------------------------------------------------
    | Views Analytics
    |--------------------------------------------------------------------------
    */

    Route::get('/posts/views-analytics', [PostController::class, 'getViewsAnalytics'])
        ->name('posts.views-analytics');

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

    Route::get('/posts/{post:slug}', [PostController::class, 'show'])
        ->name('posts.show');
});

require __DIR__ . '/auth.php';
