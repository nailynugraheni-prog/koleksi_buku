<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\KategoriController;
use App\Http\Controllers\Admin\BukuController;
use App\Http\Middleware\IsAdmin;
use App\Http\Controllers\Auth\GoogleController;
use App\Http\Controllers\Auth\OtpController;

Route::get('auth/google/redirect', [GoogleController::class, 'redirect'])->name('google.redirect');
Route::get('auth/google/callback', [GoogleController::class, 'callback'])->name('google.callback');

Route::get('auth/otp', [OtpController::class, 'showForm'])->name('otp.form');
Route::post('auth/otp/verify', [OtpController::class, 'verify'])->name('otp.verify');
Route::post('auth/otp/resend', [OtpController::class, 'resend'])->name('otp.resend');

Route::get('/', function () {
    return view('home');
})->name('home');

Route::post('/login', [LoginController::class, 'login'])->name('login');

Route::post('/logout', [LoginController::class, 'logout'])
    ->name('logout')
    ->middleware('auth');

Route::middleware(['auth', IsAdmin::class])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {

        // DASHBOARD
        Route::get('/dashboard', [DashboardController::class, 'index'])
            ->name('dashboard');

        // Cetak / stream PDF Dashboard (A4 portrait)
        Route::get('/dashboard/pdf', [DashboardController::class, 'dashboardPdf'])
            ->name('dashboard.pdf');

        // Cetak Sertifikat PDF untuk user yang login (A4 landscape)
        Route::get('/dashboard/certificate-pdf', [DashboardController::class, 'certificatePdf'])
            ->name('dashboard.certificatePdf');

        // KATEGORI (CRUD)
        Route::get('/kategori', [KategoriController::class, 'index'])->name('kategori.index');
        Route::get('/kategori/create', [KategoriController::class, 'create'])->name('kategori.create');
        Route::post('/kategori/store', [KategoriController::class, 'store'])->name('kategori.store');
        Route::get('/kategori/edit/{id}', [KategoriController::class, 'edit'])->name('kategori.edit');
        Route::put('/kategori/update/{id}', [KategoriController::class, 'update'])->name('kategori.update');
        Route::delete('/kategori/delete/{id}', [KategoriController::class, 'destroy'])->name('kategori.delete');

        // BUKU (CRUD) — sudah dirapikan, tidak duplikat
        Route::get('/buku', [BukuController::class, 'index'])->name('buku.index');
        Route::get('/buku/create', [BukuController::class, 'create'])->name('buku.create');
        Route::post('/buku/store', [BukuController::class, 'store'])->name('buku.store');
        Route::get('/buku/edit/{id}', [BukuController::class, 'edit'])->name('buku.edit');
        Route::put('/buku/update/{id}', [BukuController::class, 'update'])->name('buku.update');
        Route::delete('/buku/delete/{id}', [BukuController::class, 'destroy'])->name('buku.delete');

        // generate kode buku berdasarkan kategori
        Route::get('/buku/generate-kode/{idkategori}', [BukuController::class, 'generateKode'])
            ->name('buku.generateKode');
});