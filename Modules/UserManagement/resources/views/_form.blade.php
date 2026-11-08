@php($editing = isset($user))

<label for="name">Nama</label>
<input type="text" id="name" name="name" value="{{ old('name', $user->name ?? '') }}"
       class="@error('name') invalid @enderror">
@error('name')
    <div class="field-error">{{ $message }}</div>
@enderror

<label for="email">Email</label>
<input type="email" id="email" name="email" value="{{ old('email', $user->email ?? '') }}"
       class="@error('email') invalid @enderror">
@error('email')
    <div class="field-error">{{ $message }}</div>
@enderror

<label for="role">Role</label>
<select id="role" name="role" class="@error('role') invalid @enderror">
    @foreach (\App\Models\User::ROLES as $role)
        <option value="{{ $role }}" @selected(old('role', $user->role ?? 'user') === $role)>{{ ucfirst($role) }}</option>
    @endforeach
</select>
@error('role')
    <div class="field-error">{{ $message }}</div>
@enderror

<label for="password">Password {{ $editing ? '(kosongkan jika tidak diubah)' : '' }}</label>
<input type="password" id="password" name="password" class="@error('password') invalid @enderror">
@error('password')
    <div class="field-error">{{ $message }}</div>
@enderror

<label for="password_confirmation">Konfirmasi Password</label>
<input type="password" id="password_confirmation" name="password_confirmation">
