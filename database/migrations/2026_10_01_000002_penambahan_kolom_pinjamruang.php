<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * PENAMBAHAN NON-DESTRUKTIF (hanya ADD COLUMN nullable + index). Tidak ada kolom yang
 * diubah/dihapus dan tidak ada data yang hilang.
 *
 *  admin.remember_token, mahasiswa.remember_token : checkbox "Ingat sesi saya" (Figma)
 *  ruangan.lokasi                                 : "Gedung & Lokasi" pada form ruangan (Figma)
 *  feedback.id_ruangan                            : dropdown "Ruangan" pada form feedback (Figma)
 *  feedback.dibaca_at / balasan_admin / dibalas_at: status "Baru/Sudah Dibaca" & balasan admin (Figma)
 *  jadwal_ruangan index (id_ruangan, tanggal)     : mempercepat cek bentrok jadwal
 */
return new class extends Migration
{
    public function up(): void
    {
        foreach (['admin', 'mahasiswa'] as $tabel) {
            if (! Schema::hasColumn($tabel, 'remember_token')) {
                Schema::table($tabel, fn (Blueprint $t) => $t->string('remember_token', 100)->nullable());
            }
        }

        if (! Schema::hasColumn('ruangan', 'lokasi')) {
            Schema::table('ruangan', fn (Blueprint $t) => $t->string('lokasi', 150)->nullable());
        }

        Schema::table('feedback', function (Blueprint $t) {
            if (! Schema::hasColumn('feedback', 'id_ruangan')) {
                $t->unsignedInteger('id_ruangan')->nullable()->index();
            }
            if (! Schema::hasColumn('feedback', 'dibaca_at')) {
                $t->dateTime('dibaca_at')->nullable();
            }
            if (! Schema::hasColumn('feedback', 'balasan_admin')) {
                $t->text('balasan_admin')->nullable();
            }
            if (! Schema::hasColumn('feedback', 'dibalas_at')) {
                $t->dateTime('dibalas_at')->nullable();
            }
        });

        if (DB::getDriverName() !== 'sqlite') {
            Schema::table('feedback', fn (Blueprint $t) => $t->foreign('id_ruangan')->references('id_ruangan')->on('ruangan'));
        }

        Schema::table('jadwal_ruangan', fn (Blueprint $t) => $t->index(['id_ruangan', 'tanggal'], 'idx_jadwal_ruangan_tanggal'));
    }

    public function down(): void
    {
        Schema::table('jadwal_ruangan', fn (Blueprint $t) => $t->dropIndex('idx_jadwal_ruangan_tanggal'));

        if (DB::getDriverName() !== 'sqlite') {
            Schema::table('feedback', fn (Blueprint $t) => $t->dropForeign(['id_ruangan']));
        }
        Schema::table('feedback', fn (Blueprint $t) => $t->dropColumn(['id_ruangan', 'dibaca_at', 'balasan_admin', 'dibalas_at']));
        Schema::table('ruangan', fn (Blueprint $t) => $t->dropColumn('lokasi'));
        Schema::table('mahasiswa', fn (Blueprint $t) => $t->dropColumn('remember_token'));
        Schema::table('admin', fn (Blueprint $t) => $t->dropColumn('remember_token'));
    }
};
