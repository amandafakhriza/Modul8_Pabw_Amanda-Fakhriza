<?php

namespace Modules\UserManagement\App\Http\Requests;

use App\Models\User;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class UserRequest extends FormRequest
{
    public function authorize(): bool
    {
        // Pembatasan admin dilakukan oleh middleware role:admin pada route
        return true;
    }

    public function rules(): array
    {
        /** @var User|null $user user yang sedang diedit (null saat tambah) */
        $user = $this->route('user');

        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required', 'email', 'max:255',
                Rule::unique('users', 'email')->ignore($user?->id),
            ],
            // Wajib saat tambah; saat edit boleh dikosongkan (password tidak diubah)
            'password' => [$user ? 'nullable' : 'required', 'confirmed', Password::min(8)],
            'role' => ['required', Rule::in(User::ROLES)],
        ];
    }

    /**
     * Admin tidak boleh menurunkan role akunnya sendiri.
     */
    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $v) {
            $user = $this->route('user');

            if ($user && $user->id === $this->user()->id && $this->input('role') !== User::ROLE_ADMIN) {
                $v->errors()->add('role', 'Anda tidak dapat menurunkan role akun Anda sendiri.');
            }
        });
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Nama wajib diisi.',
            'name.max' => 'Nama maksimal 255 karakter.',
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'email.unique' => 'Email sudah dipakai user lain.',
            'password.required' => 'Password wajib diisi.',
            'password.confirmed' => 'Konfirmasi password tidak cocok.',
            'password.min' => 'Password minimal 8 karakter.',
            'role.required' => 'Role wajib dipilih.',
            'role.in' => 'Role harus admin atau user.',
        ];
    }
}
