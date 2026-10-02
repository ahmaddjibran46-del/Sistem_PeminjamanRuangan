<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * BASELINE: mencerminkan schema database `peminjaman_ruang` yang sudah ada.
 * Setiap tabel dibuat HANYA jika belum ada, sehingga database existing tidak disentuh
 * (aman, tidak ada kehilangan data). Berguna untuk instalasi baru dan testing.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('admin')) {
            Schema::create('admin', function (Blueprint $t) {
                $t->increments('id_admin');
                $t->string('nama', 100);
                $t->string('no_telfon', 13);
                $t->string('password', 255);
                $t->enum('role', ['admin']);
                $t->enum('status_akun', ['aktif', 'nonaktif']);
                $t->dateTime('created_at');
                $t->dateTime('updated_at');
            });
        }

        if (! Schema::hasTable('mahasiswa')) {
            Schema::create('mahasiswa', function (Blueprint $t) {
                $t->increments('id_mahasiswa');
                $t->string('nim', 9);
                $t->string('nama', 100);
                $t->string('no_telfon', 13);
                $t->string('password', 255);
                $t->enum('metode_login', ['daftar', 'siakad', 'scan_ktm']);
                $t->enum('status_akun', ['aktif', 'nonaktif']);
                $t->dateTime('created_at');
            });
        }

        if (! Schema::hasTable('ruangan')) {
            Schema::create('ruangan', function (Blueprint $t) {
                $t->increments('id_ruangan');
                $t->string('nama_ruangan', 100);
                $t->text('deskripsi');
                $t->integer('kapasitas');
                $t->text('fasilitas');
                $t->string('foto', 255);
                $t->enum('status', ['tersedia', 'tidak_tersedia']);
                $t->integer('created_at'); // INT di database existing (dipertahankan)
                $t->dateTime('updated_at');
            });
        }

        if (! Schema::hasTable('peminjaman')) {
            Schema::create('peminjaman', function (Blueprint $t) {
                $t->increments('id_peminjaman');
                $t->unsignedInteger('id_mahasiswa')->index();
                $t->unsignedInteger('id_ruangan')->index();
                $t->string('nama_pengaju', 100);
                $t->dateTime('tanggal_mulai');
                $t->dateTime('tanggal_selesai');
                $t->text('alasan');
                $t->integer('jumlah_peserta');
                $t->string('dokumen_pendukung', 255);
                $t->string('no_telfon', 13);
                $t->enum('status', ['menunggu', 'disetujui', 'ditolak', 'selesai']);
                $t->text('catatan_admin');
                $t->dateTime('created_at');
                $t->dateTime('updated_at');
                $t->foreign('id_mahasiswa')->references('id_mahasiswa')->on('mahasiswa');
                $t->foreign('id_ruangan')->references('id_ruangan')->on('ruangan');
            });
        }

        if (! Schema::hasTable('jadwal_ruangan')) {
            Schema::create('jadwal_ruangan', function (Blueprint $t) {
                $t->increments('id_jadwal');
                $t->unsignedInteger('id_ruangan')->index();
                $t->unsignedInteger('id_peminjaman')->index();
                $t->date('tanggal');
                $t->time('jam_mulai');
                $t->time('jam_selesai');
                $t->enum('status', ['terpakai', 'selesai']);
                $t->foreign('id_ruangan')->references('id_ruangan')->on('ruangan');
                $t->foreign('id_peminjaman')->references('id_peminjaman')->on('peminjaman');
            });
        }

        if (! Schema::hasTable('feedback')) {
            Schema::create('feedback', function (Blueprint $t) {
                $t->increments('id_feedback');
                $t->unsignedInteger('id_mahasiswa')->index();
                $t->text('isi_feedback');
                $t->dateTime('created_at');
                $t->foreign('id_mahasiswa')->references('id_mahasiswa')->on('mahasiswa');
            });
        }
    }

    public function down(): void
    {
        // Sengaja kosong: baseline tidak boleh menghapus tabel existing.
    }
};
