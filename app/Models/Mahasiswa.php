<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Mahasiswa extends Authenticatable
{
    // Tabel mahasiswa tidak punya updated_at.
    const UPDATED_AT = null;

    protected $table = 'mahasiswa';
    protected $primaryKey = 'id_mahasiswa';

    protected $fillable = ['nim', 'nama', 'no_telfon', 'password', 'metode_login', 'status_akun'];
    protected $hidden = ['password', 'remember_token'];

    protected function casts(): array
    {
        return ['password' => 'hashed'];
    }

    public function peminjaman(): HasMany
    {
        return $this->hasMany(Peminjaman::class, 'id_mahasiswa', 'id_mahasiswa');
    }

    public function feedback(): HasMany
    {
        return $this->hasMany(Feedback::class, 'id_mahasiswa', 'id_mahasiswa');
    }

    public function getInisialAttribute(): string
    {
        return collect(explode(' ', trim($this->nama)))->take(2)
            ->map(fn ($k) => mb_strtoupper(mb_substr($k, 0, 1)))->implode('');
    }
}
