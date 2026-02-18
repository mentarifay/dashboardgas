<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    /**
     * Show login form
     */
    public function showLogin()
    {
        // Redirect if already logged in
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }

        return view('auth.login');
    }

    /**
     * Handle login
     */
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|min:6',
        ], [
            'email.required' => 'Email wajib diisi',
            'email.email' => 'Format email tidak valid',
            'password.required' => 'Password wajib diisi',
            'password.min' => 'Password minimal 6 karakter',
        ]);

        $credentials = $request->only('email', 'password');
        $remember = $request->has('remember');

        // Attempt login
        if (Auth::attempt($credentials, $remember)) {
            $user = Auth::user();

            // Check if user is active
            if ($user->status !== 'active') {
                Auth::logout();
                return back()->with('error', 'Akun Anda tidak aktif. Hubungi administrator.');
            }

            $request->session()->regenerate();

            // Log activity
            AuditLog::log('login');

            // Redirect based on role
            return redirect()->intended(route('dashboard'))->with('success', 'Selamat datang, ' . $user->name . '!');
        }

        // Login failed
        return back()->with('error', 'Email atau password salah')->withInput($request->only('email'));
    }

    /**
     * Handle logout
     */
    public function logout(Request $request)
    {
        // Log activity before logout
        AuditLog::log('logout');

        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('success', 'Anda telah keluar dari sistem');
    }
}