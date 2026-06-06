<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;

class LoginController extends Controller
{
    private const MAX_ATTEMPTS = 3;
    private const DECAY_SECONDS = 300; // 5 menit lockout

    public function showLoginForm()
    {
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }
        return view('auth.login');
    }

    private function throttleKey(Request $request): string
    {
        return 'login.' . strtolower($request->input('username')) . '.' . $request->ip();
    }

    public function login(Request $request)
    {
        $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ], [
            'username.required' => 'Username wajib diisi.',
            'password.required' => 'Password wajib diisi.',
        ]);

        $key = $this->throttleKey($request);

        if (RateLimiter::tooManyAttempts($key, self::MAX_ATTEMPTS)) {
            $seconds = RateLimiter::availableIn($key);
            $menit   = ceil($seconds / 60);
            return back()->withErrors([
                'username' => "Akun dikunci sementara akibat terlalu banyak percobaan gagal. Coba lagi dalam {$menit} menit.",
            ])->withInput($request->only('username'));
        }

        $credentials = $request->only('username', 'password');
        $remember    = $request->boolean('remember');

        if (!Auth::attempt($credentials, $remember)) {
            RateLimiter::hit($key, self::DECAY_SECONDS);
            $attempts   = RateLimiter::attempts($key);
            $remaining  = max(0, self::MAX_ATTEMPTS - $attempts);
            $msg = $remaining > 0
                ? "Username atau password salah. Sisa percobaan: {$remaining} kali."
                : 'Akun dikunci sementara. Coba lagi dalam 5 menit.';
            return back()->withErrors(['username' => $msg])->withInput($request->only('username'));
        }

        $user = Auth::user();

        if (!$user->aktif) {
            Auth::logout();
            return back()->withErrors([
                'username' => 'Akun Anda tidak aktif. Hubungi administrator.',
            ])->withInput($request->only('username'));
        }

        RateLimiter::clear($key);
        $request->session()->regenerate();

        return redirect()->route('dashboard');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login');
    }
}
