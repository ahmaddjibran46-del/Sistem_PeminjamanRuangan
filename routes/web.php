<?php

use App\Http\Controllers\Admin;
use App\Http\Controllers\Mahasiswa;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;

Route::get('/', fn() => Auth::guard('mahasiswa')->check()
    ? redirect()->route('ruangan.index')
    : redirect()->route('login'))->name('home');
Route::view('/lupa-password', 'auth.lupa-password')->name('password.request');

/* ------------------------------ MAHASISWA ------------------------------ */
Route::middleware('guard.guest:mahasiswa')->group(function () {
    Route::get('/login', [AuthController::class, 'create'])->name('login');
    Route::post('/login', [AuthController::class, 'store'])->middleware('throttle:6,1');
});

Route::middleware('guard.auth:mahasiswa')->group(function () {
    Route::post('/logout', [AuthController::class, 'destroy'])->name('logout');
    Route::redirect('/dashboard', '/ruangan')->name('dashboard');

    Route::get('/ruangan', [Mahasiswa\RuanganController::class, 'index'])->name('ruangan.index');
    Route::get('/ruangan/{ruangan}', [Mahasiswa\RuanganController::class, 'show'])->name('ruangan.show');

    Route::get('/peminjaman', [Mahasiswa\PeminjamanController::class, 'index'])->name('peminjaman.index');
    Route::get('/peminjaman/create', [Mahasiswa\PeminjamanController::class, 'create'])->name('peminjaman.create');
    Route::post('/peminjaman', [Mahasiswa\PeminjamanController::class, 'store'])->name('peminjaman.store');
    Route::get('/peminjaman/{peminjaman}', [Mahasiswa\PeminjamanController::class, 'show'])->name('peminjaman.show');
    Route::get('/peminjaman/{peminjaman}/dokumen', [Mahasiswa\PeminjamanController::class, 'dokumen'])->name('peminjaman.dokumen');

    Route::get('/feedback', [Mahasiswa\FeedbackController::class, 'index'])->name('feedback.index');
    Route::get('/feedback/create', [Mahasiswa\FeedbackController::class, 'create'])->name('feedback.create');
    Route::post('/feedback', [Mahasiswa\FeedbackController::class, 'store'])->name('feedback.store');
});

/* -------------------------------- ADMIN -------------------------------- */
Route::prefix('admin')->name('admin.')->group(function () {
    Route::redirect('/login', '/login')->name('login');

    Route::middleware('guard.auth:admin')->group(function () {
        Route::post('/logout', [AuthController::class, 'destroy'])->name('logout');
        Route::get('/', fn() => redirect()->route('admin.dashboard'));
        Route::get('/dashboard', Admin\DashboardController::class)->name('dashboard');

        Route::get('/pengajuan', [Admin\PengajuanController::class, 'index'])->name('pengajuan.index');
        Route::get('/pengajuan/{peminjaman}', [Admin\PengajuanController::class, 'show'])->name('pengajuan.show');
        Route::post('/pengajuan/{peminjaman}/setujui', [Admin\PengajuanController::class, 'setujui'])->name('pengajuan.setujui');
        Route::post('/pengajuan/{peminjaman}/tolak', [Admin\PengajuanController::class, 'tolak'])->name('pengajuan.tolak');
        Route::post('/pengajuan/{peminjaman}/selesai', [Admin\PengajuanController::class, 'selesai'])->name('pengajuan.selesai');
        Route::get('/pengajuan/{peminjaman}/dokumen', [Admin\PengajuanController::class, 'dokumen'])->name('pengajuan.dokumen');

        Route::get('/ruangan', [Admin\RuanganController::class, 'index'])->name('ruangan.index');
        Route::get('/ruangan/create', [Admin\RuanganController::class, 'create'])->name('ruangan.create');
        Route::post('/ruangan', [Admin\RuanganController::class, 'store'])->name('ruangan.store');
        Route::get('/ruangan/{ruangan}/edit', [Admin\RuanganController::class, 'edit'])->name('ruangan.edit');
        Route::put('/ruangan/{ruangan}', [Admin\RuanganController::class, 'update'])->name('ruangan.update');
        Route::patch('/ruangan/{ruangan}/status', [Admin\RuanganController::class, 'status'])->name('ruangan.status');

        Route::get('/feedback', [Admin\FeedbackController::class, 'index'])->name('feedback.index');
        Route::get('/feedback/{feedback}', [Admin\FeedbackController::class, 'show'])->name('feedback.show');
        Route::post('/feedback/{feedback}/balas', [Admin\FeedbackController::class, 'balas'])->name('feedback.balas');
    });
});
