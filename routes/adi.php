<?php

use App\Http\Controllers\Adi\AdiController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Routes Khusus Modul Adi
|--------------------------------------------------------------------------
| Prefix URL  : /adi/...
| Route Name  : adi....
| Middleware  : role:karyawan,manajer (dapat diubah sesuai kebutuhan)
*/

Route::get('/', [AdiController::class, 'index'])->name('index');

// Adi dapat menambahkan route untuk fiturnya di bawah ini:
// Route::get('/fitur-1', [AdiController::class, 'fiturSatu'])->name('fitur1');
// Route::post('/simpan', [AdiController::class, 'store'])->name('store');

