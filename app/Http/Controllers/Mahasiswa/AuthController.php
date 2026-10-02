<?php

namespace App\Http\Controllers\Mahasiswa;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Services\Auth\MahasiswaAuthenticator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function create()
    {
        return view('auth.login', [
            'judul' => 'Selamat Datang',
            'action' => route('login'),
            'label' => 'NIM',
            'placeholder' => 'Masukkan NIM',
            'admin' => false,
        ]);
    }

    public function store(LoginRequest $request, MahasiswaAuthenticator $auth)
    {
        if (! $auth->attempt($request->username, $request->password, $request->boolean('remember'))) {
            return back()->withInput($request->only('username'))
                ->withErrors(['username' => 'NIM atau password salah.']);
        }

        $request->session()->regenerate();

        return redirect()->intended(route('ruangan.index'));
    }

    public function destroy(Request $request)
    {
        Auth::guard('mahasiswa')->logout();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
