@extends('layouts.app')

@section('title', 'Kelola User')

@section('content')
    <div class="card">
        <h2>Manajemen User</h2>

        <div class="toolbar">
            <span class="sub" style="margin:0">Hanya admin yang dapat membuka halaman ini.</span>
            <a href="{{ route('users.create') }}" class="btn">+ Tambah User</a>
        </div>

        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Nama</th>
                        <th>Email</th>
                        <th class="center">Role</th>
                        <th class="center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($users as $item)
                        <tr>
                            <td>{{ $users->firstItem() + $loop->index }}</td>
                            <td>
                                {{ $item->name }}
                                @if ($item->id === auth()->id())
                                    <span class="badge">Anda</span>
                                @endif
                            </td>
                            <td>{{ $item->email }}</td>
                            <td class="center"><span class="badge {{ $item->role }}">{{ $item->role }}</span></td>
                            <td>
                                <div class="aksi">
                                    <a href="{{ route('users.edit', $item) }}" class="btn edit">Edit</a>

                                    {{-- Akun sendiri tidak dapat dihapus --}}
                                    @if ($item->id !== auth()->id())
                                        <form action="{{ route('users.destroy', $item) }}" method="POST"
                                              onsubmit="return confirm('Yakin ingin menghapus user ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn hapus">Hapus</button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="kosong">Belum ada user.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{ $users->links() }}
    </div>
@endsection
