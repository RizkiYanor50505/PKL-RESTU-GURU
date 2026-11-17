<?php

use App\Http\Controllers\Absensi\AttendanceController;
use App\Http\Controllers\Absensi\LeaveController;
use App\Http\Controllers\AuthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Modul ABSENSI + Rute Bersama (Auth)
|--------------------------------------------------------------------------
| File ini juga memuat rute bersama untuk semua modul: /, login, dan logout.
| Modul lain (chatbotwa, pemeliharaanalat, penyewaanreklame) didaftarkan di bootstrap/app.php.
*/

// ---------------------------------------------------------------------
// Rute bersama semua modul
// ---------------------------------------------------------------------
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

Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.submit');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// ---------------------------------------------------------------------
// Modul ABSENSI: portal karyawan
// ---------------------------------------------------------------------
Route::prefix('karyawan')->name('karyawan.')->middleware('role:karyawan')->group(function () {
    Route::get('/dashboard', [AttendanceController::class, 'dashboard'])->name('dashboard');

    Route::get('/scan', function () {
        return view('absensi.karyawan.scan');
    })->name('scan');

    Route::post('/scan/verify', [AttendanceController::class, 'verify'])->name('scan.verify');

    Route::get('/izin', [LeaveController::class, 'index'])->name('izin');
    Route::post('/izin', [LeaveController::class, 'store'])->name('izin.store');
    Route::get('/izin/{izin}/bukti', [LeaveController::class, 'proof'])->name('izin.bukti');

    Route::get('/riwayat', [AttendanceController::class, 'history'])->name('riwayat');
});

// ---------------------------------------------------------------------
// Modul ABSENSI: portal manajer / HRD
// ---------------------------------------------------------------------
Route::prefix('admin')->name('admin.')->middleware('role:manajer')->group(function () {
    Route::get('/dashboard', [AttendanceController::class, 'adminDashboard'])->name('dashboard');
    Route::post('/lokasi', [AttendanceController::class, 'updateLokasi'])->name('lokasi.update');
    Route::post('/izin/{izin}/keputusan', [LeaveController::class, 'decide'])->name('izin.decide');
    Route::get('/izin/{izin}/bukti', [LeaveController::class, 'proof'])->name('izin.bukti');
});
