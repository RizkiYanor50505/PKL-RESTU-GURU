<?php

use App\Http\Controllers\PenyewaanReklame\DashboardController;
use App\Http\Controllers\PenyewaanReklame\TitikReklameController;
use App\Http\Middleware\EnsureUserRole;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Modul Penyewaan Reklame
|--------------------------------------------------------------------------
| Prefix URL /penyewaanreklame, nama route penyewaanreklame.*, middleware role:karyawan,manajer.
| Semua rute modul ini ditulis di file ini saja.
*/

// Rute Public View (Tanpa Login - dibagikan ke calon klien)
Route::get('/katalog', [TitikReklameController::class, 'katalog'])
    ->name('katalog')
    ->withoutMiddleware([EnsureUserRole::class]);

Route::get('/katalog/{titikReklame}', [TitikReklameController::class, 'katalogShow'])
    ->name('katalog.show')
    ->withoutMiddleware([EnsureUserRole::class]);

// Rute Admin Internal (Otomatis dilindungi middleware role:karyawan,manajer dari bootstrap/app.php)
Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
Route::resource('titik-reklame', TitikReklameController::class);
