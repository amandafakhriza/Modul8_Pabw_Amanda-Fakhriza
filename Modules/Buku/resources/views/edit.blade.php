@extends('layouts.app')

@section('title', 'Edit Buku')

@section('content')
    <div class="card form">
        <h2>Edit Data Buku</h2>

        <form action="{{ route('buku.update', $buku) }}" method="POST" novalidate>
            @csrf
            @method('PUT')
            @include('buku::_form')

            <div class="form-actions">
                <button type="submit" class="btn">Simpan Perubahan</button>
                <a href="{{ route('buku.index') }}" class="btn abu">Batal</a>
            </div>
        </form>
    </div>
@endsection
