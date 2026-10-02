<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Data utama (ruangan, mahasiswa) berasal dari database existing.
        // Untuk akun admin development: php artisan db:seed --class=DevAdminSeeder
    }
}
