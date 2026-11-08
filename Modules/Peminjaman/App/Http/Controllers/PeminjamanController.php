<?php

namespace Modules\Peminjaman\App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use Modules\Buku\App\Models\Buku;
use Modules\Peminjaman\App\Http\Requests\PeminjamanRequest;
use Modules\Peminjaman\App\Models\Peminjaman;
use Throwable;

/**
 * CRUD Peminjaman berbasis AJAX: semua aksi mengembalikan JSON,
 * halaman tidak pernah di-refresh.
 */
class PeminjamanController extends Controller
{
    // GET /peminjaman -> halaman (HTML) atau daftar data (JSON untuk AJAX)
    public function index(Request $request)
    {
        if ($request->expectsJson()) {
            return response()->json([
                'data' => Peminjaman::with('buku:id,judul')->latest('id')->get(),
                'buku' => Buku::orderBy('judul')->get(['id', 'judul', 'stok']),
            ]);
        }

        return view('peminjaman::index');
    }

    // POST /peminjaman -> tambah
    public function store(PeminjamanRequest $request): JsonResponse
    {
        $data = $request->safe()->except('bukti');
        $pathBaru = $request->hasFile('bukti')
            ? $request->file('bukti')->store('bukti', 'public')
            : null;

        try {
            $peminjaman = DB::transaction(function () use ($data, $pathBaru) {
                if ($data['status'] === 'Dipinjam') {
                    $this->kurangiStok((int) $data['buku_id'], 'Stok buku sudah habis.');
                }

                return Peminjaman::create([...$data, 'bukti' => $pathBaru]);
            });
        } catch (Throwable $e) {
            $this->hapusBerkas($pathBaru);
            throw $e;
        }

        return response()->json([
            'message' => 'Data peminjaman berhasil ditambahkan.',
            'data' => $peminjaman->load('buku:id,judul'),
        ], 201);
    }

    // GET /peminjaman/{id} -> satu data untuk mengisi form edit
    public function show(int $id): JsonResponse
    {
        return response()->json([
            'data' => Peminjaman::with('buku:id,judul')->findOrFail($id),
        ]);
    }

    // PUT /peminjaman/{id} -> ubah (berkas lama diganti jika ada berkas baru)
    public function update(PeminjamanRequest $request, int $id): JsonResponse
    {
        $data = $request->safe()->except('bukti');
        $pathBaru = $request->hasFile('bukti')
            ? $request->file('bukti')->store('bukti', 'public')
            : null;
        $pathLama = null;

        try {
            $peminjaman = DB::transaction(function () use ($data, $id, $pathBaru, &$pathLama) {
                $peminjaman = Peminjaman::where('id', $id)->lockForUpdate()->firstOrFail();

                // Kembalikan stok jika sebelumnya masih dipinjam
                if ($peminjaman->status === 'Dipinjam') {
                    Buku::where('id', $peminjaman->buku_id)->increment('stok');
                }

                // Kurangi stok jika status baru Dipinjam
                if ($data['status'] === 'Dipinjam') {
                    $this->kurangiStok((int) $data['buku_id'], 'Stok buku yang dipilih sudah habis.');
                }

                if ($pathBaru) {
                    $pathLama = $peminjaman->bukti;
                    $data['bukti'] = $pathBaru;
                }

                $peminjaman->update($data);

                return $peminjaman;
            });
        } catch (Throwable $e) {
            $this->hapusBerkas($pathBaru);
            throw $e;
        }

        // Transaksi sukses -> berkas lama aman dihapus dari storage
        $this->hapusBerkas($pathLama);

        return response()->json([
            'message' => 'Data peminjaman berhasil diperbarui.',
            'data' => $peminjaman->load('buku:id,judul'),
        ]);
    }

    // DELETE /peminjaman/{id} -> hapus data + berkas di storage
    public function destroy(int $id): JsonResponse
    {
        $path = DB::transaction(function () use ($id) {
            $peminjaman = Peminjaman::where('id', $id)->lockForUpdate()->firstOrFail();

            if ($peminjaman->status === 'Dipinjam') {
                Buku::where('id', $peminjaman->buku_id)->increment('stok');
            }

            $peminjaman->delete();

            return $peminjaman->bukti;
        });

        $this->hapusBerkas($path);

        return response()->json(['message' => 'Data peminjaman berhasil dihapus.']);
    }

    // Kurangi stok 1 dengan penguncian baris; gagal -> error 422 pada field buku_id
    private function kurangiStok(int $bukuId, string $pesan): void
    {
        $buku = Buku::where('id', $bukuId)->lockForUpdate()->firstOrFail();

        if ($buku->stok < 1) {
            throw ValidationException::withMessages(['buku_id' => $pesan]);
        }

        $buku->decrement('stok');
    }

    private function hapusBerkas(?string $path): void
    {
        if ($path) {
            Storage::disk('public')->delete($path);
        }
    }
}
