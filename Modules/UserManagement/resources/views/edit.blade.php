@extends('layouts.app')

@section('title', 'Edit User')

@section('content')
    <div class="card form">
        <h2>Edit User</h2>

        <form action="{{ route('users.update', $user) }}" method="POST" novalidate>
            @csrf
            @method('PUT')
            @include('usermanagement::_form')

            <div class="form-actions">
                <button type="submit" class="btn">Simpan Perubahan</button>
                <a href="{{ route('users.index') }}" class="btn abu">Batal</a>
            </div>
        </form>
    </div>
@endsection
