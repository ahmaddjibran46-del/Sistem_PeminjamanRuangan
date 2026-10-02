<?php

namespace App\Providers;

use App\Services\Auth\LocalMahasiswaAuthenticator;
use App\Services\Auth\MahasiswaAuthenticator;
use Carbon\Carbon;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // Titik integrasi: ganti binding ini dengan SiakadAuthenticator / KtmAuthenticator
        // bila integrasi eksternal sudah tersedia. Tidak ada integrasi palsu di sini.
        $this->app->bind(MahasiswaAuthenticator::class, LocalMahasiswaAuthenticator::class);
    }

    public function boot(): void
    {
        Carbon::setLocale('id');
        Paginator::defaultView('pagination.pinjamruang');
        Paginator::defaultSimpleView('pagination.pinjamruang');
    }
}
