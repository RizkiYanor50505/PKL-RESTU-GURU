<?php

use App\Http\Controllers\Aron\AronController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Routes Khusus Modul Aron
|--------------------------------------------------------------------------
| Prefix URL  : /aron/...
| Route Name  : aron....
| Middleware  : role:karyawan,manajer (dapat diubah sesuai kebutuhan)
*/

Route::get('/', [AronController::class, 'index'])->name('index');

// Aron dapat menambahkan route untuk fiturnya di bawah ini:
// Route::get('/fitur-1', [AronController::class, 'fiturSatu'])->name('fitur1');
// Route::post('/simpan', [AronController::class, 'store'])->name('store');

