<?php

namespace App\Services;

use App\Models\JadwalRuangan;
use App\Models\Peminjaman;
use Carbon\CarbonInterface;
use Illuminate\Support\Collection;

class JadwalRuanganService
{
    /**
     * Memecah rentang waktu menjadi segmen per hari (jadwal_ruangan menyimpan satu tanggal per baris).
     *
     * @return array<int, array{tanggal: string, jam_mulai: string, jam_selesai: string}>
     */
    public function segmen(CarbonInterface $mulai, CarbonInterface $selesai): array
    {
        $hasil = [];
        $hari = $mulai->copy()->startOfDay();
        $akhir = $selesai->copy()->startOfDay();

        while ($hari->lte($akhir)) {
            $awal = $hari->isSameDay($mulai) ? $mulai->format('H:i:s') : '00:00:00';
            $tutup = $hari->isSameDay($selesai) ? $selesai->format('H:i:s') : '23:59:59';

            if ($awal < $tutup) {
                $hasil[] = ['tanggal' => $hari->toDateString(), 'jam_mulai' => $awal, 'jam_selesai' => $tutup];
            }
            $hari->addDay();
        }

        return $hasil;
    }

    /**
     * Overlap jika: mulai_baru < selesai_lama DAN selesai_baru > mulai_lama.
     * Slot bersambung (12:00 setelah 12:00) TIDAK dianggap bentrok.
     */
    public function bentrok(int $idRuangan, CarbonInterface $mulai, CarbonInterface $selesai, ?int $kecualiPeminjaman = null): bool
    {
        foreach ($this->segmen($mulai, $selesai) as $s) {
            $ada = JadwalRuangan::query()
                ->where('id_ruangan', $idRuangan)
                ->where('status', JadwalRuangan::TERPAKAI)
                ->where('tanggal', $s['tanggal'])
                ->where('jam_mulai', '<', $s['jam_selesai'])
                ->where('jam_selesai', '>', $s['jam_mulai'])
                ->when($kecualiPeminjaman, fn ($q) => $q->where('id_peminjaman', '!=', $kecualiPeminjaman))
                ->exists();

            if ($ada) {
                return true;
            }
        }

        return false;
    }

    public function buat(Peminjaman $p): void
    {
        foreach ($this->segmen($p->tanggal_mulai, $p->tanggal_selesai) as $s) {
            JadwalRuangan::create($s + [
                'id_ruangan' => $p->id_ruangan,
                'id_peminjaman' => $p->id_peminjaman,
                'status' => JadwalRuangan::TERPAKAI,
            ]);
        }
    }

    public function tandaiSelesai(Peminjaman $p): void
    {
        JadwalRuangan::where('id_peminjaman', $p->id_peminjaman)->update(['status' => JadwalRuangan::SELESAI]);
    }

    /** id_ruangan => jam_selesai untuk ruangan yang sedang dipakai saat ini (1 query). */
    public function pemakaianSekarang(): Collection
    {
        $now = now();

        return JadwalRuangan::query()
            ->where('status', JadwalRuangan::TERPAKAI)
            ->where('tanggal', $now->toDateString())
            ->where('jam_mulai', '<=', $now->format('H:i:s'))
            ->where('jam_selesai', '>', $now->format('H:i:s'))
            ->pluck('jam_selesai', 'id_ruangan');
    }

    public function hariIni(): Collection
    {
        return JadwalRuangan::with(['ruangan', 'peminjaman'])
            ->where('status', JadwalRuangan::TERPAKAI)
            ->where('tanggal', now()->toDateString())
            ->orderBy('jam_mulai')
            ->get();
    }
}
