<?php

namespace Tests\Feature;

use App\Models\Feedback;
use App\Models\Ruangan;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\Concerns\MembuatData;
use Tests\TestCase;

class RuanganFeedbackTest extends TestCase
{
    use MembuatData, RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutVite();
    }

    public function test_admin_dapat_menambah_ruangan(): void
    {
        Storage::fake('public');

        $this->actingAs($this->buatAdmin(), 'admin')->post('/admin/ruangan', [
            'nama_ruangan' => 'Lab Baru', 'deskripsi' => 'Lab komputer', 'kapasitas' => 40,
            'fasilitas' => 'AC, LAN', 'status' => 'tersedia', 'foto' => UploadedFile::fake()->image('lab.jpg'),
        ])->assertRedirect(route('admin.ruangan.index'));

        $r = Ruangan::firstOrFail();
        $this->assertSame('Lab Baru', $r->nama_ruangan);
        $this->assertGreaterThan(0, $r->created_at); // kolom INT existing diisi time()
        Storage::disk('public')->assertExists($r->foto);
    }

    public function test_validasi_ruangan(): void
    {
        $this->actingAs($this->buatAdmin(), 'admin')
            ->post('/admin/ruangan', ['nama_ruangan' => '', 'deskripsi' => '', 'kapasitas' => 0, 'status' => 'rusak'])
            ->assertSessionHasErrors(['nama_ruangan', 'deskripsi', 'kapasitas', 'status']);
    }

    public function test_admin_mengubah_status_ruangan_tanpa_menghapus(): void
    {
        $r = $this->buatRuangan();

        $this->actingAs($this->buatAdmin(), 'admin')->patch("/admin/ruangan/{$r->id_ruangan}/status");

        $this->assertSame('tidak_tersedia', $r->fresh()->status);
        $this->assertDatabaseCount('ruangan', 1);
    }

    public function test_mahasiswa_tidak_dapat_mengelola_ruangan(): void
    {
        $r = $this->buatRuangan();

        $this->actingAs($this->buatMahasiswa(), 'mahasiswa')
            ->post('/admin/ruangan', ['nama_ruangan' => 'X'])->assertRedirect(route('admin.login'));
        $this->assertDatabaseCount('ruangan', 1);
    }

    public function test_mahasiswa_dapat_melihat_daftar_dan_detail_ruangan(): void
    {
        $r = $this->buatRuangan(['nama_ruangan' => 'Aula Besar']);

        $this->actingAs($this->buatMahasiswa(), 'mahasiswa')->get('/ruangan')->assertOk()->assertSee('Aula Besar');
        $this->get("/ruangan/{$r->id_ruangan}")->assertOk()->assertSee('Ajukan Peminjaman');
    }

    public function test_mahasiswa_mengirim_feedback(): void
    {
        $m = $this->buatMahasiswa();
        $r = $this->buatRuangan();

        $this->actingAs($m, 'mahasiswa')->post('/feedback', ['id_ruangan' => $r->id_ruangan, 'isi_feedback' => 'AC kurang dingin di sayap timur.'])
            ->assertRedirect(route('feedback.index'));

        $this->assertDatabaseHas('feedback', ['id_mahasiswa' => $m->id_mahasiswa, 'id_ruangan' => $r->id_ruangan]);
    }

    public function test_feedback_dibatasi_500_karakter(): void
    {
        $this->actingAs($this->buatMahasiswa(), 'mahasiswa')->post('/feedback', ['isi_feedback' => str_repeat('a', 501)])
            ->assertSessionHasErrors('isi_feedback');
    }

    public function test_admin_membaca_dan_membalas_feedback(): void
    {
        $f = Feedback::create(['id_mahasiswa' => $this->buatMahasiswa()->id_mahasiswa, 'isi_feedback' => 'Mic wireless berbunyi kresek-kresek.']);
        $admin = $this->buatAdmin();

        $this->actingAs($admin, 'admin')->get('/admin/feedback')->assertOk()->assertSee('Mic wireless');
        $this->assertNotNull($f->fresh()->dibaca_at);

        $this->post("/admin/feedback/{$f->id_feedback}/balas", ['balasan_admin' => 'Terima kasih, akan kami cek.']);
        $this->assertSame('Terima kasih, akan kami cek.', $f->fresh()->balasan_admin);
    }

    public function test_mahasiswa_tidak_dapat_membuka_feedback_admin(): void
    {
        $this->actingAs($this->buatMahasiswa(), 'mahasiswa')->get('/admin/feedback')->assertRedirect(route('admin.login'));
    }

    public function test_dashboard_admin_memuat_data(): void
    {
        $this->actingAs($this->buatAdmin(), 'admin')->get('/admin/dashboard')->assertOk()->assertSee('Dashboard Utama');
    }
}
