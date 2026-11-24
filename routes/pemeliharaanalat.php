<?php

if (file_exists(app_path('Support/helpers.php'))) {
    require_once app_path('Support/helpers.php');
}

use App\Http\Controllers\PemeliharaanAlat\AssetController;
use App\Http\Controllers\PemeliharaanAlat\BranchController;
use App\Http\Controllers\PemeliharaanAlat\DashboardController;
use App\Http\Controllers\PemeliharaanAlat\MaintenanceController;
use App\Http\Controllers\PemeliharaanAlat\ReportController;
use App\Http\Controllers\PemeliharaanAlat\UserController;
use Illuminate\Support\Facades\Route;

// Parameter ber-ID numerik
Route::pattern('asset', '[0-9]+');
Route::pattern('branch', '[0-9]+');
Route::pattern('report', '[0-9]+');
Route::pattern('user', '[0-9]+');

/*
|--------------------------------------------------------------------------
| Modul Pelaporan & Pemeliharaan Alat
|--------------------------------------------------------------------------
| Otomatis memiliki prefix URL: /pemeliharaanalat
| Otomatis memiliki prefix Nama: pemeliharaanalat.*
| Otomatis memiliki Middleware: role:karyawan,manajer
*/

Route::get('/', DashboardController::class)->name('dashboard');
Route::get('/dashboard', fn () => redirect()->route('pemeliharaanalat.dashboard'));

// Laporan Kendala
Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
Route::get('/reports/create', [ReportController::class, 'create'])->name('reports.create');
Route::post('/reports', [ReportController::class, 'store'])->name('reports.store');
Route::get('/reports/{report}', [ReportController::class, 'show'])->name('reports.show');
Route::put('/reports/{report}', [ReportController::class, 'update'])->name('reports.update');
Route::delete('/reports/{report}', [ReportController::class, 'destroy'])->name('reports.destroy');
Route::get('/reports/{report}/print', [ReportController::class, 'print'])->name('reports.print');

// Data Aset & Cabang
Route::resource('branches', BranchController::class);
Route::resource('assets', AssetController::class);

// Riwayat Pemeliharaan & Ekspor
Route::get('/maintenance', [MaintenanceController::class, 'index'])->name('maintenance.index');
Route::get('/maintenance/export', [MaintenanceController::class, 'export'])->name('maintenance.export');

// Pengelolaan Staf / User Cabang
Route::resource('users', UserController::class)->except(['show']);
Route::post('/users/{user}/reset-password', [UserController::class, 'resetPassword'])->name('users.reset-password');
Route::patch('/users/{user}/toggle-status', [UserController::class, 'toggleStatus'])->name('users.toggle-status');
