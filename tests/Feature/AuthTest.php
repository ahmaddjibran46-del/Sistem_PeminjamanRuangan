<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\MembuatData;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use MembuatData, RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutVite();
    }

    public function test_mahasiswa_dapat_login_dengan_nim(): void
    {
        $m = $this->buatMahasiswa(['nim' => '246250071']);

        $this->post('/login', ['username' => '246250071', 'password' => 'rahasia123'])
            ->assertRedirect(route('ruangan.index'));
        $this->assertAuthenticatedAs($m, 'mahasiswa');
    }

    public function test_login_gagal_dengan_password_salah(): void
    {
        $this->buatMahasiswa(['nim' => '246250071']);

        $this->post('/login', ['username' => '246250071', 'password' => 'salah'])->assertSessionHasErrors('username');
        $this->assertGuest('mahasiswa');
    }

    public function test_akun_nonaktif_tidak_dapat_login(): void
    {
        $this->buatMahasiswa(['nim' => '246250072', 'status_akun' => 'nonaktif']);

        $this->post('/login', ['username' => '246250072', 'password' => 'rahasia123'])->assertSessionHasErrors('username');
        $this->assertGuest('mahasiswa');
    }

    public function test_admin_dapat_login(): void
    {
        $a = $this->buatAdmin(['no_telfon' => '081200000001']);

        $this->post('/admin/login', ['username' => '081200000001', 'password' => 'admin12345'])
            ->assertRedirect(route('admin.dashboard'));
        $this->assertAuthenticatedAs($a, 'admin');
    }

    public function test_tamu_diarahkan_ke_login(): void
    {
        $this->get('/ruangan')->assertRedirect(route('login'));
        $this->get('/admin/dashboard')->assertRedirect(route('admin.login'));
    }

    public function test_mahasiswa_tidak_dapat_mengakses_halaman_admin(): void
    {
        $this->actingAs($this->buatMahasiswa(), 'mahasiswa');

        $this->get('/admin/dashboard')->assertRedirect(route('admin.login'));
        $this->get('/admin/pengajuan')->assertRedirect(route('admin.login'));
    }

    public function test_admin_tidak_dapat_mengakses_halaman_mahasiswa(): void
    {
        $this->actingAs($this->buatAdmin(), 'admin');

        $this->get('/ruangan')->assertRedirect(route('login'));
    }
}
