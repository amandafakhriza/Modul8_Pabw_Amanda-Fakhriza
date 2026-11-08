@extends('layouts.app')

@section('title', 'Daftar Buku')

@section('content')
    <div class="card">
        <h2>Daftar Buku Perpustakaan</h2>

        <div class="toolbar">
            {{-- Pencarian (judul / penulis / penerbit) --}}
            <form action="{{ route('buku.index') }}" method="GET">
                <input type="text" name="q" value="{{ $q }}" placeholder="Cari judul, penulis, penerbit...">
                <button type="submit" class="btn">Cari</button>
                @if ($q !== '')
                    <a href="{{ route('buku.index') }}" class="btn abu">Reset</a>
                @endif
            </form>

            @if (auth()->user()->isAdmin())
                <a href="{{ route('buku.create') }}" class="btn">+ Tambah Buku</a>
            @endif
        </div>

        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Judul</th>
                        <th>Penulis</th>
                        <th>Penerbit</th>
                        <th class="center">Tahun Terbit</th>
                        <th class="center">Stok</th>
                        @if (auth()->user()->isAdmin())
                            <th class="center">Aksi</th>
                        @endif
                    </tr>
                </thead>
                <tbody>
                    @forelse ($buku as $item)
                        <tr>
                            <td>{{ $buku->firstItem() + $loop->index }}</td>
                            <td>{{ $item->judul }}</td>
                            <td>{{ $item->penulis }}</td>
                            <td>{{ $item->penerbit }}</td>
                            <td class="center">{{ $item->tahun_terbit }}</td>
                            <td class="center">{{ $item->stok }}</td>
                            @if (auth()->user()->isAdmin())
                                <td>
                                    <div class="aksi">
                                        <a href="{{ route('buku.edit', $item) }}" class="btn edit">Edit</a>

                                        <form action="{{ route('buku.destroy', $item) }}" method="POST"
                                              onsubmit="return confirm('Yakin ingin menghapus buku ini? Data peminjamannya ikut terhapus.')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn hapus">Hapus</button>
                                        </form>
                                    </div>
                                </td>
                            @endif
                        </tr>
                    @empty
                        <tr>
                            <td colspan="{{ auth()->user()->isAdmin() ? 7 : 6 }}" class="kosong">
                                {{ $q !== '' ? 'Buku yang dicari tidak ditemukan.' : 'Belum ada data buku.' }}
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($buku->total() > 0)
            <p class="info-page">
                Menampilkan {{ $buku->firstItem() }}-{{ $buku->lastItem() }} dari {{ $buku->total() }} buku
            </p>
        @endif

        {{ $buku->links() }}
    </div>
@endsection
