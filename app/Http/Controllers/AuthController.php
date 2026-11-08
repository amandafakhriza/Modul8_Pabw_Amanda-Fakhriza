<?php

namespace App\Http\Controllers;

use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * Authentication manual (register, login, logout) -- berada di luar modul HMVC.
 */
class AuthController extends Controller
{
    public function showRegister()
    {
        return view('auth.register');
    }

    public function register(RegisterRequest $request): RedirectResponse
    {
        $user = User::create([
            ...$request->validated(),
            // Akun hasil register WAJIB berperan user, apa pun isi request
            'role' => User::ROLE_USER,
        ]);

        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->route('buku.index')
            ->with('success', 'Registrasi berhasil. Selamat datang, '.$user->name.'!');
    }

    public function showLogin()
    {
        return view('auth.login');
    }

    public function login(LoginRequest $request): RedirectResponse
    {
        $credentials = $request->validated();

        if (! Auth::attempt($credentials, $request->boolean('remember'))) {
            return back()
                ->withInput($request->only('email'))
                ->withErrors(['email' => 'Email atau password salah.']);
        }

        $request->session()->regenerate();

        return redirect()->intended(route('buku.index'))
            ->with('success', 'Login berhasil. Selamat datang, '.Auth::user()->name.'!');
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('success', 'Anda berhasil logout.');
    }
}
