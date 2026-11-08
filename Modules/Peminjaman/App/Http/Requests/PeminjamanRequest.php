<?php

namespace Modules\Peminjaman\App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PeminjamanRequest extends FormRequest
{
    public function authorize(): bool
    {
        // Admin dan user sama-sama boleh (cukup login, dijaga middleware auth)
        return true;
    }

    public function rules(): array
    {
        return [
            'buku_id' => ['required', 'exists:buku,id'],
            'nama_peminjam' => ['required', 'string', 'max:255'],
            'tanggal_pinjam' => ['required', 'date'],
            'tanggal_kembali' => ['nullable', 'date', 'after_or_equal:tanggal_pinjam'],
            'status' => ['required', 'in:Dipinjam,Dikembalikan'],
            // Berkas bukti: jpg/png/pdf, maksimal 2 MB (2048 KB)
            'bukti' => ['nullable', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:2048'],
        ];
    }

    public function messages(): array
    {
        return [
            'buku_id.required' => 'Buku wajib dipilih.',
            'buku_id.exists' => 'Buku yang dipilih tidak ditemukan.',
            'nama_peminjam.required' => 'Nama peminjam wajib diisi.',
            'nama_peminjam.max' => 'Nama peminjam maksimal 255 karakter.',
            'tanggal_pinjam.required' => 'Tanggal pinjam wajib diisi.',
            'tanggal_pinjam.date' => 'Tanggal pinjam tidak valid.',
            'tanggal_kembali.date' => 'Tanggal kembali tidak valid.',
            'tanggal_kembali.after_or_equal' => 'Tanggal kembali tidak boleh sebelum tanggal pinjam.',
            'status.required' => 'Status wajib dipilih.',
            'status.in' => 'Status harus Dipinjam atau Dikembalikan.',
            'bukti.file' => 'Bukti harus berupa berkas.',
            'bukti.mimes' => 'Bukti harus berformat jpg, png, atau pdf.',
            'bukti.max' => 'Ukuran bukti maksimal 2 MB.',
            'bukti.uploaded' => 'Berkas gagal diunggah (ukuran maksimal 2 MB).',
        ];
    }
}
