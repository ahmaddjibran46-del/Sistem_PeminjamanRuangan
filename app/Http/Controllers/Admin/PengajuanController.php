<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\KeputusanPengajuanRequest;
use App\Models\JadwalRuangan;
use App\Models\Peminjaman;
use App\Services\ApprovalService;
use App\Services\JadwalRuanganService;
use App\Services\PeminjamanService;
use DomainException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;

class PengajuanController extends Controller
{
    public function index(Request $request)
    {
        $status = $request->query('status');
        $kata = trim((string) $request->query('q'));

        $daftar = Peminjaman::with(['mahasiswa', 'ruangan'])
            ->status($status)
            ->when($request->query('tanggal'), fn ($q, $t) => $q->whereDate('tanggal_mulai', $t))
            ->when($kata !== '', function ($q) use ($kata) {
                $like = '%'.addcslashes($kata, '%_\\').'%';
                $q->where(function ($w) use ($kata, $like) {
                    $w->where('nama_pengaju', 'like', $like)
                        ->orWhereHas('ruangan', fn ($r) => $r->where('nama_ruangan', 'like', $like));
                    // Nomor pengajuan, mis. PR-2026-0007 atau 7
                    if (preg_match('/^(?:PR-\d{4}-)?#?0*(\d+)$/i', $kata, $m)) {
                        $w->orWhere('id_peminjaman', (int) $m[1]);
                    }
                });
            })
            ->latest('created_at')->paginate(10)->withQueryString();

        $hitung = Peminjaman::selectRaw('status, count(*) as total')->groupBy('status')->pluck('total', 'status');

        return view('admin.pengajuan.index', [
            'daftar' => $daftar,
            'hitung' => $hitung,
            'semua' => $hitung->sum(),
            'disetujuiHariIni' => Peminjaman::where('status', 'disetujui')->whereDate('updated_at', today())->count(),
        ]);
    }

    public function show(Peminjaman $peminjaman, JadwalRuanganService $jadwal)
    {
        $peminjaman->load(['mahasiswa', 'ruangan']);

        return view('admin.pengajuan.show', [
            'p' => $peminjaman,
            'bentrok' => $peminjaman->status === Peminjaman::MENUNGGU
                && $jadwal->bentrok($peminjaman->id_ruangan, $peminjaman->tanggal_mulai, $peminjaman->tanggal_selesai),
            'jadwalHari' => JadwalRuangan::where('id_ruangan', $peminjaman->id_ruangan)
                ->where('status', 'terpakai')
                ->where('tanggal', $peminjaman->tanggal_mulai->toDateString())
                ->where('id_peminjaman', '!=', $peminjaman->id_peminjaman)
                ->orderBy('jam_mulai')->get(),
        ]);
    }

    public function setujui(KeputusanPengajuanRequest $request, Peminjaman $peminjaman, ApprovalService $service)
    {
        Gate::authorize('approve', $peminjaman);

        try {
            $service->setujui($peminjaman, $request->catatan_admin);
        } catch (DomainException $e) {
            return back()->with('error', $e->getMessage());
        }

        return redirect()->route('admin.pengajuan.show', $peminjaman)->with('success', 'Pengajuan berhasil disetujui.');
    }

    public function tolak(KeputusanPengajuanRequest $request, Peminjaman $peminjaman, ApprovalService $service)
    {
        Gate::authorize('reject', $peminjaman);

        try {
            $service->tolak($peminjaman, $request->catatan_admin);
        } catch (DomainException $e) {
            return back()->with('error', $e->getMessage());
        }

        return redirect()->route('admin.pengajuan.show', $peminjaman)->with('success', 'Pengajuan ditolak.');
    }

    public function selesai(Peminjaman $peminjaman, ApprovalService $service)
    {
        Gate::authorize('complete', $peminjaman);

        try {
            $service->selesaikan($peminjaman);
        } catch (DomainException $e) {
            return back()->with('error', $e->getMessage());
        }

        return back()->with('success', 'Peminjaman ditandai selesai.');
    }

    public function dokumen(Peminjaman $peminjaman)
    {
        Gate::authorize('viewDokumen', $peminjaman);
        abort_unless($peminjaman->punya_dokumen && Storage::disk(PeminjamanService::DISK)->exists($peminjaman->dokumen_pendukung), 404);

        return Storage::disk(PeminjamanService::DISK)->response($peminjaman->dokumen_pendukung);
    }
}
