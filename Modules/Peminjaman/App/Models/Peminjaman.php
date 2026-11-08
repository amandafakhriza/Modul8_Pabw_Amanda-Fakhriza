<?php

namespace Modules\Peminjaman\App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Modules\Buku\App\Models\Buku;

class Peminjaman extends Model
{
    protected $table = 'peminjaman';

    protected $fillable = [
        'buku_id',
        'nama_peminjam',
        'tanggal_pinjam',
        'tanggal_kembali',
        'status',
        'bukti',
    ];

    // Ikut dikirim di JSON agar tautan berkas bisa dibuka dari tabel AJAX
    protected $appends = ['bukti_url'];

    protected function casts(): array
    {
        return [
            'tanggal_pinjam' => 'date:Y-m-d',
            'tanggal_kembali' => 'date:Y-m-d',
        ];
    }

    public function buku()
    {
        return $this->belongsTo(Buku::class, 'buku_id');
    }

    // URL publik berkas bukti (butuh: php artisan storage:link)
    protected function buktiUrl(): Attribute
    {
        return Attribute::get(
            fn () => $this->bukti ? asset('storage/'.$this->bukti) : null
        );
    }
}
