<?php

use Illuminate\Support\Facades\Route;
use Modules\Buku\App\Http\Controllers\BukuController;

Route::middleware('auth')->group(function () {
    // Admin & user: hanya melihat daftar buku
    Route::get('buku', [BukuController::class, 'index'])->name('buku.index');

    // Khusus admin: tambah, ubah, hapus
    Route::middleware('role:admin')->group(function () {
        Route::resource('buku', BukuController::class)
            ->except(['index', 'show'])
            ->names('buku');
    });
});
