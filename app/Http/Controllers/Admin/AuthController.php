<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function create()
    {
        return view('auth.login', [
            'judul' => 'Masuk Admin',
            'action' => route('admin.login'),
            'label' => 'Nomor Telepon',
            'placeholder' => 'Masukkan nomor telepon',
            'admin' => true,
        ]);
    }

    public function store(LoginRequest $request)
    {
        $ok = Auth::guard('admin')->attempt(
            ['no_telfon' => $request->username, 'password' => $request->password, 'status_akun' => 'aktif'],
            $request->boolean('remember')
        );

        if (! $ok) {
            return back()->withInput($request->only('username'))
                ->withErrors(['username' => 'Nomor telepon atau password salah.']);
        }

        $request->session()->regenerate();

        return redirect()->intended(route('admin.dashboard'));
    }

    public function destroy(Request $request)
    {
        Auth::guard('admin')->logout();
        $request->session()->regenerateToken();

        return redirect()->route('admin.login');
    }
}
