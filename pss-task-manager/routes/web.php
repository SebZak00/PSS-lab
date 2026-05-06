<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\UserController;

Route::get('/', function () {
    return view('welcome');
});

// Trasa Dashboard (wymaga logowania i zweryfikowanego emaila)
Route::get('/dashboard', [TaskController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

// Grupa tras chronionych (dostępnych tylko dla zalogowanych)
Route::middleware('auth')->group(function () {
    
    // --- Zarządzanie Profilem (domyślne z Laravel Breeze) ---
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // --- Zarządzanie Zadaniami (TaskController) ---
    // Grupowanie tras zapobiega powtarzaniu '/tasks' oraz nazwy kontrolera
    Route::controller(TaskController::class)->prefix('tasks')->name('tasks.')->group(function () {
        Route::get('/create', 'create')->name('create');
        Route::post('/', 'store')->name('store');
        Route::get('/{task}/edit', 'edit')->name('edit');
        Route::put('/{task}', 'update')->name('update');
        Route::patch('/{task}/status', 'updateStatus')->name('updateStatus');
        Route::get('/{task}', 'show')->name('show');
        Route::post('/{task}/notes', 'addNote')->name('addNote');
    });

    // --- Zarządzanie Użytkownikami (UserController) ---
    Route::controller(UserController::class)->prefix('users')->name('users.')->group(function () {
        Route::post('/{user}/role', 'updateRole')->name('updateRole');
        Route::post('/{user}/toggle-block', 'toggleBlock')->name('toggleBlock');
    });
});

require __DIR__.'/auth.php';