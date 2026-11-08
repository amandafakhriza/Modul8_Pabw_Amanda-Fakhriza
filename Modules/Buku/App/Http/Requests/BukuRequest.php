<?php

namespace Modules\Buku\App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BukuRequest extends FormRequest
{
    public function authorize(): bool
    {
        // Pembatasan admin dilakukan oleh middleware role:admin pada route
        return true;
    }

    public function rules(): array
    {
        return [
            'judul' => ['required', 'string', 'max:255'],
            'penulis' => ['required', 'string', 'max:255'],
            'penerbit' => ['required', 'string', 'max:255'],
            'tahun_terbit' => ['required', 'integer', 'digits:4', 'between:1900,'.(date('Y') + 1)],
            'stok' => ['required', 'integer', 'min:0'],
        ];
    }

    public function messages(): array
    {
        return [
            'judul.required' => 'Judul buku wajib diisi.',
            'judul.max' => 'Judul maksimal 255 karakter.',
            'penulis.required' => 'Penulis wajib diisi.',
            'penulis.max' => 'Penulis maksimal 255 karakter.',
            'penerbit.required' => 'Penerbit wajib diisi.',
            'penerbit.max' => 'Penerbit maksimal 255 karakter.',
            'tahun_terbit.required' => 'Tahun terbit wajib diisi.',
            'tahun_terbit.integer' => 'Tahun terbit harus berupa angka.',
            'tahun_terbit.digits' => 'Tahun terbit harus 4 digit.',
            'tahun_terbit.between' => 'Tahun terbit harus antara 1900 sampai '.(date('Y') + 1).'.',
            'stok.required' => 'Stok wajib diisi.',
            'stok.integer' => 'Stok harus berupa angka bulat.',
            'stok.min' => 'Stok tidak boleh kurang dari 0.',
        ];
    }
}
