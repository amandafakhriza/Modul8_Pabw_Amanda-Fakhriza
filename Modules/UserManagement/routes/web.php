<?php

use Illuminate\Support\Facades\Route;
use Modules\UserManagement\App\Http\Controllers\UserController;

// Seluruh modul ini khusus admin. User biasa akan mendapat error 403.
Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::resource('users', UserController::class)
        ->except(['show'])
        ->names('users');
});
