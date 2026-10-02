<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class JadwalRuangan extends Model
{
    const TERPAKAI = 'terpakai';
    const SELESAI = 'selesai';

    public $timestamps = false;

    protected $table = 'jadwal_ruangan';
    protected $primaryKey = 'id_jadwal';

    protected $fillable = ['id_ruangan', 'id_peminjaman', 'tanggal', 'jam_mulai', 'jam_selesai', 'status'];

    protected function casts(): array
    {
        return ['tanggal' => 'date'];
    }

    public function ruangan(): BelongsTo
    {
        return $this->belongsTo(Ruangan::class, 'id_ruangan', 'id_ruangan');
    }

    public function peminjaman(): BelongsTo
    {
        return $this->belongsTo(Peminjaman::class, 'id_peminjaman', 'id_peminjaman');
    }

    public function getJamAttribute(): string
    {
        return substr($this->jam_mulai, 0, 5).' - '.substr($this->jam_selesai, 0, 5);
    }
}
