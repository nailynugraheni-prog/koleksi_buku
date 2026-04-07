<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| AUTH CONTROLLERS
|--------------------------------------------------------------------------
*/

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\GoogleController;
use App\Http\Controllers\Auth\OtpController;

/*
|--------------------------------------------------------------------------
| ADMIN CONTROLLERS
|--------------------------------------------------------------------------
*/

use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\KategoriController;
use App\Http\Controllers\Admin\BarangController;
use App\Http\Controllers\Admin\BukuController;
use App\Http\Controllers\Admin\PenjualanController;
use App\Http\Controllers\Admin\WilayahController;

/*
|--------------------------------------------------------------------------
| VENDOR CONTROLLERS
|--------------------------------------------------------------------------
*/

use App\Http\Controllers\Vendor\DashboardController as VendorDashboardController;
use App\Http\Controllers\Vendor\MenuController;
use App\Http\Controllers\Vendor\OrderController;

use App\Http\Controllers\Customer\CustomerController;
use App\Http\Controllers\Customer\PaymentController;


/*
|--------------------------------------------------------------------------
| MIDDLEWARE
|--------------------------------------------------------------------------
*/

use App\Http\Middleware\IsAdmin;
use App\Http\Middleware\IsVendor;

/*
|--------------------------------------------------------------------------
| AUTH
|--------------------------------------------------------------------------
*/

Route::get('auth/google/redirect', [GoogleController::class, 'redirect'])->name('google.redirect');
Route::get('auth/google/callback', [GoogleController::class, 'callback'])->name('google.callback');

Route::get('auth/otp', [OtpController::class, 'showForm'])->name('otp.form');
Route::post('auth/otp/verify', [OtpController::class, 'verify'])->name('otp.verify');
Route::post('auth/otp/resend', [OtpController::class, 'resend'])->name('otp.resend');

/*
|--------------------------------------------------------------------------
| HOME
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return view('home');
})->name('home');

/*
|--------------------------------------------------------------------------
| LOGIN LOGOUT
|--------------------------------------------------------------------------
*/

Route::post('/login', [LoginController::class, 'login'])->name('login');

Route::post('/logout', [LoginController::class, 'logout'])
    ->name('logout')
    ->middleware('auth');

/*
|--------------------------------------------------------------------------
| ADMIN AREA
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', IsAdmin::class])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {

        /*
        |--------------------------------------
        | DASHBOARD
        |--------------------------------------
        */

        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
        Route::get('/dashboard/pdf', [DashboardController::class, 'dashboardPdf'])->name('dashboard.pdf');
        Route::get('/dashboard/certificate-pdf', [DashboardController::class, 'certificatePdf'])->name('dashboard.certificatePdf');

        /*
        |--------------------------------------
        | KATEGORI
        |--------------------------------------
        */

        Route::get('/kategori', [KategoriController::class, 'index'])->name('kategori.index');
        Route::get('/kategori/create', [KategoriController::class, 'create'])->name('kategori.create');
        Route::post('/kategori/store', [KategoriController::class, 'store'])->name('kategori.store');
        Route::get('/kategori/edit/{id}', [KategoriController::class, 'edit'])->name('kategori.edit');
        Route::put('/kategori/update/{id}', [KategoriController::class, 'update'])->name('kategori.update');
        Route::delete('/kategori/delete/{id}', [KategoriController::class, 'destroy'])->name('kategori.delete');

        /*
        |--------------------------------------
        | BUKU
        |--------------------------------------
        */

        Route::get('/buku', [BukuController::class, 'index'])->name('buku.index');
        Route::get('/buku/create', [BukuController::class, 'create'])->name('buku.create');
        Route::post('/buku/store', [BukuController::class, 'store'])->name('buku.store');
        Route::get('/buku/edit/{id}', [BukuController::class, 'edit'])->name('buku.edit');
        Route::put('/buku/update/{id}', [BukuController::class, 'update'])->name('buku.update');
        Route::delete('/buku/delete/{id}', [BukuController::class, 'destroy'])->name('buku.delete');

        Route::get('/buku/generate-kode/{idkategori}', [BukuController::class, 'generateKode'])
            ->name('buku.generateKode');

        /*
        |--------------------------------------
        | BARANG
        |--------------------------------------
        */

        Route::get('/barang', [BarangController::class, 'index'])->name('barang.index');
        Route::get('/barang/create', [BarangController::class, 'create'])->name('barang.create');
        Route::post('/barang/store', [BarangController::class, 'store'])->name('barang.store');
        Route::get('/barang/edit/{id}', [BarangController::class, 'edit'])->name('barang.edit');
        Route::put('/barang/update/{id}', [BarangController::class, 'update'])->name('barang.update');
        Route::delete('/barang/delete/{id}', [BarangController::class, 'destroy'])->name('barang.delete');

        Route::post('/barang/print-labels', [BarangController::class, 'printLabels'])->name('barang.printLabels');

        /*
        |--------------------------------------
        | PENJUALAN
        |--------------------------------------
        */

        Route::get('/penjualan', [PenjualanController::class, 'index'])->name('penjualan.index');
        Route::get('/api/barang/{kode}', [PenjualanController::class, 'findBarang'])->name('api.barang.find');
        Route::post('/api/penjualan', [PenjualanController::class, 'store'])->name('api.penjualan.store');

        /*
        |--------------------------------------
        | WILAYAH (AJAX & AXIOS)
        |--------------------------------------
        */

        Route::prefix('wilayah')->name('wilayah.')->group(function () {
            Route::get('/ajax', [WilayahController::class, 'ajax'])->name('ajax');
            Route::get('/axios', [WilayahController::class, 'axios'])->name('axios');

            Route::get('/provinsi', [WilayahController::class, 'provinsi'])->name('provinsi');
            Route::get('/kota/{provinsiId}', [WilayahController::class, 'kota'])->name('kota');
            Route::get('/kecamatan/{kotaId}', [WilayahController::class, 'kecamatan'])->name('kecamatan');
            Route::get('/kelurahan/{kecamatanId}', [WilayahController::class, 'kelurahan'])->name('kelurahan');
        });

        /*
        |--------------------------------------
        | LATIHAN VIEW
        |--------------------------------------
        */

        Route::get('/barang-biasa', function () {
            return view('admin.form_biasa');
        })->name('barang.biasa');

        Route::get('/barang-datatables', function () {
            return view('admin.form_datatable');
        })->name('barang.datatables');

        Route::get('/cities', function () {
            return view('admin.cities');
        })->name('cities');
    });

/*
|--------------------------------------------------------------------------
| VENDOR AREA
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', IsVendor::class])
    ->prefix('vendor')
    ->name('vendor.')
    ->group(function () {

        Route::get('/dashboard', [VendorDashboardController::class, 'index'])
            ->name('dashboard');

        Route::get('/menu', [MenuController::class, 'index'])
            ->name('menu.index');

        Route::get('/menu/create', [MenuController::class, 'create'])
            ->name('menu.create');

        Route::post('/menu/store', [MenuController::class, 'store'])
            ->name('menu.store');

        Route::get('/menu/edit/{id}', [MenuController::class, 'edit'])
            ->name('menu.edit');

        Route::put('/menu/update/{id}', [MenuController::class, 'update'])
            ->name('menu.update');

        Route::delete('/menu/delete/{id}', [MenuController::class, 'destroy'])
            ->name('menu.destroy');

        Route::get('/orders', [OrderController::class, 'index'])
            ->name('orders.index');
    });

    /*
|--------------------------------------------------------------------------
| CUSTOMER AREA (GUEST)
|--------------------------------------------------------------------------
*/

Route::prefix('customer')->name('customer.')->group(function () {
    Route::get('/start', [CustomerController::class, 'startGuest'])->name('start');
    Route::get('/dashboard', [CustomerController::class, 'dashboard'])->name('dashboard');

    Route::post('/cart/add', [CustomerController::class, 'addToCart'])->name('cart.add');
    Route::post('/cart/remove/{idmenu}', [CustomerController::class, 'removeFromCart'])->name('cart.remove');

    Route::post('/cart/checkout', [PaymentController::class, 'checkout'])->name('cart.checkout');

    Route::post('/midtrans/notification', [PaymentController::class, 'notification'])
        ->name('midtrans.notification');

    Route::get('/payment/success', [PaymentController::class, 'success'])
        ->name('payment.success');
});