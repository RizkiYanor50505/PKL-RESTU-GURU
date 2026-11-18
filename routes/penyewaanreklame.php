<?php

use App\Http\Controllers\PenyewaanReklame\DashboardController;
use App\Http\Controllers\PenyewaanReklame\KlienController;
use App\Http\Controllers\PenyewaanReklame\KontrakSewaController;
use App\Http\Controllers\PenyewaanReklame\LaporanController;
use App\Http\Controllers\PenyewaanReklame\PembayaranController;
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

Route::get('/titik-reklame-import', [TitikReklameController::class, 'importForm'])->name('titik-reklame.import');
Route::post('/titik-reklame-import', [TitikReklameController::class, 'importStore'])->name('titik-reklame.import.store');
Route::get('/titik-reklame-template', [TitikReklameController::class, 'downloadTemplate'])->name('titik-reklame.template');
Route::resource('titik-reklame', TitikReklameController::class);

Route::resource('klien', KlienController::class);

Route::resource('kontrak', KontrakSewaController::class)->except(['edit', 'update']);
Route::patch('kontrak/{kontrak}/acc', [KontrakSewaController::class, 'tandaiAcc'])->name('kontrak.acc');
Route::patch('kontrak/{kontrak}/pasang', [KontrakSewaController::class, 'aktivasiPemasangan'])->name('kontrak.pasang');
Route::patch('kontrak/{kontrak}/perpanjangan', [KontrakSewaController::class, 'updatePerpanjangan'])->name('kontrak.perpanjangan');
Route::patch('kontrak/{kontrak}/perlu-dicopot', [KontrakSewaController::class, 'tandaiPerluDicopot'])->name('kontrak.perlu-dicopot');
Route::patch('kontrak/{kontrak}/turunkan-materi', [KontrakSewaController::class, 'konfirmasiTurunMateri'])->name('kontrak.turunkan-materi');
Route::patch('kontrak/{kontrak}/batal', [KontrakSewaController::class, 'batalkan'])->name('kontrak.batal');

Route::post('kontrak/{kontrak}/pembayaran', [PembayaranController::class, 'store'])->name('pembayaran.store');
Route::delete('pembayaran/{pembayaran}', [PembayaranController::class, 'destroy'])->name('pembayaran.destroy');

Route::get('/laporan', [LaporanController::class, 'index'])->name('laporan.index');
Route::get('/laporan/export-csv', [LaporanController::class, 'exportCsv'])->name('laporan.export-csv');
Route::get('/laporan/cetak', [LaporanController::class, 'cetak'])->name('laporan.cetak');
