<?php

namespace App\Http\Controllers\Mahasiswa;

use App\Http\Controllers\Controller;
use App\Models\Ruangan;
use App\Services\JadwalRuanganService;
use Illuminate\Http\Request;

class RuanganController extends Controller
{
    public function index(Request $request, JadwalRuanganService $jadwal)
    {
        $ruangan = Ruangan::query()
            ->cari($request->query('q'))
            ->orderBy('nama_ruangan')
            ->paginate(9)
            ->withQueryString();

        return view('mahasiswa.ruangan.index', [
            'ruangan' => $ruangan,
            'total' => Ruangan::count(),
            'siap' => Ruangan::tersedia()->count(),
            'dipakai' => $jadwal->pemakaianSekarang(),
        ]);
    }

    public function show(Ruangan $ruangan, JadwalRuanganService $jadwal)
    {
        return view('mahasiswa.ruangan.show', [
            'ruangan' => $ruangan,
            'dipakai' => $jadwal->pemakaianSekarang()->get($ruangan->id_ruangan),
            'jadwal' => $ruangan->jadwal()
                ->where('status', 'terpakai')
                ->where('tanggal', '>=', now()->toDateString())
                ->orderBy('tanggal')->orderBy('jam_mulai')->limit(30)->get(),
        ]);
    }
}
