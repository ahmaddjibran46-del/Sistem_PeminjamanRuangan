<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;

class Admin extends Authenticatable
{
    protected $table = 'admin';
    protected $primaryKey = 'id_admin';

    protected $fillable = ['nama', 'no_telfon', 'password', 'role', 'status_akun'];
    protected $hidden = ['password', 'remember_token'];

    protected function casts(): array
    {
        return ['password' => 'hashed'];
    }

    public function getInisialAttribute(): string
    {
        return collect(explode(' ', trim($this->nama)))->take(2)
            ->map(fn ($k) => mb_strtoupper(mb_substr($k, 0, 1)))->implode('');
    }
}
