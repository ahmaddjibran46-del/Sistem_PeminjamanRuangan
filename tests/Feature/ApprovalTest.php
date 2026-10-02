<?php

namespace Tests\Feature;

use App\Models\JadwalRuangan;
use App\Models\Peminjaman;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\MembuatData;
use Tests\TestCase;

class ApprovalTest extends TestCase
{
    use MembuatData, RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutVite();
    }

    public function test_admin_menyetujui_dan_jadwal_terbentuk(): void
    {
        $p = $this->buatPeminjaman($this->buatMahasiswa(), $this->buatRuangan(), $this->waktu(10), $this->waktu(12));

        $this->actingAs($this->buatAdmin(), 'admin')
            ->post("/admin/pengajuan/{$p->id_peminjaman}/setujui", ['catatan_admin' => 'jangan lama-lama'])
            ->assertSessionHas('success');

        $p->refresh();
        $this->assertSame('disetujui', $p->status);
        $this->assertSame('jangan lama-lama', $p->catatan_admin);
        $this->assertDatabaseHas('jadwal_ruangan', [
            'id_peminjaman' => $p->id_peminjaman, 'id_ruangan' => $p->id_ruangan,
            'jam_mulai' => '10:00:00', 'jam_selesai' => '12:00:00', 'status' => 'terpakai',
        ]);
    }

    public function test_peminjaman_lintas_hari_dipecah_per_hari(): void
    {
        $p = $this->buatPeminjaman($this->buatMahasiswa(), $this->buatRuangan(), $this->waktu(20), $this->waktu(8)->addDay());

        $this->actingAs($this->buatAdmin(), 'admin')->post("/admin/pengajuan/{$p->id_peminjaman}/setujui");

        $this->assertSame(2, JadwalRuangan::where('id_peminjaman', $p->id_peminjaman)->count());
    }

    public function test_penolakan_wajib_catatan(): void
    {
        $p = $this->buatPeminjaman($this->buatMahasiswa(), $this->buatRuangan(), $this->waktu(10), $this->waktu(12));
        $admin = $this->buatAdmin();

        $this->actingAs($admin, 'admin')->post("/admin/pengajuan/{$p->id_peminjaman}/tolak", [])
            ->assertSessionHasErrors('catatan_admin');
        $this->assertSame('menunggu', $p->fresh()->status);

        $this->actingAs($admin, 'admin')->post("/admin/pengajuan/{$p->id_peminjaman}/tolak", ['catatan_admin' => 'Bentrok dies natalis']);
        $this->assertSame('ditolak', $p->fresh()->status);
        $this->assertSame('Bentrok dies natalis', $p->fresh()->catatan_admin);
        $this->assertDatabaseCount('jadwal_ruangan', 0);
    }

    public function test_mahasiswa_tidak_dapat_menyetujui(): void
    {
        $m = $this->buatMahasiswa();
        $p = $this->buatPeminjaman($m, $this->buatRuangan(), $this->waktu(10), $this->waktu(12));

        $this->actingAs($m, 'mahasiswa')->post("/admin/pengajuan/{$p->id_peminjaman}/setujui")->assertRedirect(route('admin.login'));
        $this->assertSame('menunggu', $p->fresh()->status);
    }

    public function test_persetujuan_yang_bentrok_dibatalkan(): void
    {
        $m = $this->buatMahasiswa();
        $r = $this->buatRuangan();
        $a = $this->buatPeminjaman($m, $r, $this->waktu(10), $this->waktu(12));
        $b = $this->buatPeminjaman($m, $r, $this->waktu(11), $this->waktu(13));
        $admin = $this->buatAdmin();

        $this->actingAs($admin, 'admin')->post("/admin/pengajuan/{$a->id_peminjaman}/setujui");
        $this->actingAs($admin, 'admin')->post("/admin/pengajuan/{$b->id_peminjaman}/setujui")->assertSessionHas('error');

        $this->assertSame('menunggu', $b->fresh()->status);
        $this->assertSame(1, JadwalRuangan::count());
    }

    public function test_pengajuan_tidak_dapat_diproses_dua_kali(): void
    {
        $p = $this->buatPeminjaman($this->buatMahasiswa(), $this->buatRuangan(), $this->waktu(10), $this->waktu(12));
        $admin = $this->buatAdmin();

        $this->actingAs($admin, 'admin')->post("/admin/pengajuan/{$p->id_peminjaman}/setujui");
        $this->actingAs($admin, 'admin')->post("/admin/pengajuan/{$p->id_peminjaman}/setujui")->assertSessionHas('error');

        $this->assertSame(1, JadwalRuangan::count());
    }

    public function test_peminjaman_diselesaikan_tanpa_menghapus_riwayat(): void
    {
        $p = $this->buatPeminjaman($this->buatMahasiswa(), $this->buatRuangan(), $this->waktu(10), $this->waktu(12));
        $admin = $this->buatAdmin();

        $this->actingAs($admin, 'admin')->post("/admin/pengajuan/{$p->id_peminjaman}/setujui");
        $this->actingAs($admin, 'admin')->post("/admin/pengajuan/{$p->id_peminjaman}/selesai");

        $this->assertSame('selesai', $p->fresh()->status);
        $this->assertDatabaseHas('jadwal_ruangan', ['id_peminjaman' => $p->id_peminjaman, 'status' => 'selesai']);
        $this->assertDatabaseCount('peminjaman', 1);
    }

    public function test_jadwal_selesai_tidak_dianggap_bentrok(): void
    {
        $m = $this->buatMahasiswa();
        $r = $this->buatRuangan();
        $lama = $this->buatPeminjaman($m, $r, $this->waktu(10), $this->waktu(12));
        $admin = $this->buatAdmin();
        $this->actingAs($admin, 'admin')->post("/admin/pengajuan/{$lama->id_peminjaman}/setujui");
        $this->actingAs($admin, 'admin')->post("/admin/pengajuan/{$lama->id_peminjaman}/selesai");

        $baru = $this->buatPeminjaman($m, $r, $this->waktu(10), $this->waktu(12));
        $this->actingAs($admin, 'admin')->post("/admin/pengajuan/{$baru->id_peminjaman}/setujui")->assertSessionHas('success');
    }
}
