<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class HashPasswordLama extends Command
{
    protected $signature = 'pinjamruang:hash-password';
    protected $description = 'Hash password plaintext lama pada tabel mahasiswa & admin (aman dijalankan berulang).';

    public function handle(): int
    {
        $total = 0;
        foreach (['mahasiswa' => 'id_mahasiswa', 'admin' => 'id_admin'] as $tabel => $pk) {
            DB::table($tabel)->select($pk, 'password')->orderBy($pk)->each(function ($row) use ($tabel, $pk, &$total) {
                if (! Hash::isHashed($row->password)) {
                    DB::table($tabel)->where($pk, $row->{$pk})->update(['password' => Hash::make($row->password)]);
                    $total++;
                }
            });
        }
        $this->info("Selesai. {$total} password di-hash.");

        return self::SUCCESS;
    }
}
