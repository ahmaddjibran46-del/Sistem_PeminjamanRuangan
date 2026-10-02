<?php

namespace App\Http\Controllers;

use App\Models\JadwalRuangan;
use App\Models\Mahasiswa;
use App\Models\Peminjaman;
use App\Models\Ruangan;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PeminjamanController extends Controller
{
    public function store(Request $request)
    {
        // ===== 1. Validasi bentuk data (sama dengan PHP native) =====
        $jamRegex = '/^(?:[01]\d|2[0-3]):[0-5]\d$/';

        $validator = Validator::make($request->all(), [
            'id_mahasiswa'   => ['bail', 'required', 'integer', 'min:1'],
            'id_ruangan'     => ['bail', 'required', 'integer', 'min:1'],
            'tanggal'        => ['bail', 'required', 'date_format:Y-m-d', 'not_in:0000-00-00', 'after_or_equal:today'],
            'jam_mulai'      => ['bail', 'required', 'regex:' . $jamRegex],
            'jam_selesai'    => ['bail', 'required', 'regex:' . $jamRegex, 'after:jam_mulai'],
            'alasan'         => ['required'],
            'jumlah_peserta' => ['bail', 'required', 'integer', 'min:1'],
        ], [
            'required'         => 'Data peminjaman belum lengkap.',
            'integer'          => 'Data peminjaman belum lengkap.',
            'min'              => 'Data peminjaman belum lengkap.',
            'date_format'      => 'Tanggal atau jam tidak valid.',
            'not_in'           => 'Tanggal atau jam tidak valid.',
            'regex'            => 'Tanggal atau jam tidak valid.',
            'after'            => 'Jam selesai harus lebih besar dari jam mulai.',
            'after_or_equal'   => 'Tanggal peminjaman tidak boleh sudah lewat.',
        ]);

        if ($validator->fails()) {
            // Tampilkan pesan error pertama, seperti PHP native
            return $this->kembali($validator->errors()->first());
        }

        $data           = $validator->validated();
        $idMahasiswa    = (int) $data['id_mahasiswa'];
        $idRuangan      = (int) $data['id_ruangan'];
        $tanggal        = $data['tanggal'];
        $jamMulai       = $data['jam_mulai'];
        $jamSelesai     = $data['jam_selesai'];
        $alasan         = $data['alasan'];
        $jumlahPeserta  = (int) $data['jumlah_peserta'];

        // ===== 2. Mahasiswa harus aktif, ruangan harus tersedia =====
        $mahasiswa = Mahasiswa::aktif()->find($idMahasiswa);
        $ruangan   = Ruangan::tersedia()->find($idRuangan);

        if (!$mahasiswa || !$ruangan) {
            return $this->kembali('Mahasiswa atau ruangan tidak valid.');
        }

        // ===== 3. Cek kapasitas =====
        if ($jumlahPeserta > (int) $ruangan->kapasitas) {
            return $this->kembali('Jumlah peserta melebihi kapasitas ruangan.');
        }

        // ===== 4. Cek bentrok dengan jadwal resmi (status terpakai) =====
        if (JadwalRuangan::bentrok($idRuangan, $tanggal, $jamMulai, $jamSelesai)->exists()) {
            return $this->kembali('Waktu tersebut sudah terpakai.');
        }

        // ===== 5. Simpan pengajuan. Status "menunggu", BELUM membuat jadwal =====
        try {
            Peminjaman::create([
                'id_mahasiswa'      => $idMahasiswa,
                'id_ruangan'        => $idRuangan,
                'nama_pengaju'      => $mahasiswa->nama,
                'tanggal_mulai'     => "$tanggal $jamMulai:00",
                'tanggal_selesai'   => "$tanggal $jamSelesai:00",
                'alasan'            => $alasan,
                'jumlah_peserta'    => $jumlahPeserta,
                'dokumen_pendukung' => '',
                'no_telfon'         => $mahasiswa->no_telfon,
                'status'            => 'menunggu',
                'catatan_admin'     => '',
            ]);
        } catch (QueryException $e) {
            // Error tidak disembunyikan, sama seperti mysqli_error() di PHP native
            return $this->kembali('Gagal menyimpan pengajuan: ' . $e->getMessage());
        }

        return $this->kembali('Berhasil mengajukan peminjaman. Menunggu persetujuan admin.');
    }

    // Redirect ke halaman jadwal dengan flash message (pengganti header("Location: ...?pesan="))
    private function kembali(string $pesan)
    {
        return redirect()->route('jadwal')->with('pesan', $pesan);
    }
}