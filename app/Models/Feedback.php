<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Feedback extends Model
{
    // Tabel feedback hanya punya created_at.
    const UPDATED_AT = null;

    protected $table = 'feedback';
    protected $primaryKey = 'id_feedback';

    protected $fillable = ['id_mahasiswa', 'id_ruangan', 'isi_feedback', 'dibaca_at', 'balasan_admin', 'dibalas_at'];

    protected function casts(): array
    {
        return ['dibaca_at' => 'datetime', 'dibalas_at' => 'datetime'];
    }

    public function mahasiswa(): BelongsTo
    {
        return $this->belongsTo(Mahasiswa::class, 'id_mahasiswa', 'id_mahasiswa');
    }

    public function ruangan(): BelongsTo
    {
        return $this->belongsTo(Ruangan::class, 'id_ruangan', 'id_ruangan');
    }
}
