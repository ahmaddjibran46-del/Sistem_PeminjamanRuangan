<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Peminjaman extends Model
{
    const MENUNGGU = 'menunggu';
    const DISETUJUI = 'disetujui';
    const DITOLAK = 'ditolak';
    const SELESAI = 'selesai';

    protected $table = 'peminjaman';
    protected $primaryKey = 'id_peminjaman';

    protected $fillable = [
        'id_mahasiswa', 'id_ruangan', 'nama_pengaju', 'tanggal_mulai', 'tanggal_selesai',
        'alasan', 'jumlah_peserta', 'dokumen_pendukung', 'no_telfon', 'status', 'catatan_admin',
    ];

    // Kolom NOT NULL tanpa default di database.
    protected $attributes = ['status' => self::MENUNGGU, 'catatan_admin' => '', 'dokumen_pendukung' => ''];

    protected function casts(): array
    {
        return [
            'tanggal_mulai' => 'datetime',
            'tanggal_selesai' => 'datetime',
            'jumlah_peserta' => 'integer',
        ];
    }

    public function mahasiswa(): BelongsTo
    {
        return $this->belongsTo(Mahasiswa::class, 'id_mahasiswa', 'id_mahasiswa');
    }

    public function ruangan(): BelongsTo
    {
        return $this->belongsTo(Ruangan::class, 'id_ruangan', 'id_ruangan');
    }

    public function jadwal(): HasMany
    {
        return $this->hasMany(JadwalRuangan::class, 'id_peminjaman', 'id_peminjaman');
    }

    public function scopeStatus(Builder $q, ?string $status): Builder
    {
        return $status ? $q->where('status', $status) : $q;
    }

    public function getNomorAttribute(): string
    {
        return 'PR-'.($this->created_at?->format('Y') ?? date('Y')).'-'.str_pad((string) $this->id_peminjaman, 4, '0', STR_PAD_LEFT);
    }

    public function getStatusLabelAttribute(): string
    {
        return ucfirst($this->status);
    }

    public function getPunyaDokumenAttribute(): bool
    {
        return $this->dokumen_pendukung !== '';
    }
}
