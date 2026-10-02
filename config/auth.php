<?php

// Dua guard terpisah, memakai tabel existing (admin & mahasiswa).
// Tabel "users" bawaan Laravel TIDAK dipakai.
return [
    'defaults' => [
        'guard' => 'mahasiswa',
        'passwords' => null,
    ],

    'guards' => [
        'mahasiswa' => ['driver' => 'session', 'provider' => 'mahasiswa'],
        'admin' => ['driver' => 'session', 'provider' => 'admin'],
    ],

    'providers' => [
        'mahasiswa' => ['driver' => 'eloquent', 'model' => App\Models\Mahasiswa::class],
        'admin' => ['driver' => 'eloquent', 'model' => App\Models\Admin::class],
    ],

    'passwords' => [],

    'password_timeout' => 10800,
];
