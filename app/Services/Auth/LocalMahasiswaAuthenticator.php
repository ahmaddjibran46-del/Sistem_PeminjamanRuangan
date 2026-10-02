<?php

namespace App\Services\Auth;

use Illuminate\Support\Facades\Auth;

class LocalMahasiswaAuthenticator implements MahasiswaAuthenticator
{
    public function attempt(string $nim, string $password, bool $remember = false): bool
    {
        return Auth::guard('mahasiswa')->attempt(
            ['nim' => $nim, 'password' => $password, 'status_akun' => 'aktif'],
            $remember
        );
    }
}
