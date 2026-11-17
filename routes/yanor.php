<?php

use App\Http\Controllers\Yanor\YanorController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Routes Khusus Modul Yanor
|--------------------------------------------------------------------------
| Prefix URL  : /yanor/...
| Route Name  : yanor....
| Middleware  : role:karyawan,manajer (dapat diubah sesuai kebutuhan)
*/

Route::get('/', [YanorController::class, 'index'])->name('index');

// Yanor dapat menambahkan route untuk fiturnya di bawah ini:
// Route::get('/fitur-1', [YanorController::class, 'fiturSatu'])->name('fitur1');
// Route::post('/simpan', [YanorController::class, 'store'])->name('store');

