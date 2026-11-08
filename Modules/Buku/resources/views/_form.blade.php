{{-- Field form bersama untuk tambah & edit buku. Error tampil di bawah tiap input. --}}
@php
    $fields = [
        ['judul', 'Judul Buku', 'text'],
        ['penulis', 'Penulis', 'text'],
        ['penerbit', 'Penerbit', 'text'],
        ['tahun_terbit', 'Tahun Terbit', 'number'],
        ['stok', 'Stok', 'number'],
    ];
@endphp

@foreach ($fields as [$name, $label, $type])
    <label for="{{ $name }}">{{ $label }}</label>
    <input type="{{ $type }}" id="{{ $name }}" name="{{ $name }}"
           value="{{ old($name, $buku->{$name} ?? ($name === 'stok' ? 0 : '')) }}"
           class="@error($name) invalid @enderror">
    @error($name)
        <div class="field-error">{{ $message }}</div>
    @enderror
@endforeach
