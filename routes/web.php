<?php

use App\Http\Controllers\AcaraController;
use App\Http\Controllers\BerandaController;
use App\Http\Controllers\ForumController;
use App\Http\Controllers\LapakController;
use App\Http\Controllers\NotifikasiController;
use App\Http\Controllers\PengumumanController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\WargaController;
use Illuminate\Support\Facades\Route;

Route::get('/', fn () => redirect()->route('dashboard'));

Route::middleware('auth')->group(function () {
    Route::view('/menunggu', 'menunggu')->name('menunggu');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::middleware(['auth', 'approved'])->group(function () {

    Route::get('/dashboard', [BerandaController::class, 'index'])->name('dashboard');
    Route::get('/notifikasi', [NotifikasiController::class, 'index'])->name('notifikasi.index');
    Route::patch('/notifikasi/baca-semua', [NotifikasiController::class, 'bacaSemua'])->name('notifikasi.baca-semua');
    Route::patch('/notifikasi/{notification}', [NotifikasiController::class, 'baca'])->name('notifikasi.baca');

    // ===== KHUSUS ADMIN (didefinisikan lebih dulu) =====
    Route::middleware('admin')->group(function () {
        Route::get('/admin/warga', [WargaController::class, 'index'])->name('warga.index');
        Route::patch('/admin/warga/{user}', [WargaController::class, 'update'])->name('warga.update');

        Route::resource('pengumuman', PengumumanController::class)
            ->except(['index', 'show'])->parameters(['pengumuman' => 'pengumuman']);
    });

    // ===== SEMUA WARGA TERVERIFIKASI =====
    Route::resource('pengumuman', PengumumanController::class)
        ->only(['index'])->parameters(['pengumuman' => 'pengumuman']);

    Route::resource('acara', AcaraController::class)->parameters(['acara' => 'acara']);
    Route::post('/acara/{acara}/rsvp', [AcaraController::class, 'rsvp'])->name('acara.rsvp');

    Route::resource('forum', ForumController::class);
    Route::post('/forum/{forum}/komentar', [ForumController::class, 'komentar'])->name('forum.komentar');
    Route::delete('/komentar/{komentar}', [ForumController::class, 'hapusKomentar'])->name('komentar.destroy');

    Route::resource('lapak', LapakController::class);
    Route::patch('/lapak/{lapak}/stok', [LapakController::class, 'stok'])->name('lapak.stok');
});

require __DIR__.'/auth.php';
