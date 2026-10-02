<?php

namespace App\Services;

use App\Exceptions\JadwalBentrokException;
use App\Models\Peminjaman;
use App\Models\Ruangan;
use DomainException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ApprovalService
{
    public function __construct(private JadwalRuanganService $jadwal) {}

    public function setujui(Peminjaman $peminjaman, ?string $catatan = null): Peminjaman
    {
        return DB::transaction(function () use ($peminjaman, $catatan) {
            // Kunci baris ruangan: approval pada ruangan yang sama diproses bergantian (anti race condition).
            $ruangan = Ruangan::whereKey($peminjaman->id_ruangan)->lockForUpdate()->firstOrFail();
            $p = Peminjaman::whereKey($peminjaman->getKey())->lockForUpdate()->firstOrFail();

            if ($p->status !== Peminjaman::MENUNGGU) {
                throw new DomainException('Pengajuan ini sudah diproses sebelumnya.');
            }
            if (! $ruangan->is_tersedia) {
                throw new DomainException('Ruangan sedang tidak tersedia, pengajuan tidak dapat disetujui.');
            }
            if ($this->jadwal->bentrok($p->id_ruangan, $p->tanggal_mulai, $p->tanggal_selesai, $p->id_peminjaman)) {
                throw new JadwalBentrokException;
            }

            $p->update(['status' => Peminjaman::DISETUJUI, 'catatan_admin' => $catatan ?? '']);
            $this->jadwal->buat($p);

            Log::info('Pengajuan disetujui', ['peminjaman' => $p->id_peminjaman, 'admin' => auth('admin')->id()]);

            return $p;
        });
    }

    public function tolak(Peminjaman $peminjaman, string $catatan): Peminjaman
    {
        return DB::transaction(function () use ($peminjaman, $catatan) {
            $p = Peminjaman::whereKey($peminjaman->getKey())->lockForUpdate()->firstOrFail();

            if ($p->status !== Peminjaman::MENUNGGU) {
                throw new DomainException('Pengajuan ini sudah diproses sebelumnya.');
            }

            $p->update(['status' => Peminjaman::DITOLAK, 'catatan_admin' => $catatan]);
            Log::info('Pengajuan ditolak', ['peminjaman' => $p->id_peminjaman, 'admin' => auth('admin')->id()]);

            return $p;
        });
    }

    /** Riwayat tidak dihapus: hanya status peminjaman & jadwal diubah menjadi selesai. */
    public function selesaikan(Peminjaman $peminjaman): Peminjaman
    {
        return DB::transaction(function () use ($peminjaman) {
            $p = Peminjaman::whereKey($peminjaman->getKey())->lockForUpdate()->firstOrFail();

            if ($p->status !== Peminjaman::DISETUJUI) {
                throw new DomainException('Hanya peminjaman yang disetujui yang dapat diselesaikan.');
            }

            $p->update(['status' => Peminjaman::SELESAI]);
            $this->jadwal->tandaiSelesai($p);

            return $p;
        });
    }

    public function selesaikanKadaluarsa(): int
    {
        $jumlah = 0;
        Peminjaman::where('status', Peminjaman::DISETUJUI)
            ->where('tanggal_selesai', '<', now())
            ->each(function (Peminjaman $p) use (&$jumlah) {
                $this->selesaikan($p);
                $jumlah++;
            });

        return $jumlah;
    }
}
