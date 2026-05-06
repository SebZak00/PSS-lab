<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

use App\Http\Controllers\TaskController;

Route::get('/dashboard', [TaskController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');
    
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});
Route::get('/tasks/create', [TaskController::class, 'create'])->name('tasks.create');
Route::post('/tasks', [TaskController::class, 'store'])->name('tasks.store');

require __DIR__.'/auth.php';

use App\Http\Controllers\UserController;

Route::post('/users/{user}/role', [UserController::class, 'updateRole'])->name('users.updateRole');

Route::post('/users/{user}/toggle-block', [UserController::class, 'toggleBlock'])->name('users.toggleBlock');

Route::get('/tasks/{task}/edit', [TaskController::class, 'edit'])->name('tasks.edit');
Route::put('/tasks/{task}', [TaskController::class, 'update'])->name('tasks.update');
Route::patch('/tasks/{task}/status', [TaskController::class, 'updateStatus'])->name('tasks.updateStatus');
Route::get('/tasks/{task}', [TaskController::class, 'show'])->name('tasks.show');
Route::post('/tasks/{task}/notes', [TaskController::class, 'addNote'])->name('tasks.addNote');