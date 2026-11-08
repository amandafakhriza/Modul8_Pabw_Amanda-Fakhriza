<?php

use Illuminate\Support\Facades\Route;
use Modules\Peminjaman\App\Http\Controllers\PeminjamanController;

// Admin maupun user (cukup login) boleh mengakses modul Peminjaman.
// Tidak ada route create/edit karena form ditampilkan lewat modal AJAX.
Route::middleware('auth')->group(function () {
    Route::resource('peminjaman', PeminjamanController::class)
        ->except(['create', 'edit'])
        ->names('peminjaman');
});
