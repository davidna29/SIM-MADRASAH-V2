<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class AuthController extends Controller
{
    public function showLogin()
    {
        if (Auth::check()) {
            return $this->redirectToRole();
        }

        return view('pages.auth.login');
    }

    public function login(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'login' => ['required', 'string'],
            'password' => ['required', 'string'],
        ]);

        $field = filter_var($credentials['login'], FILTER_VALIDATE_EMAIL) ? 'email' : 'username';

        if (! Auth::attempt([$field => $credentials['login'], 'password' => $credentials['password']], $request->boolean('remember'))) {
            return back()->withErrors(['login' => 'Username/email atau kata sandi salah.'])->onlyInput('login');
        }

        // Akun yang dinonaktifkan (deaktivasi otomatis) tidak boleh login.
        if (! Auth::user()->is_active) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return back()->withErrors(['login' => 'Akun Anda telah dinonaktifkan. Silakan hubungi tata usaha.'])->onlyInput('login');
        }

        $request->session()->regenerate();

        return $this->redirectToRole();
    }

    public function showChangePassword(): View
    {
        return view('pages.auth.ubah-password');
    }

    public function updatePassword(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'current_password' => ['required', 'string'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        if (! Hash::check($validated['current_password'], Auth::user()->password)) {
            return back()->withErrors(['current_password' => 'Kata sandi saat ini tidak sesuai.'])
                ->onlyInput('current_password');
        }

        Auth::user()->update([
            'password' => Hash::make($validated['password']),
            'must_change_password' => false,
        ]);

        activity('auth')
            ->causedBy(Auth::user())
            ->log('Kata sandi berhasil diubah.');

        return $this->redirectToRole()->with('status', 'Kata sandi berhasil diubah. Selamat bekerja!');
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }

    protected function redirectToRole(): RedirectResponse
    {
        return match (Auth::user()->role) {
            'guru' => redirect()->route('guru.penugasan'),
            'guru_bk' => redirect()->route('konseling.index'),
            'orang_tua' => redirect()->route('ortu.dashboard'),
            'siswa' => redirect()->route('siswa.dashboard'),
            default => redirect()->route('dashboard'),
        };
    }
}
