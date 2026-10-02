<?php

namespace Tests\Feature;

use App\Models\JadwalRuangan;
use App\Models\Peminjaman;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\Concerns\MembuatData;
use Tests\TestCase;

class PeminjamanTest extends TestCase
{
    use MembuatData, RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutVite();
        Storage::fake('local');
    }

    public function test_mahasiswa_dapat_mengajukan_peminjaman(): void
    {
        $m = $this->buatMahasiswa();
        $r = $this->buatRuangan();

        $this->actingAs($m, 'mahasiswa')->post('/peminjaman', $this->dataPengajuan($r))->assertSessionHasNoErrors();

        $p = Peminjaman::firstOrFail();
        $this->assertSame('menunggu', $p->status);
        $this->assertSame('', $p->catatan_admin);
        $this->assertSame($m->id_mahasiswa, $p->id_mahasiswa);
        Storage::disk('local')->assertExists($p->dokumen_pendukung);
    }

    public function test_id_mahasiswa_tidak_bisa_dipalsukan_dari_form(): void
    {
        $m = $this->buatMahasiswa();
        $lain = $this->buatMahasiswa();
        $r = $this->buatRuangan();

        $this->actingAs($m, 'mahasiswa')->post('/peminjaman', $this->dataPengajuan($r, ['id_mahasiswa' => $lain->id_mahasiswa]));

        $this->assertSame($m->id_mahasiswa, Peminjaman::firstOrFail()->id_mahasiswa);
    }

    public function test_peserta_melebihi_kapasitas_ditolak(): void
    {
        $r = $this->buatRuangan(['kapasitas' => 30]);

        $this->actingAs($this->buatMahasiswa(), 'mahasiswa')
            ->post('/peminjaman', $this->dataPengajuan($r, ['jumlah_peserta' => 31]))
            ->assertSessionHasErrors('jumlah_peserta');
        $this->assertDatabaseCount('peminjaman', 0);
    }

    public function test_validasi_tanggal_dan_peserta(): void
    {
        $r = $this->buatRuangan();
        $this->actingAs($this->buatMahasiswa(), 'mahasiswa');

        $this->post('/peminjaman', $this->dataPengajuan($r, ['tanggal_selesai' => $this->waktu(9)->format('Y-m-d\TH:i')]))
            ->assertSessionHasErrors('tanggal_selesai');
        $this->post('/peminjaman', $this->dataPengajuan($r, ['jumlah_peserta' => 0]))->assertSessionHasErrors('jumlah_peserta');
        $this->post('/peminjaman', $this->dataPengajuan($r, ['no_telfon' => '12345']))->assertSessionHasErrors('no_telfon');
        $this->post('/peminjaman', $this->dataPengajuan($r, ['tanggal_mulai' => now()->subDay()->format('Y-m-d\TH:i')]))
            ->assertSessionHasErrors('tanggal_mulai');
    }

    public function test_ruangan_tidak_tersedia_tidak_dapat_dipinjam(): void
    {
        $r = $this->buatRuangan(['status' => 'tidak_tersedia']);

        $this->actingAs($this->buatMahasiswa(), 'mahasiswa')->post('/peminjaman', $this->dataPengajuan($r))
            ->assertSessionHasErrors('id_ruangan');
    }

    public function test_jadwal_bentrok_ditolak_tetapi_slot_bersambung_diizinkan(): void
    {
        $m = $this->buatMahasiswa();
        $r = $this->buatRuangan();
        $p = $this->buatPeminjaman($m, $r, $this->waktu(10), $this->waktu(12), 'disetujui');
        JadwalRuangan::create(['id_ruangan' => $r->id_ruangan, 'id_peminjaman' => $p->id_peminjaman,
            'tanggal' => $this->waktu(10)->toDateString(), 'jam_mulai' => '10:00:00', 'jam_selesai' => '12:00:00', 'status' => 'terpakai']);

        $this->actingAs($m, 'mahasiswa');

        // 11:00 - 13:00 => bentrok
        $this->post('/peminjaman', $this->dataPengajuan($r, [
            'tanggal_mulai' => $this->waktu(11)->format('Y-m-d\TH:i'), 'tanggal_selesai' => $this->waktu(13)->format('Y-m-d\TH:i'),
        ]))->assertSessionHasErrors('tanggal_mulai');
        $this->assertDatabaseCount('peminjaman', 1);

        // 12:00 - 14:00 => bersambung, tidak bentrok
        $this->post('/peminjaman', $this->dataPengajuan($r, [
            'tanggal_mulai' => $this->waktu(12)->format('Y-m-d\TH:i'), 'tanggal_selesai' => $this->waktu(14)->format('Y-m-d\TH:i'),
        ]))->assertSessionHasNoErrors();
        $this->assertDatabaseCount('peminjaman', 2);
    }

    public function test_mahasiswa_tidak_dapat_melihat_peminjaman_mahasiswa_lain(): void
    {
        $a = $this->buatMahasiswa();
        $b = $this->buatMahasiswa();
        $p = $this->buatPeminjaman($b, $this->buatRuangan(), $this->waktu(10), $this->waktu(12));

        $this->actingAs($a, 'mahasiswa')->get("/peminjaman/{$p->id_peminjaman}")->assertForbidden();
        $this->actingAs($b, 'mahasiswa')->get("/peminjaman/{$p->id_peminjaman}")->assertOk();
    }

    public function test_dokumen_hanya_dapat_diakses_pemilik(): void
    {
        $a = $this->buatMahasiswa();
        $b = $this->buatMahasiswa();
        $p = $this->buatPeminjaman($b, $this->buatRuangan(), $this->waktu(10), $this->waktu(12));
        Storage::disk('local')->put('dokumen-peminjaman/x.pdf', 'isi');
        $p->update(['dokumen_pendukung' => 'dokumen-peminjaman/x.pdf']);

        $this->actingAs($a, 'mahasiswa')->get("/peminjaman/{$p->id_peminjaman}/dokumen")->assertForbidden();
        $this->actingAs($b, 'mahasiswa')->get("/peminjaman/{$p->id_peminjaman}/dokumen")->assertOk();
        $this->actingAs($this->buatAdmin(), 'admin')->get("/admin/pengajuan/{$p->id_peminjaman}/dokumen")->assertOk();
    }
}
