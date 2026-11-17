<?php

use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\LeaveController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes - Sistem Absensi Karyawan & Manajer
|--------------------------------------------------------------------------
*/

Route::get('/', function (Request $request) {
    $role = $request->session()->get('auth_role');

    if ($role === 'manajer') {
        return redirect()->route('admin.dashboard');
    }

    if ($role === 'karyawan') {
        return redirect()->route('karyawan.dashboard');
    }

    return redirect()->route('login');
});

// Autentikasi & Sesi Peran
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.submit');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Portal Pusat Modul Terpadu (Akses untuk semua role setelah login)
Route::get('/portal', function () {
    return view('portal');
})->middleware('role:karyawan,manajer')->name('portal');

// ==========================================
// WADAH MODUL PROJEK TEMAN-TEMAN:
// ==========================================

// 1. Modul Aron
Route::prefix('aron')->name('aron.')->middleware('role:karyawan,manajer')->group(base_path('routes/aron.php'));

// 2. Modul Yanor
Route::prefix('yanor')->name('yanor.')->middleware('role:karyawan,manajer')->group(base_path('routes/yanor.php'));

// 3. Modul Adi
Route::prefix('adi')->name('adi.')->middleware('role:karyawan,manajer')->group(base_path('routes/adi.php'));

// Portal Khusus Karyawan (Hanya dapat diakses oleh role:karyawan)
Route::prefix('karyawan')->name('karyawan.')->middleware('role:karyawan')->group(function () {
    Route::get('/dashboard', [AttendanceController::class, 'dashboard'])->name('dashboard');

    Route::get('/scan', function () {
        return view('karyawan.scan');
    })->name('scan');

    Route::post('/scan/verify', [AttendanceController::class, 'verify'])->name('scan.verify');

    Route::get('/izin', [LeaveController::class, 'index'])->name('izin');
    Route::post('/izin', [LeaveController::class, 'store'])->name('izin.store');
    Route::get('/izin/{izin}/bukti', [LeaveController::class, 'proof'])->name('izin.bukti');

    Route::get('/riwayat', [AttendanceController::class, 'history'])->name('riwayat');
});

// Portal Khusus Manajer / HRD (Hanya dapat diakses oleh role:manajer)
Route::prefix('admin')->name('admin.')->middleware('role:manajer')->group(function () {
    Route::get('/dashboard', [AttendanceController::class, 'adminDashboard'])->name('dashboard');
    Route::post('/lokasi', [AttendanceController::class, 'updateLokasi'])->name('lokasi.update');
    Route::post('/izin/{izin}/keputusan', [LeaveController::class, 'decide'])->name('izin.decide');
    Route::get('/izin/{izin}/bukti', [LeaveController::class, 'proof'])->name('izin.bukti');
});
