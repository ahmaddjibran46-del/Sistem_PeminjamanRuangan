<?php

namespace App\Console\Commands;

use App\Services\ApprovalService;
use Illuminate\Console\Command;

class SelesaikanPeminjaman extends Command
{
    protected $signature = 'peminjaman:selesaikan';
    protected $description = 'Tandai peminjaman disetujui yang waktunya sudah lewat sebagai selesai.';

    public function handle(ApprovalService $service): int
    {
        $this->info($service->selesaikanKadaluarsa().' peminjaman diselesaikan.');

        return self::SUCCESS;
    }
}
