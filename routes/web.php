<?php

use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;

// Halaman awal
Route::get('/', fn () => redirect()->route('buku.index'));

// Authentication (di luar modul HMVC)
Route::middleware('guest')->group(function () {
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register'])->name('register.store');

    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.store');
});

Route::post('/logout', [AuthController::class, 'logout'])
    ->middleware('auth')
    ->name('logout');
