@extends('layouts.app')

@section('title', 'Tambah Buku')

@section('content')
    <div class="card form">
        <h2>Tambah Buku</h2>

        <form action="{{ route('buku.store') }}" method="POST" novalidate>
            @csrf
            @include('buku::_form')

            <div class="form-actions">
                <button type="submit" class="btn">Simpan Buku</button>
                <a href="{{ route('buku.index') }}" class="btn abu">Batal</a>
            </div>
        </form>
    </div>
@endsection
