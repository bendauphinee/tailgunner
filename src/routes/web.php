<?php

use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\RecordController;
use App\Http\Controllers\TemplateController;
use App\Http\Controllers\HomeController;

Route::get('/', [HomeController::class, 'index'])->name('home');

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::get('/dashboard', function () {
        return Inertia::render('Dashboard');
    })->name('dashboard');

    // Record management routes
    Route::prefix('records')
        ->name('records.')
        ->group(function () {
            Route::resource('{template}', RecordController::class)->only(['index', 'create', 'store']);
        }
    );

    Route::redirect('/records', '/templates');

    // Template pages and tools
    Route::resource('/templates', TemplateController::class)->only(['index', 'show', 'store', 'update']);
});
