<?php

use App\Http\Controllers\PemeliharaanAlat\DashboardController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Modul Pelaporan & Pemeliharaan Alat
|--------------------------------------------------------------------------
| Prefix URL /pemeliharaanalat, nama route pemeliharaanalat.*, middleware role:karyawan,manajer.
| Semua rute modul ini ditulis di file ini saja.
*/

Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
