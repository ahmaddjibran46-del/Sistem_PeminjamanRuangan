<?php

namespace App\Console\Commands;

use App\Models\Admin;
use App\Models\Mahasiswa;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class ResetPasswordPengguna extends Command
{
    protected $signature = 'pinjamruang:reset-password {identitas : NIM mahasiswa atau NIP admin}';
    protected $description = 'Reset password satu akun (mahasiswa/admin) dengan password sementara acak.';

    public function handle(): int
    {
        $identitas = (string) $this->argument('identitas');

        // Urutan sama dengan login: NIM dulu, lalu NIP.
        $akun = Mahasiswa::where('nim', $identitas)->first() ?? Admin::where('nip', $identitas)->first();

        if (! $akun) {
            $this->error('Akun dengan NIM/NIP tersebut tidak ditemukan.');

            return self::FAILURE;
        }

        $baru = Str::password(12, symbols: false);
        $akun->password = $baru; // di-hash otomatis oleh cast 'hashed' pada model
        $akun->save();

        Log::info('Password direset via artisan', ['tipe' => class_basename($akun), 'id' => $akun->getKey()]);

        $this->info("Password untuk {$akun->nama} berhasil direset.");
        $this->line("Password sementara: {$baru}");
        $this->warn('Ditampilkan sekali ini saja. Sampaikan langsung kepada pemilik akun.');

        return self::SUCCESS;
    }
}
