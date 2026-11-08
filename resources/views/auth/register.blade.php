@extends('layouts.app')

@section('title', 'Register')

@section('content')
    <div class="card narrow">
        <h2>&#128221; Daftar Akun</h2>
        <p class="sub">Akun baru otomatis berperan sebagai anggota (user).</p>

        <form action="{{ route('register.store') }}" method="POST" novalidate>
            @csrf

            <label for="name">Nama Lengkap</label>
            <input type="text" id="name" name="name" value="{{ old('name') }}"
                   class="@error('name') invalid @enderror" autofocus>
            @error('name')
                <div class="field-error">{{ $message }}</div>
            @enderror

            <label for="email">Email</label>
            <input type="email" id="email" name="email" value="{{ old('email') }}"
                   class="@error('email') invalid @enderror">
            @error('email')
                <div class="field-error">{{ $message }}</div>
            @enderror

            <label for="password">Password</label>
            <input type="password" id="password" name="password"
                   class="@error('password') invalid @enderror">
            @error('password')
                <div class="field-error">{{ $message }}</div>
            @enderror

            <label for="password_confirmation">Konfirmasi Password</label>
            <input type="password" id="password_confirmation" name="password_confirmation">

            <div class="form-actions">
                <button type="submit" class="btn block">Daftar</button>
            </div>
        </form>

        <p class="alt">Sudah punya akun? <a href="{{ route('login') }}">Login</a></p>
    </div>
@endsection
