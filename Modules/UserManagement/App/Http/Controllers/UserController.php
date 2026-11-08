<?php

namespace Modules\UserManagement\App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use Modules\UserManagement\App\Http\Requests\UserRequest;

class UserController extends Controller
{
    public function index()
    {
        $users = User::orderBy('name')->paginate(10);

        return view('usermanagement::index', compact('users'));
    }

    public function create()
    {
        return view('usermanagement::create');
    }

    public function store(UserRequest $request)
    {
        User::create($request->validated());

        return redirect()->route('users.index')
            ->with('success', 'Data user berhasil ditambahkan!');
    }

    public function edit(User $user)
    {
        return view('usermanagement::edit', compact('user'));
    }

    public function update(UserRequest $request, User $user)
    {
        $data = $request->validated();

        // Password kosong = tidak diganti
        if (empty($data['password'])) {
            unset($data['password']);
        }

        $user->update($data);

        return redirect()->route('users.index')
            ->with('success', 'Data user berhasil diperbarui!');
    }

    public function destroy(User $user)
    {
        // Admin tidak boleh menghapus akunnya sendiri
        if ($user->id === auth()->id()) {
            return redirect()->route('users.index')
                ->with('error', 'Anda tidak dapat menghapus akun Anda sendiri.');
        }

        $user->delete();

        return redirect()->route('users.index')
            ->with('success', 'Data user berhasil dihapus!');
    }
}
