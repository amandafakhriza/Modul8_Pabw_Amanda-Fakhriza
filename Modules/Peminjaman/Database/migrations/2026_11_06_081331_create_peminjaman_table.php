<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('peminjaman', function (Blueprint $table) {
            $table->id();

            $table->foreignId('buku_id')
                ->constrained('buku')
                ->cascadeOnDelete();

            $table->string('nama_peminjam');
            $table->date('tanggal_pinjam');
            $table->date('tanggal_kembali')->nullable();
            $table->string('status')->default('Dipinjam');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('peminjaman');
    }
};
