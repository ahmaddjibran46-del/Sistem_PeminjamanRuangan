<?php

namespace App\Http\Controllers;

use App\Models\JadwalRuangan;
use App\Models\Ruangan;

class JadwalController extends Controller
{
    public function index()
    {
        /*
         * SEMENTARA: mahasiswa ID 1 dipakai sebagai pengguna aktif.
         * Nanti, setelah ada login, baris ini diganti dengan ID mahasiswa
         * yang sedang login, misalnya dari session.
         */
        $id_mahasiswa = 1;

        // Daftar ruangan yang tersedia, urut nama (untuk pilihan di form)
        $ruangan = Ruangan::tersedia()
            ->select('id_ruangan', 'nama_ruangan', 'kapasitas', 'fasilitas')
            ->orderBy('nama_ruangan')
            ->get();

        // Jadwal resmi yang terpakai, urut tanggal lalu jam mulai
        $jadwal = JadwalRuangan::terpakai()
            ->with('ruangan')
            ->orderBy('tanggal')
            ->orderBy('jam_mulai')
            ->get();

        return view('jadwal', compact('id_mahasiswa', 'ruangan', 'jadwal'));
    }
}