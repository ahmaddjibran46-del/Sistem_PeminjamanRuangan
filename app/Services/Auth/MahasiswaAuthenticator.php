<?php

namespace App\Services\Auth;

/**
 * Abstraksi metode login mahasiswa (enum mahasiswa.metode_login: daftar | siakad | scan_ktm).
 * Saat ini hanya login lokal (NIM + password). Untuk SIAKAD / scan KTM, buat implementasi
 * baru dari interface ini lalu ganti binding di AppServiceProvider. Tidak ada integrasi palsu.
 */
interface MahasiswaAuthenticator
{
    public function attempt(string $nim, string $password, bool $remember = false): bool;
}
