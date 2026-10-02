<?php

namespace App\Policies;

use App\Models\Admin;
use App\Models\Feedback;
use App\Models\Mahasiswa;
use Illuminate\Contracts\Auth\Authenticatable;

class FeedbackPolicy
{
    public function view(Authenticatable $user, Feedback $f): bool
    {
        return $user instanceof Admin
            || ($user instanceof Mahasiswa && $f->id_mahasiswa === $user->id_mahasiswa);
    }

    public function reply(Authenticatable $user, Feedback $f): bool
    {
        return $user instanceof Admin;
    }
}
