<?php

namespace App\Services;

use App\Models\Mahasiswa;
use App\Models\Peminjaman;
use App\Models\Ruangan;
use Carbon\Carbon;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class PeminjamanService
{
    public function __construct(private JadwalRuanganService $jadwal) {}

    /** Disk privat (storage/app), tidak dapat diakses langsung dari web. */
    public const DISK = 'local';

    public function ajukan(Mahasiswa $mahasiswa, array $data, UploadedFile $dokumen): Peminjaman
    {
        $ruangan = Ruangan::findOrFail($data['id_ruangan']);
        $mulai = Carbon::parse($data['tanggal_mulai']);
        $selesai = Carbon::parse($data['tanggal_selesai']);

        $this->validasiBisnis($ruangan, (int) $data['jumlah_peserta'], $mulai, $selesai);

        $path = $dokumen->store('dokumen-peminjaman', self::DISK);

        try {
            return Peminjaman::create([
                'id_mahasiswa' => $mahasiswa->id_mahasiswa, // selalu dari user login
                'id_ruangan' => $ruangan->id_ruangan,
                'nama_pengaju' => $data['nama_pengaju'],
                'tanggal_mulai' => $mulai,
                'tanggal_selesai' => $selesai,
                'alasan' => $data['alasan'],
                'jumlah_peserta' => $data['jumlah_peserta'],
                'dokumen_pendukung' => $path,
                'no_telfon' => $data['no_telfon'],
                'status' => Peminjaman::MENUNGGU,
                'catatan_admin' => '',
            ]);
        } catch (\Throwable $e) {
            Storage::disk(self::DISK)->delete($path);
            Log::error('Gagal menyimpan pengajuan', ['mahasiswa' => $mahasiswa->id_mahasiswa, 'error' => $e->getMessage()]);
            throw $e;
        }
    }

    /** @throws ValidationException */
    public function validasiBisnis(Ruangan $ruangan, int $peserta, Carbon $mulai, Carbon $selesai): void
    {
        $error = [];

        if (! $ruangan->is_tersedia) {
            $error['id_ruangan'] = 'Ruangan ini sedang tidak tersedia untuk dipinjam.';
        }
        if ($peserta > $ruangan->kapasitas) {
            $error['jumlah_peserta'] = "Jumlah peserta melebihi kapasitas ruangan ({$ruangan->kapasitas} orang).";
        }
        if (! $error && $this->jadwal->bentrok($ruangan->id_ruangan, $mulai, $selesai)) {
            $error['tanggal_mulai'] = 'Maaf, pengajuan tidak dapat diproses karena jadwal ruangan telah digunakan.';
        }

        if ($error) {
            throw ValidationException::withMessages($error);
        }
    }
}
