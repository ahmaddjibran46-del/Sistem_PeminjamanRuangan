<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Feedback;
use App\Models\Peminjaman;
use App\Models\Ruangan;
use App\Services\JadwalRuanganService;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function __invoke(JadwalRuanganService $jadwal)
    {
        $hitung = Peminjaman::selectRaw('status, count(*) as total')->groupBy('status')->pluck('total', 'status');

        // Tren minggu berjalan (Senin-Minggu) dihitung dari tanggal pengajuan dibuat.
        $awal = now()->startOfWeek();
        $baris = Peminjaman::selectRaw('DATE(created_at) as hari, status, count(*) as total')
            ->whereBetween('created_at', [$awal, $awal->copy()->endOfWeek()])
            ->groupBy('hari', 'status')->get()->groupBy('hari');

        $tren = collect(range(0, 6))->map(function ($i) use ($awal, $baris) {
            $hari = $awal->copy()->addDays($i);
            $r = $baris->get($hari->toDateString(), collect())->pluck('total', 'status');

            return [
                'label' => ucfirst($hari->translatedFormat('D')),
                'disetujui' => (int) ($r['disetujui'] ?? 0) + (int) ($r['selesai'] ?? 0),
                'menunggu' => (int) ($r['menunggu'] ?? 0),
                'ditolak' => (int) ($r['ditolak'] ?? 0),
            ];
        });

        return view('admin.dashboard', [
            'menunggu' => (int) ($hitung['menunggu'] ?? 0),
            'disetujui' => (int) ($hitung['disetujui'] ?? 0) + (int) ($hitung['selesai'] ?? 0),
            'ditolak' => (int) ($hitung['ditolak'] ?? 0),
            'feedbackBaru' => Feedback::whereNull('dibaca_at')->count(),
            'totalRuangan' => Ruangan::count(),
            'ruanganTersedia' => Ruangan::tersedia()->count(),
            'tren' => $tren,
            'trenMax' => max(1, $tren->max(fn ($t) => $t['disetujui'] + $t['menunggu'] + $t['ditolak'])),
            'terbaru' => Peminjaman::with('ruangan')->latest('created_at')->limit(5)->get(),
            'hariIni' => $jadwal->hariIni(),
            'bulan' => now()->translatedFormat('F Y'),
        ]);
    }
}
