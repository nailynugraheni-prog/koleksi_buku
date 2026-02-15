<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\KategoriController;
use App\Http\Controllers\Admin\BukuController;
use App\Http\Middleware\IsAdmin; // <- pakai class middleware langsung

/*
|--------------------------------------------------------------------------
| HOME (ADA FORM LOGIN DI HOME BLADE)
|--------------------------------------------------------------------------
*/
Route::get('/', function () {
    return view('home');
})->name('home');

/*
|--------------------------------------------------------------------------
| AUTH
|--------------------------------------------------------------------------
*/
Route::post('/login', [LoginController::class, 'login'])->name('login');

Route::post('/logout', [LoginController::class, 'logout'])
    ->name('logout')
    ->middleware('auth');

/*
|--------------------------------------------------------------------------
| ADMIN AREA (WAJIB LOGIN + ROLE ADMIN)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', IsAdmin::class])->prefix('admin')->name('admin.')->group(function () {

    // DASHBOARD
    Route::get('/dashboard', [DashboardController::class, 'index'])
        ->name('dashboard');

    /*
    |----------------------------------------------------------------------
    | KATEGORI
    |----------------------------------------------------------------------
    */
    Route::get('/kategori', [KategoriController::class, 'index'])->name('kategori.index');
    Route::get('/kategori/create', [KategoriController::class, 'create'])->name('kategori.create');
    Route::post('/kategori/store', [KategoriController::class, 'store'])->name('kategori.store');
    Route::get('/kategori/edit/{id}', [KategoriController::class, 'edit'])->name('kategori.edit');
    Route::put('/kategori/update/{id}', [KategoriController::class, 'update'])->name('kategori.update');
    Route::delete('/kategori/delete/{id}', [KategoriController::class, 'destroy'])->name('kategori.delete');

    /*
    |----------------------------------------------------------------------
    | BUKU
    |----------------------------------------------------------------------
    */
    Route::get('/buku', [BukuController::class, 'index'])->name('buku.index');
    Route::get('/buku/create', [BukuController::class, 'create'])->name('buku.create');
    Route::post('/buku/store', [BukuController::class, 'store'])->name('buku.store');
    Route::get('/buku/edit/{id}', [BukuController::class, 'edit'])->name('buku.edit');
    Route::put('/buku/update/{id}', [BukuController::class, 'update'])->name('buku.update');
    Route::delete('/buku/delete/{id}', [BukuController::class, 'destroy'])->name('buku.delete');

});
