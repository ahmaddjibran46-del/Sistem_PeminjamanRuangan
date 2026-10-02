<?php

namespace Database\Seeders;

use App\Models\Admin;
use Illuminate\Database\Seeder;

/**
 * DEVELOPMENT SEED SAJA. Jangan dijalankan di production.
 * Membuat 1 akun admin agar bisa login (tabel admin pada dump SQL kosong).
 * Login: no telepon 081234567890 / password: admin12345  (segera ganti!)
 */
class DevAdminSeeder extends Seeder
{
    public function run(): void
    {
        if (app()->isProduction()) {
            return;
        }

        Admin::firstOrCreate(
            ['no_telfon' => '081234567890'],
            ['nama' => 'Admin Dev', 'password' => 'admin12345', 'role' => 'admin', 'status_akun' => 'aktif']
        );
    }
}
