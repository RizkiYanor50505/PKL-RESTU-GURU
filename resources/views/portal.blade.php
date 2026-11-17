@extends('layouts.app')

@section('title', 'Portal Sistem Terpadu PKL · hadir.in')

@section('content')
<div class="min-h-screen bg-slate-50/50 pb-20 pt-8">
    <div class="mx-auto max-w-5xl px-4 sm:px-6">
        
        <!-- Header Sambutan -->
        <div class="mb-8 rounded-2xl border border-slate-200/80 bg-white p-6 shadow-sm sm:p-8">
            <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
                <div>
                    <span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-50 px-3 py-1 text-xs font-semibold text-emerald-700 ring-1 ring-inset ring-emerald-700/10">
                        <i class="ph ph-check-circle text-sm"></i> Sistem Terpadu Aktif
                    </span>
                    <h1 class="mt-2 text-2xl font-bold tracking-tight text-slate-900 sm:text-3xl">
                        Portal Layanan & Modul Terpadu
                    </h1>
                    <p class="mt-1 text-sm text-slate-500">
                        Selamat datang, <strong class="text-slate-800">{{ session('auth_user.name', 'Pengguna') }}</strong>. Silakan pilih modul sistem yang ingin diakses.
                    </p>
                </div>
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="inline-flex items-center gap-2 rounded-xl border border-rose-200 bg-rose-50/50 px-4 py-2 text-sm font-semibold text-rose-700 transition hover:bg-rose-100">
                        <i class="ph ph-sign-out text-base"></i> Keluar
                    </button>
                </form>
            </div>
        </div>

        <!-- Grid 4 Modul Terpadu -->
        <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
            
            <!-- 1. Modul Absensi (Naufal / Nafmi) -->
            <div class="group relative flex flex-col justify-between rounded-2xl border border-slate-200 bg-white p-6 shadow-sm transition-all hover:border-emerald-500 hover:shadow-md">
                <div>
                    <div class="flex items-center justify-between">
                        <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-emerald-50 text-emerald-600 transition group-hover:bg-emerald-500 group-hover:text-white">
                            <i class="ph ph-fingerprint-simple text-2xl"></i>
                        </div>
                        <span class="rounded-full bg-slate-100 px-2.5 py-0.5 text-xs font-medium text-slate-600">
                            Modul 1 (Naufal)
                        </span>
                    </div>
                    <h3 class="mt-4 text-lg font-bold text-slate-900 group-hover:text-emerald-600 transition">
                        Presensi & Perizinan PKL
                    </h3>
                    <p class="mt-1 text-sm text-slate-500 leading-relaxed">
                        Sistem absensi GPS radius, verifikasi biometrik/kamera, pengajuan izin cuti/sakit, dan dashboard manajer.
                    </p>
                </div>
                <div class="mt-6 pt-4 border-t border-slate-100 flex items-center justify-between">
                    <span class="text-xs font-semibold text-emerald-600">Status: Aktif & Siap</span>
                    <a href="{{ session('auth_role') === 'manajer' ? route('admin.dashboard') : route('karyawan.dashboard') }}" class="inline-flex items-center gap-1.5 rounded-lg bg-emerald-600 px-3.5 py-1.5 text-xs font-semibold text-white shadow-sm hover:bg-emerald-500">
                        Buka Modul <i class="ph ph-arrow-right"></i>
                    </a>
                </div>
            </div>

            <!-- 2. Modul Aron -->
            <div class="group relative flex flex-col justify-between rounded-2xl border border-slate-200 bg-white p-6 shadow-sm transition-all hover:border-blue-500 hover:shadow-md">
                <div>
                    <div class="flex items-center justify-between">
                        <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-blue-50 text-blue-600 transition group-hover:bg-blue-500 group-hover:text-white">
                            <i class="ph ph-stack text-2xl"></i>
                        </div>
                        <span class="rounded-full bg-slate-100 px-2.5 py-0.5 text-xs font-medium text-slate-600">
                            Modul 2 (Aron)
                        </span>
                    </div>
                    <h3 class="mt-4 text-lg font-bold text-slate-900 group-hover:text-blue-600 transition">
                        Modul & Layanan Aron
                    </h3>
                    <p class="mt-1 text-sm text-slate-500 leading-relaxed">
                        Wadah sistem untuk fitur yang dikembangkan Aron (misal: inventaris, pendataan, atau operasional kantor).
                    </p>
                </div>
                <div class="mt-6 pt-4 border-t border-slate-100 flex items-center justify-between">
                    <span class="text-xs font-semibold text-blue-600">Wadah Terintegrasi</span>
                    <a href="{{ route('aron.index') }}" class="inline-flex items-center gap-1.5 rounded-lg bg-blue-600 px-3.5 py-1.5 text-xs font-semibold text-white shadow-sm hover:bg-blue-500">
                        Buka Modul <i class="ph ph-arrow-right"></i>
                    </a>
                </div>
            </div>

            <!-- 3. Modul Yanor -->
            <div class="group relative flex flex-col justify-between rounded-2xl border border-slate-200 bg-white p-6 shadow-sm transition-all hover:border-purple-500 hover:shadow-md">
                <div>
                    <div class="flex items-center justify-between">
                        <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-purple-50 text-purple-600 transition group-hover:bg-purple-500 group-hover:text-white">
                            <i class="ph ph-chart-polar text-2xl"></i>
                        </div>
                        <span class="rounded-full bg-slate-100 px-2.5 py-0.5 text-xs font-medium text-slate-600">
                            Modul 3 (Yanor)
                        </span>
                    </div>
                    <h3 class="mt-4 text-lg font-bold text-slate-900 group-hover:text-purple-600 transition">
                        Modul & Layanan Yanor
                    </h3>
                    <p class="mt-1 text-sm text-slate-500 leading-relaxed">
                        Wadah sistem untuk fitur yang dikembangkan Yanor (misal: distribusi, logistik, atau pelaporan berkala).
                    </p>
                </div>
                <div class="mt-6 pt-4 border-t border-slate-100 flex items-center justify-between">
                    <span class="text-xs font-semibold text-purple-600">Wadah Terintegrasi</span>
                    <a href="{{ route('yanor.index') }}" class="inline-flex items-center gap-1.5 rounded-lg bg-purple-600 px-3.5 py-1.5 text-xs font-semibold text-white shadow-sm hover:bg-purple-500">
                        Buka Modul <i class="ph ph-arrow-right"></i>
                    </a>
                </div>
            </div>

            <!-- 4. Modul Adi -->
            <div class="group relative flex flex-col justify-between rounded-2xl border border-slate-200 bg-white p-6 shadow-sm transition-all hover:border-rose-500 hover:shadow-md">
                <div>
                    <div class="flex items-center justify-between">
                        <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-rose-50 text-rose-600 transition group-hover:bg-rose-500 group-hover:text-white">
                            <i class="ph ph-calculator text-2xl"></i>
                        </div>
                        <span class="rounded-full bg-slate-100 px-2.5 py-0.5 text-xs font-medium text-slate-600">
                            Modul 4 (Adi)
                        </span>
                    </div>
                    <h3 class="mt-4 text-lg font-bold text-slate-900 group-hover:text-rose-600 transition">
                        Modul & Layanan Adi
                    </h3>
                    <p class="mt-1 text-sm text-slate-500 leading-relaxed">
                        Wadah sistem untuk fitur yang dikembangkan Adi (misal: SPK, penilaian kinerja, atau modul khusus).
                    </p>
                </div>
                <div class="mt-6 pt-4 border-t border-slate-100 flex items-center justify-between">
                    <span class="text-xs font-semibold text-rose-600">Wadah Terintegrasi</span>
                    <a href="{{ route('adi.index') }}" class="inline-flex items-center gap-1.5 rounded-lg bg-rose-600 px-3.5 py-1.5 text-xs font-semibold text-white shadow-sm hover:bg-rose-500">
                        Buka Modul <i class="ph ph-arrow-right"></i>
                    </a>
                </div>
            </div>

        </div>

    </div>
</div>
@endsection

