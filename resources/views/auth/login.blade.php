@extends('layouts.app')

@section('title', 'Login')

@section('content')
    <div class="card narrow">
        <h2>&#128218; Login SiPustaka</h2>
        <p class="sub">Masuk untuk mengelola data perpustakaan.</p>

        <form action="{{ route('login.store') }}" method="POST" novalidate>
            @csrf

            <label for="email">Email</label>
            <input type="email" id="email" name="email" value="{{ old('email') }}"
                   class="@error('email') invalid @enderror" autofocus>
            @error('email')
                <div class="field-error">{{ $message }}</div>
            @enderror

            <label for="password">Password</label>
            <input type="password" id="password" name="password"
                   class="@error('password') invalid @enderror">
            @error('password')
                <div class="field-error">{{ $message }}</div>
            @enderror

            <label class="checkbox">
                <input type="checkbox" name="remember" value="1"> Ingat saya
            </label>

            <div class="form-actions">
                <button type="submit" class="btn block">Masuk</button>
            </div>
        </form>

        <p class="alt">Belum punya akun? <a href="{{ route('register') }}">Daftar di sini</a></p>
    </div>
@endsection
