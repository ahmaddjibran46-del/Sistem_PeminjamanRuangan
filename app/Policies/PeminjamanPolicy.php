<?php

namespace App\Policies;

use App\Models\Admin;
use App\Models\Mahasiswa;
use App\Models\Peminjaman;
use Illuminate\Contracts\Auth\Authenticatable;

class PeminjamanPolicy
{
    public function view(Authenticatable $user, Peminjaman $p): bool
    {
        return $user instanceof Admin
            || ($user instanceof Mahasiswa && $p->id_mahasiswa === $user->id_mahasiswa);
    }

    public function viewDokumen(Authenticatable $user, Peminjaman $p): bool
    {
        return $this->view($user, $p);
    }

    public function approve(Authenticatable $user, Peminjaman $p): bool
    {
        return $user instanceof Admin;
    }

    public function reject(Authenticatable $user, Peminjaman $p): bool
    {
        return $user instanceof Admin;
    }

    public function complete(Authenticatable $user, Peminjaman $p): bool
    {
        return $user instanceof Admin;
    }
}
