<?php

use App\Http\Controllers\PenyewaanReklame\DashboardController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Modul Penyewaan Reklame
|--------------------------------------------------------------------------
| Prefix URL /penyewaanreklame, nama route penyewaanreklame.*, middleware role:karyawan,manajer.
| Semua rute modul ini ditulis di file ini saja.
*/

Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
