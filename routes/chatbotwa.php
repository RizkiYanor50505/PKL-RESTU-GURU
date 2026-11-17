<?php

use App\Http\Controllers\ChatbotWa\DashboardController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Modul Chatbot WA
|--------------------------------------------------------------------------
| Prefix URL /chatbotwa, nama route chatbotwa.*, middleware role:karyawan,manajer.
| Semua rute modul ini ditulis di file ini saja.
*/

Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
