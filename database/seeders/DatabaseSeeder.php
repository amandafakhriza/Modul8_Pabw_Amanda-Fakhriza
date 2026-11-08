<?php

namespace Database\Seeders;
use App\Models\User;
use Illuminate\Database\Seeder;
use Modules\Buku\Database\Seeders\BukuDatabaseSeeder;
use Modules\Peminjaman\Database\Seeders\PeminjamanDatabaseSeeder;

class DatabaseSeeder extends Seeder
{
  
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'admin@sipustaka.test'],
            ['name' => 'Petugas Admin', 'password' => 'password', 'role' => User::ROLE_ADMIN],
        );

        User::updateOrCreate(
            ['email' => 'user@sipustaka.test'],
            ['name' => 'Anggota Perpustakaan', 'password' => 'password', 'role' => User::ROLE_USER],
        );

        $this->call([
            BukuDatabaseSeeder::class,
            PeminjamanDatabaseSeeder::class,
        ]);
    }
}
