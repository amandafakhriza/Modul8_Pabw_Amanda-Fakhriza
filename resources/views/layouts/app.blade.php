<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'SiPustaka') - SiPustaka</title>
    <link rel="stylesheet" href="{{ asset('css/sipustaka.css') }}">
    @stack('styles')
</head>
<body>

@auth
    <nav class="navbar">
        <a href="{{ route('buku.index') }}" class="brand">&#128218; SiPustaka</a>

        <div class="menu">
            <a href="{{ route('buku.index') }}" class="{{ request()->routeIs('buku.*') ? 'active' : '' }}">Buku</a>
            <a href="{{ route('peminjaman.index') }}" class="{{ request()->routeIs('peminjaman.*') ? 'active' : '' }}">Peminjaman</a>

            @if (auth()->user()->isAdmin())
                <a href="{{ route('users.index') }}" class="{{ request()->routeIs('users.*') ? 'active' : '' }}">Kelola User</a>
            @endif
        </div>

        <div class="akun">
            <span>{{ auth()->user()->name }}</span>
            <span class="badge {{ auth()->user()->role }}">{{ auth()->user()->role }}</span>

            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" class="btn abu">Logout</button>
            </form>
        </div>
    </nav>
@endauth

<main class="wrap">
    {{-- Pesan sukses / gagal dari redirect (tambah, ubah, hapus, login, dll.) --}}
    @if (session('success'))
        <div class="alert success">{{ session('success') }}</div>
    @endif

    @if (session('error'))
        <div class="alert error">{{ session('error') }}</div>
    @endif

    @yield('content')
</main>

@stack('scripts')
</body>
</html>
