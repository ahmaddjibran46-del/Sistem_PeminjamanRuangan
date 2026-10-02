<?php

namespace App\Http\Controllers\Mahasiswa;

use App\Http\Controllers\Controller;
use App\Http\Requests\StorePeminjamanRequest;
use App\Models\Peminjaman;
use App\Models\Ruangan;
use App\Services\PeminjamanService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;

class PeminjamanController extends Controller
{
    public function index(Request $request)
    {
        $mhs = Auth::user();
        $dasar = Peminjaman::where('id_mahasiswa', $mhs->id_mahasiswa);

        $daftar = (clone $dasar)->with('ruangan')
            ->status($request->query('status'))
            ->when($request->query('ruangan'), fn ($q, $r) => $q->where('id_ruangan', $r))
            ->when($request->query('tanggal'), fn ($q, $t) => $q->whereDate('tanggal_mulai', $t))
            ->when($request->query('q'), function ($q, $kata) {
                $like = '%'.addcslashes($kata, '%_\\').'%';
                $q->where(fn ($w) => $w->where('alasan', 'like', $like)
                    ->orWhereHas('ruangan', fn ($r) => $r->where('nama_ruangan', 'like', $like)));
            })
            ->latest('tanggal_mulai')
            ->paginate(8)->withQueryString();

        $hitung = (clone $dasar)->selectRaw('status, count(*) as total')->groupBy('status')->pluck('total', 'status');

        return view('mahasiswa.peminjaman.index', [
            'daftar' => $daftar,
            'hitung' => $hitung,
            'total' => $hitung->sum(),
            'ruanganOpsi' => Ruangan::orderBy('nama_ruangan')->get(['id_ruangan', 'nama_ruangan']),
        ]);
    }

    public function create(Request $request)
    {
        return view('mahasiswa.peminjaman.create', [
            'ruangan' => Ruangan::orderBy('nama_ruangan')->get(),
            'dipilih' => $request->query('ruangan'),
        ]);
    }

    public function store(StorePeminjamanRequest $request, PeminjamanService $service)
    {
        $p = $service->ajukan(Auth::user(), $request->validated(), $request->file('dokumen_pendukung'));

        return redirect()->route('peminjaman.show', $p)->with('success', 'Pengajuan berhasil dikirim.');
    }

    public function show(Peminjaman $peminjaman)
    {
        Gate::authorize('view', $peminjaman);

        return view('mahasiswa.peminjaman.show', ['p' => $peminjaman->load('ruangan')]);
    }

    public function dokumen(Peminjaman $peminjaman)
    {
        Gate::authorize('viewDokumen', $peminjaman);
        abort_unless($peminjaman->punya_dokumen && Storage::disk(PeminjamanService::DISK)->exists($peminjaman->dokumen_pendukung), 404);

        return Storage::disk(PeminjamanService::DISK)->response($peminjaman->dokumen_pendukung);
    }
}
