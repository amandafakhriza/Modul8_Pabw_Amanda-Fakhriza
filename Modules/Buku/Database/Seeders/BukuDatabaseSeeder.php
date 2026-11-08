<?php

namespace Modules\Buku\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Buku\App\Models\Buku;

class BukuDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            ['Laskar Pelangi', 'Andrea Hirata', 'Bentang Pustaka', 2005, 5],
            ['Bumi Manusia', 'Pramoedya Ananta Toer', 'Hasta Mitra', 1980, 3],
            ['Filosofi Teras', 'Henry Manampiring', 'Kompas', 2018, 4],
            ['Atomic Habits', 'James Clear', 'Gramedia Pustaka Utama', 2019, 6],
            ['Cantik Itu Luka', 'Eka Kurniawan', 'Gramedia Pustaka Utama', 2002, 2],
            ['Pemrograman Web dengan Laravel', 'Budi Raharjo', 'Informatika', 2023, 7],
            ['Basis Data Relasional', 'Abdul Kadir', 'Andi Offset', 2021, 4],
            ['Algoritma dan Struktur Data', 'Rinaldi Munir', 'Informatika', 2020, 5],
        ];

        foreach ($data as [$judul, $penulis, $penerbit, $tahun, $stok]) {
            // updateOrCreate agar seeder aman dijalankan berulang kali
            Buku::updateOrCreate(
                ['judul' => $judul],
                ['penulis' => $penulis, 'penerbit' => $penerbit, 'tahun_terbit' => $tahun, 'stok' => $stok],
            );
        }
    }
}
