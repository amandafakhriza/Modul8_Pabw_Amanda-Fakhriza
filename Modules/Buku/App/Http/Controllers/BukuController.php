<?php

namespace Modules\Buku\App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Buku\App\Http\Requests\BukuRequest;
use Modules\Buku\App\Models\Buku;

class BukuController extends Controller
{
    public function index(Request $request)
    {
        $q = trim((string) $request->query('q', ''));

        $buku = Buku::query()
            ->when($q !== '', function ($query) use ($q) {
                $query->where(function ($w) use ($q) {
                    $w->where('judul', 'like', "%{$q}%")
                        ->orWhere('penulis', 'like', "%{$q}%")
                        ->orWhere('penerbit', 'like', "%{$q}%");
                });
            })
            ->orderBy('judul')
            ->paginate(5)
            ->withQueryString();

        return view('buku::index', compact('buku', 'q'));
    }

    public function create()
    {
        return view('buku::create');
    }

    public function store(BukuRequest $request)
    {
        Buku::create($request->validated());

        return redirect()->route('buku.index')
            ->with('success', 'Data buku berhasil ditambahkan!');
    }

    public function edit(Buku $buku)
    {
        return view('buku::edit', compact('buku'));
    }

    public function update(BukuRequest $request, Buku $buku)
    {
        $buku->update($request->validated());

        return redirect()->route('buku.index')
            ->with('success', 'Data buku berhasil diperbarui!');
    }

    public function destroy(Buku $buku)
    {
        $buku->delete();

        return redirect()->route('buku.index')
            ->with('success', 'Data buku berhasil dihapus!');
    }
}
