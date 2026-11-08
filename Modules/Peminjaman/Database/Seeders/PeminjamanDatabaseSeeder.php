<?php

namespace Modules\Peminjaman\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Buku\App\Models\Buku;
use Modules\Peminjaman\App\Models\Peminjaman;

class PeminjamanDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * Seeder ini tidak mengubah stok buku; data hanya contoh tampilan.
     */
    public function run(): void
    {
        if (Peminjaman::exists()) {
            return;
        }

        $contoh = [
            ['Laskar Pelangi', 'Siti Aminah', '2026-11-01', '2026-11-08', 'Dikembalikan'],
            ['Filosofi Teras', 'Budi Santoso', '2026-11-03', null, 'Dipinjam'],
            ['Atomic Habits', 'Dewi Lestari', '2026-11-05', null, 'Dipinjam'],
        ];

        foreach ($contoh as [$judul, $nama, $pinjam, $kembali, $status]) {
            $buku = Buku::where('judul', $judul)->first();

            if (! $buku) {
                continue;
            }

            Peminjaman::create([
                'buku_id' => $buku->id,
                'nama_peminjam' => $nama,
                'tanggal_pinjam' => $pinjam,
                'tanggal_kembali' => $kembali,
                'status' => $status,
            ]);
        }
    }
}
