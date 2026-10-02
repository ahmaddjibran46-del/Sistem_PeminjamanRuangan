<?php

namespace App\Policies;

use App\Models\Admin;
use App\Models\Ruangan;
use Illuminate\Contracts\Auth\Authenticatable;

class RuanganPolicy
{
    public function manage(Authenticatable $user, Ruangan|string|null $ruangan = null): bool
    {
        return $user instanceof Admin;
    }
}
