<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use App\Models\Admin;
use App\Services\Auth\MahasiswaAuthenticator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function create()
    {
        if (Auth::guard('admin')->check()) {
            return redirect()->route('admin.dashboard');
        }
        if (Auth::guard('mahasiswa')->check()) {
            return redirect()->route('ruangan.index');
        }

        return view('auth.login', [
            'judul' => 'Selamat Datang',
            'action' => route('login'),
            'admin' => false,
        ]);
    }

    public function store(LoginRequest $request, MahasiswaAuthenticator $mahasiswa)
    {
        $username = trim($request->username);
        $remember = $request->boolean('remember');

        // 1) Mahasiswa: NIM + password
        if ($mahasiswa->attempt($username, $request->password, $remember)) {
            $request->session()->regenerate();

            return redirect()->route('ruangan.index');
        }

        // 2) Admin: NIP + password
        $admin = Admin::where('status_akun', 'aktif')->where('nip', $username)->first();

        if ($admin && Hash::check($request->password, $admin->password)) {
            Auth::guard('admin')->login($admin, $remember);
            $request->session()->regenerate();

            return redirect()->route('admin.dashboard');
        }

        return back()->withInput($request->only('username'))
            ->withErrors(['username' => 'Username atau password salah.']);
    }

    public function destroy(Request $request)
    {
        Auth::guard('mahasiswa')->logout();
        Auth::guard('admin')->logout();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}