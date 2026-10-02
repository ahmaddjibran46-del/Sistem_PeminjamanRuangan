<?php

namespace Tests\Concerns;

use App\Models\Admin;
use App\Models\Mahasiswa;
use App\Models\Peminjaman;
use App\Models\Ruangan;
use Carbon\Carbon;
use Illuminate\Http\UploadedFile;

trait MembuatData
{
    protected function buatMahasiswa(array $o = []): Mahasiswa
    {
        return Mahasiswa::create($o + [
            'nim' => (string) random_int(100000000, 999999999), 'nama' => 'Mahasiswa Uji',
            'no_telfon' => '081234567890', 'password' => 'rahasia123',
            'metode_login' => 'daftar', 'status_akun' => 'aktif',
        ]);
    }

    protected function buatAdmin(array $o = []): Admin
    {
        return Admin::create($o + [
            'nama' => 'Admin Uji', 'no_telfon' => '081200000000', 'password' => 'admin12345',
            'role' => 'admin', 'status_akun' => 'aktif',
        ]);
    }

    protected function buatRuangan(array $o = []): Ruangan
    {
        return Ruangan::create($o + [
            'nama_ruangan' => 'Aula Uji', 'deskripsi' => 'Ruangan uji', 'kapasitas' => 50,
            'fasilitas' => 'AC, Proyektor', 'status' => 'tersedia',
        ]);
    }

    protected function buatPeminjaman(Mahasiswa $m, Ruangan $r, $mulai, $selesai, string $status = 'menunggu'): Peminjaman
    {
        return Peminjaman::create([
            'id_mahasiswa' => $m->id_mahasiswa, 'id_ruangan' => $r->id_ruangan, 'nama_pengaju' => $m->nama,
            'tanggal_mulai' => $mulai, 'tanggal_selesai' => $selesai, 'alasan' => 'Rapat', 'jumlah_peserta' => 10,
            'dokumen_pendukung' => '', 'no_telfon' => $m->no_telfon, 'status' => $status, 'catatan_admin' => '',
        ]);
    }

    /** Jam pada hari H+3 supaya selalu di masa depan. */
    protected function waktu(int $jam, int $menit = 0): Carbon
    {
        return now()->addDays(3)->setTime($jam, $menit, 0);
    }

    protected function dataPengajuan(Ruangan $r, array $o = []): array
    {
        return $o + [
            'nama_pengaju' => 'Mahasiswa Uji', 'id_ruangan' => $r->id_ruangan,
            'tanggal_mulai' => $this->waktu(10)->format('Y-m-d\TH:i'),
            'tanggal_selesai' => $this->waktu(12)->format('Y-m-d\TH:i'),
            'alasan' => 'Seminar jurusan', 'jumlah_peserta' => 20, 'no_telfon' => '081234567890',
            'dokumen_pendukung' => UploadedFile::fake()->create('surat.pdf', 100, 'application/pdf'),
        ];
    }
}
