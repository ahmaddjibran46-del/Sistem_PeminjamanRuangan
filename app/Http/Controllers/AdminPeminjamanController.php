<?php

namespace App\Http\Controllers;

use App\Models\JadwalRuangan;
use App\Models\Peminjaman;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminPeminjamanController extends Controller
{
    // Daftar semua pengajuan, terbaru di atas (pengganti admin_peminjaman.php)
    public function index()
    {
        $pengajuan = Peminjaman::with(['mahasiswa', 'ruangan'])
            ->orderByDesc('created_at')
            ->get();

        return view('admin.peminjaman', compact('pengajuan'));
    }

    // Setujui pengajuan
    public function approve($id)
    {
        $pesan = DB::transaction(function () use ($id) {
            // lockForUpdate: kunci baris ini supaya tidak diproses dua kali bersamaan
            $p = Peminjaman::lockForUpdate()->find($id);

            if (!$p) {
                return 'Pengajuan tidak ditemukan.';
            }
            if ($p->status !== 'menunggu') {
                return 'Pengajuan ini sudah diproses.';
            }

            // Pecah "2026-10-05 09:00:00" menjadi tanggal dan jam
            $tanggal    = substr($p->tanggal_mulai, 0, 10);
            $jamMulai   = substr($p->tanggal_mulai, 11, 8);
            $jamSelesai = substr($p->tanggal_selesai, 11, 8);

            // Cek bentrok ULANG saat approval
            if (JadwalRuangan::bentrok($p->id_ruangan, $tanggal, $jamMulai, $jamSelesai)->exists()) {
                $p->update([
                    'status'        => 'ditolak',
                    'catatan_admin' => 'Ditolak otomatis: waktu tersebut sudah terpakai oleh jadwal lain.',
                ]);
                return 'Pengajuan ditolak otomatis karena waktu sudah terpakai.';
            }

            // Tidak bentrok: setujui dan buat jadwal resmi
            $p->update(['status' => 'disetujui']);

            JadwalRuangan::create([
                'id_ruangan'    => $p->id_ruangan,
                'id_peminjaman' => $p->id_peminjaman,
                'tanggal'       => $tanggal,
                'jam_mulai'     => $jamMulai,
                'jam_selesai'   => $jamSelesai,
                'status'        => 'terpakai',
            ]);

            return 'Berhasil menyetujui pengajuan. Jadwal ruangan dibuat.';
        });

        return redirect()->route('admin.peminjaman')->with('pesan', $pesan);
    }

    // Tolak pengajuan (catatan_admin boleh kosong)
    public function reject(Request $request, $id)
    {
        $request->validate(['catatan_admin' => ['nullable', 'string', 'max:255']]);

        $p = Peminjaman::find($id);

        if (!$p) {
            $pesan = 'Pengajuan tidak ditemukan.';
        } elseif ($p->status !== 'menunggu') {
            $pesan = 'Pengajuan ini sudah diproses.';
        } else {
            $p->update([
                'status'        => 'ditolak',
                'catatan_admin' => $request->input('catatan_admin') ?? '',
            ]);
            $pesan = 'Berhasil menolak pengajuan.';
        }

        return redirect()->route('admin.peminjaman')->with('pesan', $pesan);
    }
}