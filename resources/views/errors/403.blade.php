@extends('layouts.app')

@section('title', '403 Forbidden')

@section('content')
    <div class="card err-page">
        <div class="kode">403</div>
        <h2>Akses Ditolak</h2>
        <p>{{ $exception->getMessage() ?: 'Anda tidak memiliki hak akses ke halaman ini.' }}</p>
        <p>Halaman ini hanya dapat dibuka oleh <strong>admin</strong>.</p>

        <a href="{{ auth()->check() ? route('buku.index') : route('login') }}" class="btn">
            Kembali
        </a>
    </div>
@endsection
