@extends('layouts.app')

@section('title', 'Tambah User')

@section('content')
    <div class="card form">
        <h2>Tambah User</h2>

        <form action="{{ route('users.store') }}" method="POST" novalidate>
            @csrf
            @include('usermanagement::_form')

            <div class="form-actions">
                <button type="submit" class="btn">Simpan User</button>
                <a href="{{ route('users.index') }}" class="btn abu">Batal</a>
            </div>
        </form>
    </div>
@endsection
